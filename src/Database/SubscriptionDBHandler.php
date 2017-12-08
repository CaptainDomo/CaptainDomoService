<?php
namespace cds\Database;

use cds\Model\Member;
use cds\Model\Subscription;
use cds\Model\SubscriptionRequest;
use cds\Model\SubscriptionSearchResult;
use cds\Model\SubscriptionSearchResultCode;

class SubscriptionDBHandler extends DBHandler
{

    const SEARCH_EXACT_MATCH_QUERY = "SELECT id, email, number, firstname, lastname FROM subscription WHERE upper(email)=? AND number=? AND upper(firstname)=? AND upper(lastname)=?";

    const SEARCH_OTHER_EMAIL_QUERY = "SELECT id, email, number, firstname, lastname FROM subscription WHERE number=? AND upper(firstname)=? AND upper(lastname)=?";

    const SEARCH_OTHER_MEMBER_QUERY = "SELECT id, email, number, firstname, lastname  FROM subscription WHERE upper(email)=?";

    const ADD_SUBSCRIPTION_QUERY = "INSERT INTO subscription (email, number, firstname, lastname) VALUES (?, ?, ?, ?)";

    const GET_ALL_SUBSCRIPTIONS = "SELECT s.id AS s_id, s.email AS s_email, s.number AS s_number, s.firstname AS s_firstname, s.lastname AS s_lastname, m.number AS m_number, m.firstname AS m_firstname, m.lastname AS m_lastname FROM subscription s LEFT OUTER JOIN member m ON s.number = m.number ORDER BY s.number";

    const BY_ID_QUERY = "SELECT id, email, number, firstname, lastname  FROM subscription WHERE id=?";

    const DELETE_SUBSCRIPTION_QUERY = "DELETE FROM subscription WHERE id=?";

    /**
     * @param SubscriptionRequest $subscriptionRequest
     * @param Member $member
     * @return SubscriptionSearchResult|null
     */
    function add(SubscriptionRequest $subscriptionRequest, $member)
    {
        $stmt = $this->getMysqli()->prepare(SubscriptionDBHandler::ADD_SUBSCRIPTION_QUERY);

        if ($stmt == false) {
            return null;
        }

        $email = $subscriptionRequest->getEmail();
        $number = $subscriptionRequest->getNumber();
        $firstname = $subscriptionRequest->getFirstname();
        $lastname = $subscriptionRequest->getLastname();

        $stmt->bind_param('siss', $email, $number, $firstname, $lastname);

        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }
        $stmt->close();


        if ($member == null) {
            $memberDBHandler = new MemberDBHandler();
            $memberId = $memberDBHandler->exactMatch($number, $firstname, $lastname);
            $member = $memberDBHandler->getById($memberId);
        }

        // Provide all subscriptions(with different emails) for that member
        $subscriptionSearchResult = $this->searchInDB(SubscriptionDBHandler::SEARCH_OTHER_EMAIL_QUERY, $email, $member);
        if ($subscriptionSearchResult != null) {
            $subscriptionSearchResult
                ->setResultCode(new SubscriptionSearchResultCode(SubscriptionSearchResultCode::SUBSCRIPTION_ADDED));
        }
        return $subscriptionSearchResult;
    }

    /**
     * @return array|null
     */
    function getSubscriptionList()
    {
        $stmt = $this->getMysqli()->prepare(SubscriptionDBHandler::GET_ALL_SUBSCRIPTIONS);

        if (!$stmt) {
            error_log("Error while preparing statement to retrieve all subscirptions");
            return null;
        }

        if (!$stmt->execute()) {
            error_log("Error while executing statement to retrieve all subscriptions");
            $stmt->close;
            return null;
        }

        $result = $stmt->get_result();

        $subscriptions = array();
        while ($row = $result->fetch_assoc()) {
            $subscription = new Subscription();

            if (isset($row['m_number']) && $row['m_number'] != null) {
                $member = new Member();
                $member->setNumber($row['m_number']);
                $member->setFirstname($row['m_firstname']);
                $member->setLastname($row['m_lastname']);
                $subscription->setMember($member);
            }

            $subscription->setId($row['s_id']);
            $subscription->setNumber($row['s_number']);
            $subscription->setFirstname($row['s_firstname']);
            $subscription->setLastname($row['s_lastname']);
            $subscription->setEmailaddress($row['s_email']);
            $subscriptions[] = $subscription;
        }

        $result->free();

        return $subscriptions;
    }

    /**
     * @param $subscriptionId
     * @return Subscription|null
     */
    function getById($subscriptionId)
    {
        $stmt = $this->getMysqli()->prepare(SubscriptionDBHandler::BY_ID_QUERY);

        if ($stmt == false) {
            return null;
        }

        $stmt->bind_param('i', $subscriptionId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        $row = $result->fetch_assoc();

        if ($row['number'] == null) {
            return null;
        }

        $subscription = new Subscription();
        $subscription->setId($row['id']);
        $subscription->setEmailaddress($row['email']);
        $subscription->setNumber($row['number']);
        $subscription->setFirstname($row['firstname']);
        $subscription->setLastname($row['lastname']);

        return $subscription;
    }

    /**
     * @param $email
     * @param Member $member
     * @return SubscriptionSearchResult
     */
    function searchSubscription($email, Member $member)
    {

        // Search for exact match
        $subscriptionSearchResult = $this->searchInDB(SubscriptionDBHandler::SEARCH_EXACT_MATCH_QUERY, $email, $member);
        if ($subscriptionSearchResult != null) {
            return $subscriptionSearchResult;
        }

        // Search for subscription with member data but other email
        $subscriptionSearchResult = $this->searchInDB(SubscriptionDBHandler::SEARCH_OTHER_EMAIL_QUERY, $email, $member);
        if ($subscriptionSearchResult != null) {
            return $subscriptionSearchResult;
        }

        // Search for subscription with email but other member data
        $subscriptionSearchResult = $this->searchInDB(SubscriptionDBHandler::SEARCH_OTHER_MEMBER_QUERY, $email, $member);
        if ($subscriptionSearchResult != null) {
            return $subscriptionSearchResult;
        }

        // No subscription found
        $subscriptionSearchResult = new SubscriptionSearchResult();
        $subscriptionSearchResult->setResultCode(
            new SubscriptionSearchResultCode(SubscriptionSearchResultCode::SUBSCRIPTION_NOT_EXISTENT)
        );

        return $subscriptionSearchResult;
    }

    /**
     * @param $query
     * @param $email
     * @param Member $member
     * @return SubscriptionSearchResult|null
     */
    function searchInDB($query, $email, $member)
    {
        $upper_email = strtoupper($email);
        $upper_firstname = strtoupper($member->getFirstname());
        $upper_lastname = strtoupper($member->getLastname());
        $number = $member->getNumber();

        $stmt = $this->getMysqli()->prepare($query);

        if ($stmt == false) {
            return null;
        }

        if ($query == SubscriptionDBHandler::SEARCH_EXACT_MATCH_QUERY) {
            $stmt->bind_param('siss', $upper_email, $number, $upper_firstname, $upper_lastname);
        } elseif ($query == SubscriptionDBHandler::SEARCH_OTHER_EMAIL_QUERY) {
            $stmt->bind_param('iss', $number, $upper_firstname, $upper_lastname);
        } elseif ($query == SubscriptionDBHandler::SEARCH_OTHER_MEMBER_QUERY) {
            $stmt->bind_param('s', $upper_email);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $subscriptionSearchResult = new SubscriptionSearchResult();
        $subscriptions = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_row()) {
                $subscription = new Subscription();
                $subscription->setId($row[0]);
                $subscription->setEmailaddress($row[1]);
                $subscription->setNumber($row[2]);
                $subscription->setFirstname($row[3]);
                $subscription->setLastname($row[4]);

                $subscriptions[] = $subscription;
            }

            $subscriptionSearchResult->setSubscriptions($subscriptions);

            if ($query == SubscriptionDBHandler::SEARCH_EXACT_MATCH_QUERY) {
                $subscriptionSearchResult->setResultCode(
                    new SubscriptionSearchResultCode(SubscriptionSearchResultCode::SUBSCRIPTION_ALREADY_EXISTS)
                );
            } elseif ($query == SubscriptionDBHandler::SEARCH_OTHER_EMAIL_QUERY) {
                $subscriptionSearchResult->setResultCode(
                    new SubscriptionSearchResultCode(SubscriptionSearchResultCode::SUBSCRIPTION_WITH_OTHER_EMAIL_EXISTS)
                );
            } elseif ($query == SubscriptionDBHandler::SEARCH_OTHER_MEMBER_QUERY) {
                $subscriptionSearchResult->setResultCode(
                    new SubscriptionSearchResultCode(SubscriptionSearchResultCode::SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS)
                );
            }
        } else {
            $subscriptionSearchResult = null;
        }

        return $subscriptionSearchResult;
    }

    /**
     * @param Subscription $subscription
     * @return bool|null
     */
    public function delete(Subscription $subscription)
    {
        $stmt = $this->getMysqli()->prepare(SubscriptionDBHandler::DELETE_SUBSCRIPTION_QUERY);

        if (!$stmt)
            return null;

        $subscriptionId = $subscription->getId();

        $stmt->bind_param('i', $subscriptionId);

        if (!$stmt->execute()) {
            error_log("Could not delete subscription with id " . $subscriptionId . " from DB!");
            $stmt->close();
            return false;
        } else {
            $stmt->close();
            return true;
        }
    }
}
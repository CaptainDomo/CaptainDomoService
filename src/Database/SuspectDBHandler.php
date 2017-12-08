<?php
namespace cds\Database;

use cds\Model\SubscriptionRequest;
use cds\Model\Suspect;

class SuspectDBHandler extends DBHandler
{

    const ADD_SUSPECT_QUERY = "INSERT INTO suspect (email, number, firstname, lastname, suspectreason) VALUES (?, ?, ?, ?, ?)";

    const GET_SUSPECT_BY_ID_QUERY = "SELECT id, email, number, firstname, lastname, suspectreason FROM suspect WHERE id = ?";

    const GET_ALL_SUSPECTS_QUERY = "SELECT id, email, number, firstname, lastname, suspectreason FROM suspect";

    const DELETE_SUSPECT = "DELETE FROM suspect WHERE id = ?";

    /**
     *
     * @param Suspect $suspect
     * @return Suspect $suspect with id set
     */
    function add(Suspect $suspect)
    {
        $stmt = $this->getMysqli()->prepare(SuspectDBHandler::ADD_SUSPECT_QUERY);

        if (!$stmt)
            return null;

        $email = $suspect->getSubscriptionRequest()->getEmail();
        $number = $suspect->getSubscriptionRequest()->getNumber();
        $firstname = $suspect->getSubscriptionRequest()->getFirstname();
        $lastname = $suspect->getSubscriptionRequest()->getLastname();
        $suspectreason = $suspect->getSuspectReason();

        $stmt->bind_param('sisss', $email, $number, $firstname, $lastname, $suspectreason);
        if (!$stmt->execute()) {
            error_log("Could not add suspect into DB.");
            $stmt->close();
            return null;
        } else {
            error_log("Added suspect to DB.");
            $suspect->setId($this->getMysqli()->insert_id);
            $stmt->close();
        }

        return $suspect;
    }

    function delete($suspectId)
    {
        $stmt = $this->getMysqli()->prepare(SuspectDBHandler::DELETE_SUSPECT);

        if (!$stmt)
            return null;

        $stmt->bind_param('i', $suspectId);

        if (!$stmt->execute()) {
            error_log("Could not delete suspect with id " . $suspectId . " from DB!");
            $stmt->close();
            return false;
        } else {
            $stmt->close();
            return true;
        }
    }

    function getAllSuspects()
    {
        $stmt = $this->getMysqli()->prepare(SuspectDBHandler::GET_ALL_SUSPECTS_QUERY);

        if (!$stmt) {
            error_log("Could not create SQL statement for query: " . SuspectDBHandler::GET_ALL_SUSPECTS_QUERY);
            return null;
        }

        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $result = $stmt->get_result();
        $stmt->close();

        $suspects = array();
        while ($row = $result->fetch_assoc()) {
            $suspect = new Suspect();
            $suspect->setId($row['id']);
            $suspect->setSuspectReason($row['suspectreason']);
            $subscriptionRequest = new SubscriptionRequest($row['number'], $row['firstname'], $row['lastname'], $row['email']);
            $suspect->setSubscriptionRequest($subscriptionRequest);

            $suspects[] = $suspect;
        }

        $result->free();

        return $suspects;
    }

    /**
     *
     * @param int $suspectId
     * @return Suspect $suspect
     */
    function get($suspectId)
    {
        $stmt = $this->getMysqli()->prepare(SuspectDBHandler::GET_SUSPECT_BY_ID_QUERY);
        $stmt->bind_param('i', $suspectId);

        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        } else {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            if ($row == null)
                return null;

            $suspect = new Suspect();
            $suspect->setId($row['id']);
            $suspect->setSuspectReason($row['suspectreason']);
            $subscriptionRequest = new SubscriptionRequest($row['number'], $row['firstname'], $row['lastname'], $row['email']);
            $suspect->setSubscriptionRequest($subscriptionRequest);

            return $suspect;
        }
    }
}

?>
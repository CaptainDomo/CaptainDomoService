<?php
namespace cds\Database;

use cds\Model\Member;
use cds\Model\PublishPreCheckedMembersResultCode;

class MemberDBHandler extends DBHandler
{

    const ADD_PRECHECK_MEMBER_QUERY = "INSERT INTO precheck_member (precheck_id, number, firstname, lastname) VALUES (?, ?, ?, ?)";

    const ADD_MEMBER_QUERY = "INSERT INTO member (number, firstname, lastname) VALUES (?, ?, ?)";

    const GET_PRECHECK_MEMBER_LIST_QUERY = "SELECT number, firstname, lastname FROM precheck_member WHERE precheck_id = ?";

    const GET_ALL_MEMBERS_QUERY = "SELECT id, number, firstname, lastname FROM member";

    const EMPTY_MEMBER_TABLE_QUERY = "DELETE FROM member";

    const EMPTY_PRE_CHECK_MEMBER_TABLE_QUERY = "DELETE FROM precheck_member";

    const CHECK_IF_PRECHECK_ID_VALID_QUERY="SELECT number FROM precheck_member WHERE precheck_id = ?";

    const PUBLISH_PRE_CHECKED_MEMBERS_QUERY = "INSERT INTO member (number, firstname, lastname) SELECT number, firstname, lastname FROM precheck_member WHERE precheck_id = ?";

    const EXACTMATCH_QUERY = "SELECT id from member WHERE number=? AND upper(firstname)=? AND upper(lastname)=?";

    const BY_ID_QUERY = "SELECT number, firstname, lastname from member WHERE id=?";

    const BY_NUMBER_QUERY = "SELECT id, number, firstname, lastname from member WHERE number=?";

    const BY_FIRSTNAME_AND_LASTNAME_QUERY = "SELECT id, number, firstname, lastname from member WHERE firstname=? AND lastname=?";

    const BY_LASTNAME_QUERY = "SELECT id, number, firstname, lastname from member WHERE lastname=?";

    const BY_FIRSTNAME_QUERY = "SELECT id, number, firstname, lastname from member WHERE firstname=?";

    /**
     * @param $firstname
     * @return array|null
     */
    function getByFirstname($firstname)
    {
        $stmt = $this->getMysqli()->prepare(MemberDBHandler::BY_FIRSTNAME_QUERY);
        
        if ($stmt == false) {
            return null;
        }
        
        $stmt->bind_param('s', $firstname);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $stmt->close();
        
        if ($result->num_rows < 1) {
            return null;
        }
        
        $members = array();
        while ($row = $result->fetch_assoc()) {
            $member = new Member();
            $member->setId($row['id']);
            $member->setNumber($row['number']);
            $member->setFirstname($row['firstname']);
            $member->setLastname($row['lastname']);
            $members[] = $member;
        }
        
        $result->free();
        return $members;
    }

    /**
     * @param $lastname
     * @return Member[]|null
     */
    function getByLastname($lastname)
    {
        $stmt = $this->getMysqli()->prepare(MemberDBHandler::BY_LASTNAME_QUERY);
        
        if ($stmt == false) {
            error_log("Could not get member by lastname.");
            return null;
        }
        
        $stmt->bind_param('s', $lastname);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $stmt->close();
        
        if ($result->num_rows < 1) {
            return null;
        }
        
        $members = array();
        while ($row = $result->fetch_assoc()) {
            $member = new Member();
            $member->setId($row['id']);
            $member->setNumber($row['number']);
            $member->setFirstname($row['firstname']);
            $member->setLastname($row['lastname']);
            $members[] = $member;
        }
        
        $result->free();
        
        return $members;
    }

    /**
     * @param $firstname
     * @param $lastname
     * @return Member[]|null
     */
    function getByFirstnameAndLastname($firstname, $lastname)
    {
        $stmt = $this->getMysqli()->prepare(MemberDBHandler::BY_FIRSTNAME_AND_LASTNAME_QUERY);
        
        if ($stmt == false) {
            error_log("Could not get member by firstname and lastname.");
            return null;
        }
        
        $stmt->bind_param('ss', $firstname, $lastname);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $stmt->close();
        
        if ($result->num_rows < 1) {
            return null;
        }
        
        $members = array();
        while ($row = $result->fetch_assoc()) {
            $member = new Member();
            $member->setId($row['id']);
            $member->setNumber($row['number']);
            $member->setFirstname($row['firstname']);
            $member->setLastname($row['lastname']);
            $members[] = $member;
        }
        
        $result->free();
        return $members;
    }

    /**
     * @param $number
     * @return Member|null
     */
    function getByNumber($number)
    {
        $stmt = $this->getMysqli()->prepare(MemberDBHandler::BY_NUMBER_QUERY);
        
        if ($stmt == false) {
            error_log("Could not get member by number.");
            return null;
        }
        
        $stmt->bind_param('i', $number);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $stmt->close();
        
        $row = $result->fetch_assoc();
        
        if ($row['number'] == null) {
            return null;
        }
        
        $member = new Member();
        $member->setId($row['id']);
        $member->setNumber($row['number']);
        $member->setFirstname($row['firstname']);
        $member->setLastname($row['lastname']);
        
        $result->free();
        return $member;
    }

    /**
     * @param $number
     * @param $firstname
     * @param $lastname
     * @return null
     */
    function exactMatch($number, $firstname, $lastname)
    {
        $stmt = $this->getMysqli()->prepare(MemberDBHandler::EXACTMATCH_QUERY);
        
        if ($stmt == false) {
            return null;
        }
        
        $upper_firstname = strtoupper($firstname);
        $upper_lastname = strtoupper($lastname);
        
        $stmt->bind_param('iss', $number, $upper_firstname, $upper_lastname);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        $stmt->close();
        
        return $result->fetch_row()[0];
    }

    /**
     * @return Member[]|null
     */
    function getAllMembers()
    {
        $stmt = $this->getMysqli()->prepare(MemberDBHandler::GET_ALL_MEMBERS_QUERY);
        
        if (! $stmt) {
            error_log("Could not prepare query: " . MemberDBHandler::GET_ALL_MEMBERS_QUERY);
            return null;
        }
        
        if (! $stmt->execute()) {
            $stmt->close();
            return null;
        }
        
        $result = $stmt->get_result();
        $stmt->close();
        
        $members = array();
        while ($row = $result->fetch_assoc()) {
            $member = new Member();
            $member->setId($row['id']);
            $member->setNumber($row['number']);
            $member->setFirstname(utf8_encode($row['firstname']));
            $member->setLastname(utf8_encode($row['lastname']));
            $members[] = $member;
        }
        
        $result->free();
        
        return $members;
    }

    /**
     * @param $pre_check_Id
     * @return PublishPreCheckedMembersResultCode
     */
    function publishPreCheckedMembers($pre_check_Id) {

        if(0 >= $this->executeQueryWithPreCheckId(MemberDBHandler::CHECK_IF_PRECHECK_ID_VALID_QUERY, $pre_check_Id)){
            return new PublishPreCheckedMembersResultCode(PublishPreCheckedMembersResultCode::FAILED);
        }

        if(-1 == $this->executeQueryWithPreCheckId(MemberDBHandler::EMPTY_MEMBER_TABLE_QUERY, null)){
            return new PublishPreCheckedMembersResultCode(PublishPreCheckedMembersResultCode::FAILED);
        }

        if(-1 == $this->executeQueryWithPreCheckId(MemberDBHandler::PUBLISH_PRE_CHECKED_MEMBERS_QUERY, $pre_check_Id)){
            return new PublishPreCheckedMembersResultCode(PublishPreCheckedMembersResultCode::FAILED);
        }

        if(-1 == $this->executeQueryWithPreCheckId(MemberDBHandler::EMPTY_PRE_CHECK_MEMBER_TABLE_QUERY, null)){
            return new PublishPreCheckedMembersResultCode(PublishPreCheckedMembersResultCode::FAILED);
        }
        
        return new PublishPreCheckedMembersResultCode(PublishPreCheckedMembersResultCode::SUCCESSFUL);
    }

    /**
     * @param $pre_check_Id
     * @return Member[]|null
     */
    function getPreCheckMemberList($pre_check_Id)
    {
        $stmt = $this->getMysqli()->prepare(MemberDBHandler::GET_PRECHECK_MEMBER_LIST_QUERY);
        
        if (! $stmt)
            return null;
        
        $stmt->bind_param('i', $pre_check_Id);
        
        if (! $stmt->execute()) {
            $stmt->close();
            return null;
        } else {
            $stmt->bind_result($number, $firstname, $lastname);

            $members = array();
            while ($stmt->fetch()) {
                $member = new Member();
                $member->setNumber($number);
                $member->setFirstname(utf8_encode($firstname));
                $member->setLastname(utf8_encode($lastname));
                $members[] = $member;
            }
            
            $stmt->close();
            
            return $members;
        }
    }

    /**
     * @param Member $member
     * @param $precheck_id
     * @return Member|null
     */
    function addForPreCheck(Member $member, $precheck_id)
    {
        $stmt = $this->getMysqli()->prepare(MemberDBHandler::ADD_PRECHECK_MEMBER_QUERY);
        
        if (! $stmt)
            return null;
        
        $number = $member->getNumber();
        $firstname = $member->getFirstname();
        $lastname = $member->getLastname();
        
        $stmt->bind_param('isss', $precheck_id, $number, $firstname, $lastname);
        if (! $stmt->execute()) {
            $stmt->close();
            return null;
        } else {
            $stmt->close();
        }
        
        return $member;
    }

    function addMember(Member $member){
        $stmt = $this->getMysqli()->prepare(MemberDBHandler::ADD_MEMBER_QUERY);

        if (! $stmt)
            return null;

        $number = $member->getNumber();
        $firstname = $member->getFirstname();
        $lastname = $member->getLastname();

        $stmt->bind_param('sss', $number, $firstname, $lastname);
        if (! $stmt->execute()) {
            $stmt->close();
            return null;
        } else {
            $stmt->close();
        }

        return $member;
    }

    /**
     * @param $memberId
     * @return Member|null
     */
    function getById($memberId)
    {
        $stmt = $this->getMysqli()->prepare(MemberDBHandler::BY_ID_QUERY);
        
        if ($stmt == false) {
            return null;
        }
        
        $stmt->bind_param('i', $memberId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $stmt->close();
        
        $row = $result->fetch_assoc();
        
        if ($row['number'] == null) {
            return null;
        }
        
        $member = new Member();
        $member->setId($row['id']);
        $member->setNumber($row['number']);
        $member->setFirstname($row['firstname']);
        $member->setLastname($row['lastname']);
        
        return $member;
    }

    /**
     * @param $query
     * @param $preCheckId
     * @return int
     */
    function executeQueryWithPreCheckId($query, $preCheckId)
    {
        $stmt = $this->getMysqli()->prepare($query);

        if (! $stmt){
            $stmt->close();
            return -1;
        }

        if($preCheckId != null){
            $stmt->bind_param('i', $preCheckId);
        }

        if (! $stmt->execute()) {
            $stmt->close();
            return -1;
        }

        $stmt->get_result();

        $affected_rows = $stmt->affected_rows;

        $stmt->close();

        return $affected_rows;
    }
}
?>
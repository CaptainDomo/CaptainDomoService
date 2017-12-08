<?php

namespace cds\Controller;

use cds\Controller\Response\MemberResponse;
use cds\Controller\Response\PublishPreCheckedMembersResponse;
use cds\Database\MemberDBHandler;
use cds\Model\Member;
use Slim\Http\Request;
use Slim\Http\Response;

class MemberController
{

    const CSV_COL_HEAD_MEMBER_NO = "mitgliedsnummer_extern";

    const CSV_COL_HEAD_MEMBER_FIRSTNAME = "vorname";

    const CSV_COL_HEAD_MEMBER_LASTNAME = "name";

    const CSV_DELIMITER = ";";

    private $memberDBHandler;

    function __construct()
    {
        $this->memberDBHandler = new MemberDBHandler();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    function getAll(Request $request, Response $response, $args)
    {
        return $response->withJson(MemberResponse::fromListToResponse($this->memberDBHandler->getAllMembers()));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    function getPreCheckMembers(Request $request, Response $response, $args)
    {
        $pre_check_Id = $request->getAttribute("preCheckMemberId");

        $members = $this->memberDBHandler->getPreCheckMemberList($pre_check_Id);

        return $response->withJson(MemberResponse::fromListToResponse($members));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    function publishPreCheckedMembers(Request $request, Response $response, $args)
    {
        $pre_check_Id = $request->getAttribute("preCheckMemberId");

        $publishPreCheckedMembersResultCode = $this->memberDBHandler->publishPreCheckedMembers($pre_check_Id);

        return $response->withJson(['publishPreCheckedMembersResult'
        => PublishPreCheckedMembersResponse::toResponse($publishPreCheckedMembersResultCode)]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    function uploadMemberFile(Request $request, Response $response, $args)
    {
        $uploadedFile = $request->getUploadedFiles()["file"];

        $precheck_id = $this->preloadFromFile($uploadedFile->file);

        if ($precheck_id == null) {
            return $response->withJson('Could not preload file to Server', 400);
        } else {
            $uploadReponse = ['preCheckId' => $precheck_id];
            return $response->withJson($uploadReponse);
        }
    }

    private function remove_utf8_bom($text)
    {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }

    private function preloadFromFile($file_path_on_server)
    {
        $row_count = 0;
        $precheck_id = time();

        if (($handle = fopen($file_path_on_server, "r")) !== FALSE) {
            $columns = array();
            while (($data = fgetcsv($handle, 1000, MemberController::CSV_DELIMITER)) !== FALSE) {
                if ($row_count == 0) {

                    $data[0] = $this->remove_utf8_bom($data[0]);

                    $columns = $data;
                    $row_count++;
                } else {
                    $num = count($data);
                    $row_count++;

                    $member = new Member();

                    for ($c = 0; $c < $num && $c < count($columns); $c++) {
                        if ($columns[$c] == MemberController::CSV_COL_HEAD_MEMBER_NO) {
                            $member->setNumber($data[$c]);
                        }

                        if ($columns[$c] == MemberController::CSV_COL_HEAD_MEMBER_FIRSTNAME) {
                            $member->setFirstname($data[$c]);
                        }

                        if ($columns[$c] == MemberController::CSV_COL_HEAD_MEMBER_LASTNAME) {
                            $member->setLastname($data[$c]);
                        }
                    }

                    if ($this->memberDBHandler->addForPreCheck($member, $precheck_id) == null) {
                        error_log("Could not write member to precheck_table. Firstname: " . $member->getFirstname() . " Lastname: " . $member->getLastname() . " Number: " . $member->getNumber());
                    }
                }
            }

            fclose($handle);
        } else {
            error_log("Could not open uploaded file on server.");
            return null;
        }

        return $precheck_id;
    }
}
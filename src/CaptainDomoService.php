<?php
namespace cds;

use cds\Config\Config;
use Psr\Http\Message\RequestInterface as Request;
use Slim\App;
use Slim\Http\Response;
use Slim\Middleware\HttpBasicAuthentication;


class CaptainDomoService extends App
{
    function __construct()
    {
        $config['displayErrorDetails'] = true;
        $config['addContentLengthHeader'] = false;

        parent::__construct(["settings" => $config]);

        $this->add(new HttpBasicAuthentication([
            "path" => "/management", //Securing all routes for the management interface; registration is still public
            "realm" => "Protected",
            "users" => Config::get()['BasicAuthUsers'],
            "environment" => "REDIRECT_HTTP_AUTHORIZATION",
            "error" => function ($request, $response, $arguments) {
                $data = [];
                $data["status"] = "error";
                $data["message"] = $arguments["message"];
                $data["params"] = $request->getServerParams();
                return $response->withJson($data);
            }
        ]));

        //Get rid of trailling slashes
        $this->add(function (Request $request, Response $response, callable $next) {
            $uri = $request->getUri();
            $path = $uri->getPath();
            if ($path != '/' && substr($path, -1) == '/') {
                // permanently redirect paths with a trailing slash to their non-trailing counterpart
                $uri = $uri->withPath(substr($path, 0, -1));
                return $response->withRedirect((string)$uri, 301);
            }

            return $next($request, $response);
        });

        //Authentication
        $this->post('/authentication', 'cds\Controller\AuthenticationController:authenticate');

        // User interface
        $this->post('/registration', 'cds\Controller\RegistrationController:register');
        $this->post('/registration/addAdditionalEmail', 'cds\Controller\RegistrationController:addAdditionalEmail');
        $this->post('/unsubscribe', 'cds\Controller\RegistrationController:unsubscribe');

        // Management interface
        $this->get('/management/member', 'cds\Controller\MemberController:getAll');
        $this->post('/management/member/preCheckMember/uploadMemberFile', 'cds\Controller\MemberController:uploadMemberFile');
        $this->get('/management/member/preCheckMember/{preCheckMemberId}', 'cds\Controller\MemberController:getPreCheckMembers');
        $this->put('/management/member/preCheckMember/{preCheckMemberId}', 'cds\Controller\MemberController:publishPreCheckedMembers');

        $this->get('/management/subscription', 'cds\Controller\SubscriptionController:getAll');
        $this->delete('/management/subscription/{subscriptionId}', 'cds\Controller\SubscriptionController:unsubscribe');

        $this->get('/management/suspect', 'cds\Controller\SuspectController:getAll');
        $this->get('/management/suspect/{suspectId}/potentialmembers', 'cds\Controller\SuspectController:getPotentialMembersForSuspect');
        $this->put('/management/suspect/{suspectId}/resolveWithMember', 'cds\Controller\SuspectController:resolveWithMember');
        $this->put('/management/suspect/{suspectId}/resolveWithAdditionalSubscription', 'cds\Controller\SuspectController:resolveWithAdditionalSubscription');
        $this->put('/management/suspect/{suspectId}/resolveViaCreationOfNewMember', 'cds\Controller\SuspectController:resolveViaCreationOfNewMember');
        $this->put('/management/suspect/{suspectId}/resolveViaRejection', 'cds\Controller\SuspectController:resolveViaRejection');
    }
}
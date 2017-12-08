<?php
namespace cds\Controller;

use cds\Controller\Response\RegistrationResponse;
use cds\Model\SubscriptionRequest;
use cds\Model\UnsubscribeRequest;
use cds\Model\UnsubscribeResultCode;
use cds\Service\RegistrationSevice;
use Slim\Http\Request;
use Slim\Http\Response;

class RegistrationController
{
    private $registrationService;

    function __construct()
    {
        $this->registrationService = new RegistrationSevice();
    }

    function addAdditionalEmail(Request $request, Response $response, $args)
    {
        $registrationRequest = $request->getParsedBody();

        $subscriptionRequest = new SubscriptionRequest(
            $registrationRequest['number'],
            $registrationRequest['firstname'],
            $registrationRequest['lastname'],
            $registrationRequest['email']
        );

        $registerResult = $this->registrationService->addAdditionalEmail($subscriptionRequest);
        $response->withJson(RegistrationResponse::toResponse($registerResult));
    }

    function register(Request $request, Response $response, $args)
    {
        $registrationRequest = $request->getParsedBody();

        $subscriptionRequest = new SubscriptionRequest(
            $registrationRequest['number'],
            $registrationRequest['firstname'],
            $registrationRequest['lastname'],
            $registrationRequest['email']
        );

        $registerResult = $this->registrationService->register($subscriptionRequest);

        $response->withJson(RegistrationResponse::toResponse($registerResult));
    }

    function unsubscribe(Request $request, Response $response, $args)
    {
        $unsubscribeRequest = new UnsubscribeRequest(
            $request->getParsedBody()['email']
        );

        $unsubscribeResult = $this->registrationService->unsubscribe($unsubscribeRequest);

        switch ($unsubscribeResult->getUnsubscribeResultCode()) {
            case UnsubscribeResultCode::TECHNICAL_ERROR:
                $response_code = 500;
                break;
            default:
                $response_code = 200;
        }

        $response->withJson(UnsubscribeResponse::toResponse($unsubscribeResult), $response_code);
    }
}

?>
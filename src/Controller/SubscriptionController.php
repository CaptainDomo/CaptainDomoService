<?php
namespace cds\Controller;

use cds\Controller\Response\SubscriptionDeleteResponse;
use cds\Controller\Response\SubscriptionResponse;
use cds\Database\SubscriptionDBHandler;
use cds\Model\SubscriptionDeleteResult;
use cds\Model\SubscriptionDeleteResultCode;
use cds\Service\SubscriptionService;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;

class SubscriptionController
{
    protected $ci;
    private $subscriptionDBHandler;
    private $subscriptionService;

    //Constructor
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->subscriptionDBHandler = new SubscriptionDBHandler();
        $this->subscriptionService = new SubscriptionService();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    function getAll(Request $request, Response $response, $args)
    {
        $subscriptions = $this->subscriptionDBHandler->getSubscriptionList();

        return $response->withJson($subscriptionResponses = SubscriptionResponse::fromListToResponse($subscriptions));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    function unsubscribe(Request $request, Response $response, $args)
    {
        $subscripitonId = $args['subscriptionId'];

        $subscription = $this->subscriptionDBHandler->getById($subscripitonId);

        if ($subscription == null) {
            $subscriptionDeleteResult = new SubscriptionDeleteResult();
            $subscriptionDeleteResult->setSubscriptionDeleteResultCode(
                new SubscriptionDeleteResultCode(SubscriptionDeleteResultCode::SUBSCRIPTION_NOT_FOUND));

            return $response->withJson(SubscriptionDeleteResponse::toResponse($subscriptionDeleteResult));
        }

        $subscriptionDeleteResult = $this->subscriptionService->unsubscribe($subscription);

        return $response->withJson(SubscriptionDeleteResponse::toResponse($subscriptionDeleteResult));
    }
}
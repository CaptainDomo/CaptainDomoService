<?php

namespace cds\Controller;


use cds\Controller\Response\MemberSearchResultResponse;
use cds\Controller\Response\ResolveWithAdditionalSubscriptionResponse;
use cds\Controller\Response\SuspectResolvingResponse;
use cds\Controller\Response\SuspectResponse;
use cds\Database\MemberDBHandler;
use cds\Model\Member;
use cds\Model\MemberSearchResult;
use cds\Model\MemberSearchResultCode;
use cds\Model\ResolveWithAdditionalSubscriptionResultCode;
use cds\Model\SubscriptionRequest;
use cds\Model\SubscriptionSearchResultCode;
use cds\Model\SuspectResolvingResult;
use cds\Model\SuspectResolvingResultCode;
use cds\Service\SubscriptionService;
use cds\Service\SuspectService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;

class SuspectController
{
    private $memberDbHandler;
    private $subscriptionService;
    private $suspectService;

    function __construct()
    {
        $this->memberDbHandler = new MemberDBHandler();
        $this->subscriptionService = new SubscriptionService();
        $this->suspectService = new SuspectService();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    function getAll(Request $request, Response $response, $args)
    {
        $suspects = $this->suspectService->getAllSuspects();

        $suspectResponses = SuspectResponse::fromListToResponse($suspects);

        return $response->withJson($suspectResponses);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return null
     */
    function getPotentialMembersForSuspect(Request $request, Response $response, $args)
    {
        $suspect = $this->suspectService->getSuspect($args['suspectId']);

        if ($suspect == null) {
            return null;
        }

        $subscriptionSearchResult = $this->subscriptionService->searchSubscription(
            $suspect->getSubscriptionRequest()->getEmail(), new Member());

        if ($subscriptionSearchResult->getResultCode() == SubscriptionSearchResultCode::SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS) {
            $subscription = $subscriptionSearchResult->getSubscriptions()[0];
            $member = $subscription->getMember();

            if ($member == null) {
                $member = new Member();
                $member->setNumber($subscription->getNumber());
                $member->setFirstname($subscription->getFirstname());
                $member->setLastname($subscription->getLastname());
            }


            $memberSearchResults[] = new MemberSearchResult(
                MemberSearchResultCode::SUBSCRIPTION_WITH_OTHER_MEMBER_DATA_EXISTS, $member);
            return $response->withJson(MemberSearchResultResponse::fromListToResponse($memberSearchResults));
        }

        $memberSearchResults = [];

        $member = $this->memberDbHandler->getByNumber($suspect->getSubscriptionRequest()->getNumber());
        if ($member != null) {
            $memberSearchResults[] = new MemberSearchResult(MemberSearchResultCode::MATCH_BY_NUMBER, $member);
        }

        $memberSearchResults = array_merge($memberSearchResults,
            $this->buildMemberSearchResults(
                $this->memberDbHandler->getByFirstnameAndLastname(
                    $suspect->getSubscriptionRequest()->getFirstname(),
                    $suspect->getSubscriptionRequest()->getLastname()
                ), MemberSearchResultCode::MATCH_BY_FIRSTNAME_AND_LASTNAME));

        $memberSearchResults = array_merge($memberSearchResults,
            $this->buildMemberSearchResults(
                $this->memberDbHandler->getByLastname(
                    $suspect->getSubscriptionRequest()->getLastname()
                ), MemberSearchResultCode::MATCH_BY_LASTNAME));

        $memberSearchResults = array_merge($memberSearchResults,
            $this->buildMemberSearchResults(
                $this->memberDbHandler->getByFirstname(
                    $suspect->getSubscriptionRequest()->getFirstname()
                ), MemberSearchResultCode::MATCH_BY_FIRSTNAME));

        // Add possibility to add a subscription without a member
        $addNewMember[] = new MemberSearchResult(MemberSearchResultCode::ADD_NEW_MEMBER, new Member());
        $memberSearchResults = array_merge($memberSearchResults, $addNewMember);

        $memberSearchResults = $this->filterDuplicates($memberSearchResults);

        $memberSearchResults = $this->filterInvalidSubscriptionRequest($memberSearchResults, $suspect->getSubscriptionRequest());

        return $response->withJson(MemberSearchResultResponse::fromListToResponse($memberSearchResults));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    function resolveWithAdditionalSubscription(Request $request, Response $response, $args)
    {
        $suspect = $this->suspectService->getSuspect($args['suspectId']);

        if ($suspect == null) {
            return $this->returnWithJsonForResolveWithAdditionalSubscription(
                $response, ResolveWithAdditionalSubscriptionResultCode::SUSPECT_NOT_FOUND);
        }

        $postBody = $request->getParsedBody();
        $member = $this->memberDbHandler->getByNumber($postBody['memberNumber']);

        if ($member == null) {
            return $this->returnWithJsonForResolveWithAdditionalSubscription(
                $response, ResolveWithAdditionalSubscriptionResultCode::MEMBER_NOT_FOUND);
        }

        $postBody = $request->getParsedBody();
        $email = $postBody['email'];

        if ($email == null) {
            return $this->returnWithJsonForResolveWithAdditionalSubscription(
                $response, ResolveWithAdditionalSubscriptionResultCode::EMAIL_NOT_VALID);
        }

        $subscriptionRequest = new SubscriptionRequest(
            $member->getNumber(),
            $member->getFirstname(),
            $member->getLastname(),
            $email
        );

        $subscriptionSearchResult = $this->subscriptionService->addAdditionalSubscriptionForMember($member, $subscriptionRequest);

        if ($subscriptionSearchResult->getResultCode() == SubscriptionSearchResultCode::SUBSCRIPTION_ADDED) {
            if ($this->suspectService->delete($suspect)) {
                return $this->returnWithJsonForResolveWithAdditionalSubscription(
                    $response, ResolveWithAdditionalSubscriptionResultCode::SUBSCRIPTION_ADDED);
            }
        }

        return $this->returnWithJsonForResolveWithAdditionalSubscription(
            $response, ResolveWithAdditionalSubscriptionResultCode::RESOLVING_FAILED);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    function resolveWithMember(Request $request, Response $response, $args)
    {
        $suspectResolvingResult = new SuspectResolvingResult();

        $suspect = $this->suspectService->getSuspect($args['suspectId']);

        if ($suspect == null) {
            $suspectResolvingResult->setSuspectResolvingResultCode(
                new SuspectResolvingResultCode(SuspectResolvingResultCode::SUSPECT_NOT_FOUND));
            return $response->withJson(SuspectResolvingResponse::toResponse($suspectResolvingResult));
        }

        $postBody = $request->getParsedBody();
        $member = $this->memberDbHandler->getByNumber($postBody['memberNumber']);

        if ($member == null) {
            $suspectResolvingResult->setSuspectResolvingResultCode(
                new SuspectResolvingResultCode(SuspectResolvingResultCode::MEMBER_NOT_FOUND));
            return $response->withJson(SuspectResolvingResponse::toResponse($suspectResolvingResult));
        }

        $suspectResolvingResult = $this->suspectService->resolveSuspectWithMember($suspect, $member);

        return $response->withJson(SuspectResolvingResponse::toResponse($suspectResolvingResult));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    function resolveViaRejection(Request $request, Response $response, $args)
    {
        $suspect = $this->suspectService->getSuspect($request->getAttribute('suspectId'));

        $suspectResolvingResult = $this->suspectService->reject($suspect);

        return $response->withJson(SuspectResolvingResponse::toResponse($suspectResolvingResult));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    function resolveViaCreationOfNewMember(Request $request, Response $response, $args)
    {
        $suspectResolvingResult = new SuspectResolvingResult();

        $suspect = $this->suspectService->getSuspect($request->getAttribute('suspectId'));

        if ($suspect == null) {
            $suspectResolvingResult->setSuspectResolvingResultCode(
                new SuspectResolvingResultCode(SuspectResolvingResultCode::SUSPECT_NOT_FOUND));
        } else {
            $suspectResolvingResult = $this->suspectService->registerAndAddNewMember($suspect);
        }

        return $response->withJson(SuspectResolvingResponse::toResponse($suspectResolvingResult));
    }

    /**
     * @param $members
     * @param $memberSearchResultCode
     * @return array of MemberSearchResult
     */
    private function buildMemberSearchResults($members, $memberSearchResultCode)
    {
        $memberSearchResults = [];

        if ($members == null) {
            return $memberSearchResults;
        }

        foreach ($members as $member) {
            $memberSearchResults[] = new MemberSearchResult($memberSearchResultCode, $member);
        }

        return $memberSearchResults;
    }

    /**
     * @param MemberSearchResult[] $memberSearchResults
     * @return MemberSearchResult[]
     */
    private function filterDuplicates(array $memberSearchResults)
    {
        $memberIds = [];

        $filteredMemberSearchResults = [];

        foreach ($memberSearchResults as $memberSearchResult) {
            if ($memberSearchResult->getMember() == null) {
                $filteredMemberSearchResults[] = $memberSearchResult;
                continue;
            }

            $id = $memberSearchResult->getMember()->getId();
            if (!in_array($id, $memberIds)) {
                $filteredMemberSearchResults[] = $memberSearchResult;
                $memberIds[] = $id;
            }
        }

        return $filteredMemberSearchResults;
    }

    /**
     * @param MemberSearchResult[] $memberSearchResults
     * @param SubscriptionRequest $subscriptionRequest
     * @return MemberSearchResult[] $filteredMemberSearchResults
     */
    private function filterInvalidSubscriptionRequest($memberSearchResults, $subscriptionRequest)
    {
        $filteredMemberSearchResults = [];

        foreach ($memberSearchResults as $memberSearchResult) {
            if ($memberSearchResult->getMember() == null) {
                $filteredMemberSearchResults[] = $memberSearchResult;
                continue;
            }

            $subscriptionSearchResult = $this->subscriptionService
                ->searchSubscription($subscriptionRequest->getEmail(), $memberSearchResult->getMember());

            if (
                $subscriptionSearchResult->getResultCode() == SubscriptionSearchResultCode::SUBSCRIPTION_NOT_EXISTENT ||
                $subscriptionSearchResult->getResultCode() == SubscriptionSearchResultCode::SUBSCRIPTION_WITH_OTHER_EMAIL_EXISTS
            ) {
                $filteredMemberSearchResults[] = $memberSearchResult;
            }
        }

        return $filteredMemberSearchResults;
    }

    /**
     * @param Response $response
     * @param $resolveWithAdditionalSubscriptionResultCode
     * @return Response
     */
    private function returnWithJsonForResolveWithAdditionalSubscription(
        Response $response, $resolveWithAdditionalSubscriptionResultCode)
    {
        $resolveWithAdditionalSubscriptionResponse = new ResolveWithAdditionalSubscriptionResponse();

        $resolveWithAdditionalSubscriptionResponse->setResolveWithAdditionalSubscriptionResultCode(
            new ResolveWithAdditionalSubscriptionResultCode($resolveWithAdditionalSubscriptionResultCode));

        return $response->withJson(get_object_vars($resolveWithAdditionalSubscriptionResponse));
    }
}
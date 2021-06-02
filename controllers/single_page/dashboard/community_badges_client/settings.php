<?php

/**
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace Concrete\Package\CommunityBadgesClient\Controller\SinglePage\Dashboard\CommunityBadgesClient;

use Concrete\Controller\Panel\Page;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Url;
use PortlandLabs\CommunityBadgesClient\ApiClient;
use PortlandLabs\CommunityBadgesClient\Models\Achievements;
use Symfony\Component\HttpFoundation\Response;

class Settings extends DashboardPageController
{

    public function submit()
    {
        /** @var Validation $formValidation */
        $formValidation = $this->app->make(Validation::class);
        /** @var ApiClient $apiClient */
        $apiClient = $this->app->make(ApiClient::class);
        $formValidation->setData($this->request->request->all());
        $formValidation->addRequiredToken("update_settings");
        $formValidation->addRequired("endpoint", t("You need to enter a valid endpoint."));
        $formValidation->addRequired("clientId", t("You need to enter a valid client id."));
        $formValidation->addRequired("clientSecret", t("You need to enter a valid client secret."));
        if ($formValidation->test()) {
            $endpoint = $this->request->request->get("endpoint");
            $clientId = $this->request->request->get("clientId");
            $clientSecret = $this->request->request->get("clientSecret");

            $apiClient
                ->setEndpoint($endpoint)
                ->setClientId($clientId)
                ->setClientSecret($clientSecret);

            $this->flash('success', t("The settings has been successfully updated."));
            return $this->buildRedirect(['/dashboard/community_badges_client/view']);

        } else {
            $this->error = $formValidation->getError();
        }
        $this->view();
    }

    public function test_connection()
    {
        $achievements = $this->app->make(Achievements::class);
        $response = $achievements->getList();
        if ($response instanceof \Exception) {
            $this->error->add($response->getMessage());
        } else {
            $this->flash('success', t2('Successfully connected to badges endpoint. 1 badge detected.', 'Successfully connected to badges endpoint. %s badges detected.',
                count($response)
            ));
        }
        $this->view();
    }

    public function view()
    {
        /** @var ApiClient $apiClient */
        $apiClient = $this->app->make(ApiClient::class);

        $this->set('endpoint', $apiClient->getEndpoint());
        $this->set('clientId', $apiClient->getClientId());
        $this->set('clientSecret', $apiClient->getClientSecret());
        $this->set('showTestConnectionButton',
           !empty($apiClient->getEndpoint()) && !empty($apiClient->getClientId()) && !empty($apiClient->getClientSecret())
        );
    }

}

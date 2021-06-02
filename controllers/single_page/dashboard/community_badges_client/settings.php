<?php

/**
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace Concrete\Package\CommunityBadgesClient\Controller\SinglePage\Dashboard\CommunityBadgesClient;

use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Url;
use PortlandLabs\CommunityBadgesClient\ApiClient;
use Symfony\Component\HttpFoundation\Response;

class Settings extends DashboardPageController
{
    public function updated()
    {
        $this->set('success', t("The settings has been successfully updated."));
        $this->view();
    }

    public function view()
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        /** @var Validation $formValidation */
        $formValidation = $this->app->make(Validation::class);
        /** @var ApiClient $apiClient */
        $apiClient = $this->app->make(ApiClient::class);

        $formValidation->setData($this->request->request->all());
        $formValidation->addRequiredToken("update_settings");
        $formValidation->addRequired("endpoint", t("You need to enter a valid endpoint."));
        $formValidation->addRequired("clientId", t("You need to enter a valid client id."));
        $formValidation->addRequired("clientSecret", t("You need to enter a valid client secret."));

        if ($this->request->getMethod() === "POST") {
            if ($formValidation->test()) {
                $endpoint = $this->request->request->get("endpoint");
                $clientId = $this->request->request->get("clientId");
                $clientSecret = $this->request->request->get("clientSecret");

                $apiClient
                    ->setEndpoint($endpoint)
                    ->setClientId($clientId)
                    ->setClientSecret($clientSecret);

                return $responseFactory->redirect(Url::to("/dashboard/community_badges_client/settings/updated"), Response::HTTP_TEMPORARY_REDIRECT);

            } else {
                $this->error = $formValidation->getError();
            }
        }

        $this->set('endpoint', $apiClient->getEndpoint());
        $this->set('clientId', $apiClient->getClientId());
        $this->set('clientSecret', $apiClient->getClientSecret());
    }

}

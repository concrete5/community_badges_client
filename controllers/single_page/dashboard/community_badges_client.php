<?php

/**
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace Concrete\Package\CommunityBadgesClient\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Support\Facade\Url;
use Symfony\Component\HttpFoundation\Response;

class CommunityBadgesClient extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;

    public function on_start()
    {
        parent::on_start();
        $this->responseFactory = $this->app->make(ResponseFactory::class);
    }

    public function view()
    {
        return $this->responseFactory->redirect((string)Url::to("/dashboard/community_badges_client/settings"), Response::HTTP_TEMPORARY_REDIRECT);
    }
}

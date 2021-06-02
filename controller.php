<?php

/**
 * @project:   Community Badges Client
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace Concrete\Package\CommunityBadgesClient;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Package\Package;

class Controller extends Package
{
    protected $pkgHandle = 'community_badges_client';
    protected $appVersionRequired = '9.0';
    protected $pkgVersion = '0.0.14';
    protected $pkgAutoloaderRegistries = [
        'src/PortlandLabs/CommunityBadgesClient' => 'PortlandLabs\CommunityBadgesClient',
    ];

    public function getPackageDescription()
    {
        return t("Assign badges remotely to the community site.");
    }

    public function getPackageName()
    {
        return t("Community Badges Client");
    }

    public function on_start()
    {
        if (file_exists($this->getPackagePath() . '/vendor/autoload.php')) {
            require $this->getPackagePath() . '/vendor/autoload.php';
        }
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installContentFile("data.xml");
        /** @var NavigationCache $navigationCache */
        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();
        return $pkg;
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile("data.xml");
        /** @var NavigationCache $navigationCache */
        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();
    }
}

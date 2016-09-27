<?php

/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Service;

use Agit\IntlBundle\Tool\Translate;
use Agit\SeedBundle\Event\SeedEvent;

class LogentryCategorySeed
{
    public function registerSeed(SeedEvent $event)
    {
        $event->addSeedEntry("AgitLoggingBundle:LogentryCategory", [
            "id"   => "agit.settings",
            "name" => Translate::noopX("logging category", "Settings")
        ]);
    }
}

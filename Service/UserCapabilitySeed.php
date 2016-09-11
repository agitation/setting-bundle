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

class UserCapabilitySeed
{
    public function registerSeed(SeedEvent $event)
    {
        $capabilities = [
            ["id" => "agit.setting.load", "name" => Translate::noopX("user capability", "Load settings")],
            ["id" => "agit.setting.save", "name" => Translate::noopX("user capability", "Save settings")]
        ];

        foreach ($capabilities as $capability) {
            $event->addSeedEntry("AgitUserBundle:UserCapability", $capability);
        }
    }
}

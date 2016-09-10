<?php

/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Plugin;

use Agit\BaseBundle\Pluggable\Seed\SeedEntry;
use Agit\BaseBundle\Pluggable\Seed\SeedPlugin;
use Agit\BaseBundle\Pluggable\Seed\SeedPluginInterface;
use Agit\IntlBundle\Tool\Translate;

/**
 * @SeedPlugin(entity="AgitUserBundle:UserCapability")
 */
class UserCapabilitySeedPlugin implements SeedPluginInterface
{
    private $seedData = [];

    public function load()
    {
        $capabilities = [
            ["id" => "agit.settings.load", "name" => Translate::noopX("user capability", "Load settings")],
            ["id" => "agit.settings.save", "name" => Translate::noopX("user capability", "Save settings")]
        ];

        foreach ($capabilities as $capability) {
            $seedEntry = new SeedEntry();
            $seedEntry->setDoUpdate(true);
            $seedEntry->setData($capability);
            $this->seedData[] = $seedEntry;
        }
    }

    public function nextSeedEntry()
    {
        return array_pop($this->seedData);
    }
}

<?php
/**
 * @package    agitation/setting
 * @link       http://github.com/agitation/AgitSettingBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Plugin;

use Agit\BaseBundle\Pluggable\Seed\SeedPluginInterface;
use Agit\BaseBundle\Pluggable\Seed\SeedPlugin;
use Agit\BaseBundle\Pluggable\Seed\SeedEntry;
use Agit\BaseBundle\Tool\Translate;

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

        foreach ($capabilities as $capability)
        {
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

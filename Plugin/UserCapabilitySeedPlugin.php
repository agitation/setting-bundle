<?php
/**
 * @package    agitation/setting
 * @link       http://github.com/agitation/AgitSettingBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Plugin;

use Agit\PluggableBundle\Strategy\Seed\SeedPluginInterface;
use Agit\PluggableBundle\Strategy\Seed\SeedPlugin;
use Agit\PluggableBundle\Strategy\Seed\SeedEntry;
use Agit\IntlBundle\Translate;

/**
 * @SeedPlugin(entity="AgitUserBundle:UserCapability")
 */
class UserCapabilitySeedPlugin implements SeedPluginInterface
{
    private $seedData = [];

    public function load()
    {
        $capabilities = [
            ['id' => 'agit.settings.load', 'name' => Translate::noopX('Load settings', 'user capability')],
            ['id' => 'agit.settings.save', 'name' => Translate::noopX('Save settings', 'user capability')]
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

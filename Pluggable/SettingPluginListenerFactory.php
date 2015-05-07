<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Pluggable;

use Agit\CoreBundle\Service\ClassCollector;

/**
 * Creates object collector listeners.
 */
class SettingPluginListenerFactory
{
    protected $ClassCollector;

    public function __construct(ClassCollector $ClassCollector)
    {
        $this->ClassCollector = $ClassCollector;
    }

    public function create($searchPath, $priority = 100)
    {
        return new SettingPluginListener($this->ClassCollector, $searchPath, $priority);
    }
}

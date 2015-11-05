<?php
/**
 * @package    agitation/setting
 * @link       http://github.com/agitation/AgitSettingBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Plugin;

use Agit\PluggableBundle\Strategy\Cache\CachePlugin;
use Agit\ApiBundle\Plugin\AbstractApiEndpointPlugin;

/**
 * @CachePlugin(tag="agit.api.endpoint")
 */
class ApiEndpointPlugin extends AbstractApiEndpointPlugin
{
    protected function getSearchNamespace()
    {
        return "Agit\SettingBundle\Api\Endpoint";
    }

    protected function getApiNamespace()
    {
        return "setting.v1";
    }
}

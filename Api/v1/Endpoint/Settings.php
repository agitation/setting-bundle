<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Api\v1\Endpoint;

use Agit\ApiBundle\Api\Meta;
use Agit\ApiBundle\Api\Endpoint\AbstractEndpoint;
use Agit\ApiBundle\Api\Object\AbstractObject;

class Settings extends AbstractEndpoint
{
    /**
     * @Meta\Call\Call(request="common.v1/String[]",response="Setting[]")
     * @Meta\Call\Security(capability="agit.settings.load")
     *
     * Load application settings by setting names.
     */
    protected function load(array $names)
    {
        $result = [];
        $SettingList = $this->getService('agit.settings')->getSettings($names);

        foreach ($SettingList as $Setting)
            $result[] = $this->createObject('Setting', (object)[
                "id" => $Setting->getId(), "value" => $Setting->getValue()
            ]);

        return $result;
    }

    /**
     * @Meta\Call\Call(request="Setting[]",response="Setting[]")
     * @Meta\Call\Security(capability="agit.settings.save")
     *
     * Save application settings.
     */
    protected function save(array $ApiSettingList)
    {
        $settings = [];

        foreach ($ApiSettingList as $ApiSetting)
            $settings[$ApiSetting->get('id')] = $ApiSetting->get('value');

        $SettingList = $this->getService('agit.settings')->getSettings(array_keys($settings));

        foreach ($SettingList as $Setting)
            $Setting->setValue($settings[$Setting->getId()]);

        $this->getService('agit.settings')->saveSettings($SettingList);

        return $this->load(array_keys($settings));
    }
}

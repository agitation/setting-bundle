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
        $settingList = $this->getService('agit.settings')->getSettings($names);

        foreach ($settingList as $setting)
            $result[] = $this->createObject('Setting', (object)[
                "id" => $setting->getId(), "value" => $setting->getValue()
            ]);

        return $result;
    }

    /**
     * @Meta\Call\Call(request="Setting[]",response="Setting[]")
     * @Meta\Call\Security(capability="agit.settings.save")
     *
     * Save application settings.
     */
    protected function save(array $apiSettingList)
    {
        $settings = [];

        foreach ($apiSettingList as $apiSetting)
            $settings[$apiSetting->get('id')] = $apiSetting->get('value');

        $settingList = $this->getService('agit.settings')->getSettings(array_keys($settings));

        foreach ($settingList as $setting)
            $setting->setValue($settings[$setting->getId()]);

        $this->getService('agit.settings')->saveSettings($settingList);

        return $this->load(array_keys($settings));
    }
}

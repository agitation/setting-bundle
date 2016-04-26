<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Plugin\Api\SettingV1\Endpoint;

use Agit\ApiBundle\Annotation\Endpoint;
use Agit\ApiBundle\Common\AbstractController;
use Agit\PluggableBundle\Strategy\Depends;

/**
 * @Endpoint\Controller
 */
class Settings extends AbstractController
{
    /**
     * @Endpoint\Endpoint(request="common.v1/String[]",response="Setting[]")
     * @Endpoint\Security(capability="agit.settings.read")
     * @Depends({"agit.settings"})
     *
     * Load application settings by setting names.
     */
    protected function load(array $names)
    {
        $result = [];
        $settingList = $this->getService("agit.settings")->getSettings($names);

        foreach ($settingList as $setting)
            $result[] = $this->createObject("Setting", (object)[
                "id" => $setting->getId(), "value" => $setting->getValue()
            ]);

        return $result;
    }

    /**
     * @Endpoint\Endpoint(request="Setting[]",response="Setting[]")
     * @Endpoint\Security(capability="agit.settings.write")
     * @Depends({"agit.settings"})
     *
     * Save application settings.
     */
    protected function save(array $apiSettingList)
    {
        $settings = [];

        foreach ($apiSettingList as $apiSetting)
            $settings[$apiSetting->get("id")] = $apiSetting->get("value");

        $settingList = $this->getService("agit.settings")->getSettings(array_keys($settings));

        foreach ($settingList as $setting)
            $setting->setValue($settings[$setting->getId()]);

        $this->getService("agit.settings")->saveSettings($settingList);

        return $this->load(array_keys($settings));
    }
}

<?php

/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Plugin\Api\SettingV1\Endpoint;

use Agit\ApiBundle\Annotation\Endpoint;
use Agit\ApiBundle\Common\AbstractController;
use Agit\BaseBundle\Pluggable\Depends;

/**
 * @Endpoint\Controller
 */
class Settings extends AbstractController
{
    /**
     * @Endpoint\Endpoint(request="common.v1/String[]",response="Setting[]")
     * @Endpoint\Security(capability="agit.setting.read")
     * @Depends({"@agit.setting"})
     *
     * Load application settings by setting names.
     */
    protected function load(array $names)
    {
        $result = [];
        $settingList = $this->getService("agit.setting")->getSettings($names);

        foreach ($settingList as $setting) {
            $result[] = $this->createObject("Setting", (object) [
                "id" => $setting->getId(), "value" => $setting->getValue()
            ]);
        }

        return $result;
    }

    /**
     * @Endpoint\Endpoint(request="Setting[]",response="Setting[]")
     * @Endpoint\Security(capability="agit.setting.write")
     * @Depends({"@agit.setting"})
     *
     * Save application settings.
     */
    protected function save(array $apiSettingList)
    {
        $settings = [];

        foreach ($apiSettingList as $apiSetting) {
            $settings[$apiSetting->get("id")] = $apiSetting->get("value");
        }

        $settingList = $this->getService("agit.setting")->getSettings(array_keys($settings));

        foreach ($settingList as $setting) {
            $setting->setValue($settings[$setting->getId()]);
        }

        $this->getService("agit.setting")->saveSettings($settingList);

        return $this->load(array_keys($settings));
    }
}

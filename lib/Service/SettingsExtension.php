<?php

/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Service;

use Twig_Extension;
use Twig_SimpleFunction;

class SettingsExtension extends Twig_Extension
{
    private $settingService;

    public function __construct($settingService)
    {
        $this->settingService = $settingService;
    }

    public function getName()
    {
        return "agit.settings";
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction("getSetting", [$this, "getSetting"], ["is_safe" => ["all"]]),
            new Twig_SimpleFunction("getSettings", [$this, "getSettings"], ["is_safe" => ["all"]])
        ];
    }

    public function getSetting($id)
    {
        return $this->settingService->getValueOf($id);
    }

    public function getSettings(array $ids)
    {
        $settings = $this->settingService->getValuesOf($ids);

        // NOTE: we're reusing the $ids array intentionally
        // in order to preserve the passed keys

        foreach ($ids as $key => $name) {
            $ids[$key] = $settings[$name];
        };

        return $ids;
    }
}

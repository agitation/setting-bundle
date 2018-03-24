<?php
declare(strict_types=1);

/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Event;

use Agit\SettingBundle\Service\SettingService;
use Symfony\Component\EventDispatcher\Event;

class SettingsLoadedEvent extends Event
{
    private $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function getSettingService()
    {
        return $this->settingService;
    }
}

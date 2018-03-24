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

class SettingsModifiedEvent extends SettingsLoadedEvent
{
    private $modifiedSettings;

    public function __construct(SettingService $settingService, array $modifiedSettings)
    {
        parent::__construct($settingService);
        $this->modifiedSettings = $modifiedSettings;
    }

    public function getModifiedSettings()
    {
        return $this->modifiedSettings;
    }
}

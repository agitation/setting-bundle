<?php
declare(strict_types=1);

/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Event;

use Agit\SettingBundle\Service\SettingInterface;
use Symfony\Component\EventDispatcher\Event;

class SettingsLoadedEvent extends Event
{
    /**
     * @var SettingInterface[]
     */
    private $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return SettingInterface[]
     */
    public function getSettings() : array
    {
        return $this->settings;
    }
}

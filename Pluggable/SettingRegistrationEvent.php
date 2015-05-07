<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Pluggable;

use Symfony\Component\EventDispatcher\Event;
use Agit\SettingBundle\Setting\AbstractSetting;

class SettingRegistrationEvent extends Event
{
    public function __construct(SettingProcessor $Processor)
    {
        $this->Processor = $Processor;
    }

    public function register(AbstractSetting $Setting, $priority)
    {
        return $this->Processor->register($Setting, $priority);
    }
}
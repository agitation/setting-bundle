<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Pluggable;

use Agit\CoreBundle\Service\ClassCollector;
use Agit\CoreBundle\Exception\InternalErrorException;
use Agit\SettingBundle\Setting\AbstractSetting;

class SettingPluginListener
{
    private $ClassCollector;

    protected $searchPath;

    private $priority;

    public function __construct($ClassCollector, $searchPath, $priority)
    {
        $this->ClassCollector = $ClassCollector;
        $this->searchPath = $searchPath;
        $this->priority = $priority;
    }

    /**
     * the event listener to be used in the service configuration
     */
    public function onRegistration(SettingRegistrationEvent $RegistrationEvent)
    {
        foreach ($this->ClassCollector->collect($this->searchPath) as $class)
        {
            $object = new $class();

            if (!($object instanceof AbstractSetting))
                throw new InternalErrorException("Class $class must be a child of AbstractSetting.");

            $RegistrationEvent->register($object, $this->priority);
        }
    }
}

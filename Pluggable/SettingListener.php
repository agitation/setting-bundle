<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Pluggable;

use Agit\CoreBundle\Pluggable\PluggableServiceRegistrationEvent;
use Agit\CoreBundle\Pluggable\Strategy\Object\ObjectProcessorFactory;
use Agit\CoreBundle\Pluggable\Strategy\Fixture\FixtureProcessorFactory;

/**
 * This class is used by pluggable services themselves to generate listeners
 * for their expected objects. Use ObjectListenerFactory to create instances.
 */
class SettingListener
{
    private $ObjectProcessorFactory;

    private $FixtureProcessorFactory;

    public function __construct(ObjectProcessorFactory $ObjectProcessorFactory, FixtureProcessorFactory $FixtureProcessorFactory)
    {
        $this->ObjectProcessorFactory = $ObjectProcessorFactory;
        $this->FixtureProcessorFactory = $FixtureProcessorFactory;
    }

    public function onRegistration(PluggableServiceRegistrationEvent $RegistrationEvent)
    {
        $RegistrationEvent->registerProcessor(new SettingProcessor(
            $this->ObjectProcessorFactory,
            $this->FixtureProcessorFactory
        ));
    }
}

<?php
/**
 * @package    agitation/setting
 * @link       http://github.com/agitation/AgitSettingBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Agit\CoreBundle\Pluggable\Strategy\Seed\SeedRegistrationEvent;
use Agit\IntlBundle\Service\Translate;

class UserCapabilitySeedData
{
    public function onRegistration(SeedRegistrationEvent $registrationEvent)
    {
        $translate = new Translate();

        $capabilities = [
            ['agit.settings.load', $translate->noopX('Load settings', 'user capability')],
            ['agit.settings.save', $translate->noopX('Save settings', 'user capability')]
        ];

        foreach ($capabilities as $cap)
        {
            $registrationData = $registrationEvent->createContainer();

            $registrationData->setData([
                'id' => $cap[0],
                'name' => $cap[1]
            ]);

            $registrationEvent->register($registrationData);
        }
    }
}

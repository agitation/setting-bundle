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
    public function onRegistration(SeedRegistrationEvent $RegistrationEvent)
    {
        $Translate = new Translate();

        $capabilities = [
            ['agit.settings.load', $Translate->noopX('Load settings', 'user capability')],
            ['agit.settings.save', $Translate->noopX('Save settings', 'user capability')]
        ];

        foreach ($capabilities as $cap)
        {
            $RegistrationData = $RegistrationEvent->createContainer();

            $RegistrationData->setData([
                'id' => $cap[0],
                'name' => $cap[1]
            ]);

            $RegistrationEvent->register($RegistrationData);
        }
    }
}

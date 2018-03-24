<?php
declare(strict_types=1);

/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterSettingsCompilerPass implements CompilerPassInterface
{
    private $containerBuilder;

    public function process(ContainerBuilder $containerBuilder)
    {
        $processor = $containerBuilder->findDefinition('agit.setting');
        $services = $containerBuilder->findTaggedServiceIds('agit.setting');

        foreach ($services as $name => $tags)
        {
            foreach ($tags as $tag)
            {
                $processor->addMethodCall('addSetting', [new Reference($name)]);
            }
        }
    }
}

<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Plugin;

use Agit\PluggableBundle\Strategy\Entity\EntityPluginInterface;
use Agit\PluggableBundle\Strategy\ServiceAwarePluginInterface;
use Agit\PluggableBundle\Strategy\ServiceAwarePluginTrait;
use Agit\PluggableBundle\Strategy\Depends;

/**
 * @Depends({"@agit.validation"})
 */
abstract class AbstractSetting implements EntityPluginInterface, ServiceAwarePluginInterface
{
    use ServiceAwarePluginTrait;

    private $value;

    private $seeds = [];

    final public function loadSeedData()
    {
        $this->seeds["AgitSettingBundle:Setting"] =
        [
            [ 'id' => $this->getId(), 'value' => $this->getDefaultValue() ]
        ];
    }

    final public function nextSeedEntry($entityName)
    {
        $val = array_key_exists($entityName, $this->seeds)
            ? array_pop($this->seeds[$entityName])
            : null;

        return $val;
    }

    final public function setEntity($entity)
    {
        $this->value = $entity->getValue();
    }

    final public function setValue($value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    final public function getValue()
    {
        return $this->value;
    }

    /**
     * Read-only settings cannot be edited through the API (i.e. by a "normal"
     * admin), but they can and have to be set programmatically by calling
     * SettingService::saveSetting directly with the $force parameter set to true.
     */
    public function isReadonly()
    {
        return false;
    }

    abstract public function getId();

    abstract public function getName();

    abstract public function getDefaultValue();

    /**
     * Must throw an exception if the value is invalid.
     */
    abstract public function validate($value);
}

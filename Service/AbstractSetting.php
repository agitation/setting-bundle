<?php

/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Service;

abstract class AbstractSetting
{
    private $value;

    private $seeds = [];

    final public function setEntity($entity)
    {
        $this->value = $entity->getValue();
    }

    /**
     * @internal just for the SettingService, to bypass validation when loading from database
     */
    final public function _restoreValue($value)
    {
        $this->value = $value;
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
     * admin). Instead, they can and have to be set programmatically by calling
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

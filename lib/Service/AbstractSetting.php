<?php
declare(strict_types=1);

/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Service;

use Agit\SettingBundle\Entity\Setting;

abstract class AbstractSetting implements SettingInterface
{
    protected $readonly = false;

    protected $value;

    private $seeds = [];

    final public function setEntity(Setting $entity)
    {
        $this->value = $entity->getValue();
    }

    /**
     * @internal just for the SettingService, to bypass validation when loading from database
     * @param mixed $value
     */
    public function _setRealValue($value)
    {
        $this->value = $value;
    }

    /**
     * @internal just for the SettingService, to bypass validation when loading from database
     * @param mixed $value
     */
    public function _getRealValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value setter for the "public" value
     */
    public function setValue($value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    /**
     * @return mixed the "public" value
     */
    public function getValue()
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
        return $this->readonly;
    }
}

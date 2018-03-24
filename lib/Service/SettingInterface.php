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

interface SettingInterface
{
    public function setEntity(Setting $setting);

    /**
     * @internal just for the SettingService, to inject the value stored in the DB
     * @param mixed $value
     */
    public function _setRealValue($value);

    /**
     * @internal just for the SettingService, to get the value to be stored in the DB
     * @param mixed $value
     */
    public function _getRealValue();

    public function setValue($value);

    public function getValue();

    /**
     * Read-only settings cannot be edited through the API (i.e. by a "normal"
     * admin). Instead, they can and have to be set programmatically by calling
     * SettingService::saveSetting directly with the $force parameter set to true.
     */
    public function isReadonly();

    public function getId();

    public function getName();

    public function getDefaultValue();

    /**
     * @throws InvalidSettingValueException if the value is invalid
     * @param  mixed                        $value
     */
    public function validate($value);
}

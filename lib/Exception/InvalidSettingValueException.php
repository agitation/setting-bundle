<?php

/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Exception;

use Agit\BaseBundle\Exception\AgitException;

/**
 * Passed an invalid value for a setting.
 */
class InvalidSettingValueException extends AgitException
{
    protected $httpStatus = 400;
}

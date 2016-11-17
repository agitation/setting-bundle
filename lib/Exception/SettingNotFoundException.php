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
 * Tried to modify a non-existent setting.
 */
class SettingNotFoundException extends AgitException
{
    protected $statusCode = 404;
}

<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Exception;

use Agit\CommonBundle\Exception\AgitException;

/**
 * Tried to modify a non-existent setting.
 */
class SettingNotFoundException extends AgitException
{
    protected $httpStatus = 404;
}

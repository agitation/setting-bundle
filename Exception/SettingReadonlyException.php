<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Exception;

use Agit\BaseBundle\Exception\AgitException;

/**
 * Tried to modify a setting which is read-only.
 */
class SettingReadonlyException extends AgitException
{
    protected $httpStatus = 400;
}

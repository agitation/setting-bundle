<?php
/**
 * @package    agitation/setting
 * @link       http://github.com/agitation/AgitSettingBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Api\v1\Object;

use Agit\ApiBundle\Api\Object\AbstractObject;
use Agit\ApiBundle\Api\Meta\Property;

/**
 * An application setting.
 */
class Setting extends AbstractObject
{
    /**
     * @Property\StringType(minLength=3,maxLength=40)
     *
     * Identifier of the setting.
     */
    public $id;

    /**
     * @Property\PolymorphicType
     *
     * The setting value.
     */
    public $value;
}

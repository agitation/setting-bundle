<?php
/**
 * @package    agitation/setting
 * @link       http://github.com/agitation/AgitSettingBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Plugin\Api\SettingV1\Object;

use Agit\ApiBundle\Annotation\Object;
use Agit\ApiBundle\Annotation\Property;
use Agit\ApiBundle\Common\AbstractEntityObject;

/**
 * @Object\Object
 *
 * An application setting.
 */
class Setting extends AbstractEntityObject
{
    /**
     * @Property\StringType(minLength=3, maxLength=40)
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

<?php

/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
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

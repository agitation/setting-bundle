<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Setting;

use Agit\ValidationBundle\Exception\InvalidValueException;
use Agit\IntlBundle\Service\Translate;
use Agit\CoreBundle\Pluggable\Strategy\Combined\CombinedPluginInterface;

abstract class AbstractSetting implements CombinedPluginInterface
{
    protected $Translate;

    private $value;

    // used for creating an instance during plugin registration
    private static $instance;

    public static function getPluginId()
    {
        if (!self::$instance)
            self::$instance = new static();

        return self::$instance->getId();
    }

    final public static function getFixtures($entityName)
    {
        if (!self::$instance)
            self::$instance = new static();

        return [
            ['id' => self::$instance->getId(), 'value' => self::$instance->getDefaultValue()]
        ];
    }

    public function __construct($value = null)
    {
        $this->value = is_null($value)
            ? $this->getDefaultValue()
            : $value;

        $this->Translate = new Translate();
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

    abstract public function getId();

    abstract public function getName();

    abstract public function getDefaultValue();

    /**
     * Must throw an exception if the value is invalid.
     */
    abstract public function validate($value);
}

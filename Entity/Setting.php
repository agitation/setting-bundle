<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Agit\CommonBundle\Entity\IdentityAwareTrait;

/**
 * @ORM\Entity
 */
class Setting
{
    use IdentityAwareTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="string",length=30)
     */
    private $id;

    /**
     * @ORM\Column(type="object")
     */
    private $value;

    /**
     * Set value
     *
     * @param \stdClass $value
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return \stdClass 
     */
    public function getValue()
    {
        return $this->value;
    }
}

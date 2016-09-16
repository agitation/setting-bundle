<?php

/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Entity;

use Agit\BaseBundle\Entity\IdentityAwareTrait;
use Doctrine\ORM\Mapping as ORM;

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
     * Set value.
     *
     * @param \stdClass $value
     *
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return \stdClass
     */
    public function getValue()
    {
        return $this->value;
    }
}

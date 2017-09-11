<?php
declare(strict_types=1);
/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Exception;

use Agit\BaseBundle\Exception\PublicException;

/**
 * Tried to modify a setting which is read-only.
 */
class SettingReadonlyException extends PublicException
{
    protected $statusCode = 400;
}

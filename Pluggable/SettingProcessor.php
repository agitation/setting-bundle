<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Pluggable;

use Agit\CoreBundle\Pluggable\Strategy\ProcessorInterface;
use Agit\CoreBundle\Pluggable\Strategy\Object\ObjectProcessorFactory;
use Agit\CoreBundle\Pluggable\Strategy\Cache\CacheData;
use Agit\CoreBundle\Pluggable\Strategy\Fixture\FixtureProcessorFactory;
use Agit\CoreBundle\Pluggable\Strategy\Fixture\FixtureData;
use Agit\SettingBundle\Setting\AbstractSetting;

/**
 * This is a special processor which internally combines the ObjectProcessor
 * and the FixtureProcessor.
 */
class SettingProcessor implements ProcessorInterface
{
    private $ObjectProcessor;

    private $FixtureProcessor;

    private $parentClass = "\Agit\SettingBundle\Setting\AbstractSetting";

    private $registrationTag = "agit.setting";

    private $entityName = "AgitSettingBundle:Setting";

    public function __construct(ObjectProcessorFactory $ObjectProcessorFactory, FixtureProcessorFactory $FixtureProcessorFactory)
    {
        $this->ObjectProcessor = $ObjectProcessorFactory->create($this->registrationTag, $this->parentClass);
        $this->FixtureProcessor = $FixtureProcessorFactory->create($this->entityName, $this->getPriority(), true, false);
    }

    public function createRegistrationEvent()
    {
        return new SettingRegistrationEvent($this);
    }

    public function getRegistrationTag()
    {
        return $this->registrationTag;
    }

    public function register(AbstractSetting $Setting, $priority)
    {
        $CacheData = new CacheData();
        $CacheData->setId($Setting->getId());
        $CacheData->setData(get_class($Setting));
        $this->ObjectProcessor->register($CacheData);

        $FixtureData = new FixtureData();
        $FixtureData->setData(['id' => $Setting->getId(), 'value' => $Setting->getValue()]);
        $this->FixtureProcessor->register($FixtureData);
    }

    public function getPriority()
    {
        return 1;
    }

    public function process()
    {
        $this->ObjectProcessor->process();
        $this->FixtureProcessor->process();

    }
}

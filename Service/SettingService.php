<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Service;

use Agit\CoreBundle\Exception\InternalErrorException;
use Agit\IntlBundle\Service\Translate;
use Agit\SettingBundle\Exception\SettingNotFoundException;
use Agit\SettingBundle\Exception\SettingReadonlyException;
use Agit\SettingBundle\Setting\AbstractSetting;
use Agit\CoreBundle\Pluggable\Strategy\Object\ObjectLoader;
use Doctrine\ORM\EntityManager;

class SettingService
{
    private $objectLoader;

    private $entityManager;

    private $settingList = [];

    public function __construct(ObjectLoader $objectLoader, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->objectLoader = $objectLoader;

        $this->objectLoader->setObjectFactory(function ($id, $className)
        {
            $settingEntity = $this->getSettingEntity($id);
            $setting = new $className($settingEntity->getValue());
            return $setting;
        });
    }

    public function getSetting($id)
    {
        try
        {
            $setting = $this->objectLoader->getObject($id);
        }
        catch(\Exception $e)
        {
            throw new SettingNotFoundException(sprintf(Translate::getInstance()->t("Setting `%s` could not be loaded."), $id));
        }

        return $setting;
    }

    private function getSettingEntity($id)
    {
        $setting = $this->entityManager->find('AgitSettingBundle:Setting', $id);

        if (!$setting)
            throw new SettingNotFoundException(sprintf(Translate::getInstance()->t("Setting `%s` does not exist."), $id));

        return $setting;
    }

    public function getSettings(array $idList)
    {
        $settingList = [];

        foreach ($idList as $id)
            $settingList[] = $this->getSetting($id);

        return $settingList;
    }

    public function saveSetting(AbstractSetting $setting, $force = false)
    {
        if (!$force && $setting->isReadonly())
            throw new SettingReadonlyException(sprintf(Translate::getInstance()->t("Setting `%s` is read-only."), $setting->getId()));

        $this->persistSetting($setting);
        $this->entityManager->flush();
    }

    public function saveSettings(array $settingList)
    {
        try
        {
            $this->entityManager->getConnection()->beginTransaction();

            foreach ($settingList as $setting)
                $this->persistSetting($setting);

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    private function persistSetting($setting)
    {
        $settingEntity = $this->getSettingEntity($setting->getId());
        $settingEntity->setValue($setting->getValue());
        $this->entityManager->persist($settingEntity);
    }
}

<?php
/**
 * @package    agitation/settings
 * @link       http://github.com/agitation/AgitSettingsBundle
 * @author     Alex Günsche <http://www.agitsol.com/>
 * @copyright  2012-2015 AGITsol GmbH
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Service;

use Doctrine\ORM\EntityManager;
use Agit\BaseBundle\Exception\InternalErrorException;
use Agit\BaseBundle\Tool\Translate;
use Agit\SettingBundle\Exception\SettingNotFoundException;
use Agit\SettingBundle\Exception\SettingReadonlyException;
use Agit\SettingBundle\Plugin\AbstractSetting;
use Agit\PluggableBundle\Strategy\Entity\EntityLoaderFactory;

class SettingService
{
    private $objectLoader;

    private $entityManager;

    private $settingList = [];

    private $registrationTag = "agit.setting";

    private $entityName = "AgitSettingBundle:Setting";

    public function __construct(EntityLoaderFactory $entityLoaderFactory, EntityManager $entityManager)
    {
        $this->objectLoader = $entityLoaderFactory->create($this->registrationTag, $this->entityName);
        $this->entityManager = $entityManager;
    }

    public function getValueOf($id)
    {
        return $this->getSetting($id)->getValue();
    }

    public function getValuesOf(array $idList)
    {
        $settings = $this->getSettings($idList);

        return array_map(function($setting){
            return $setting->getValue();
        }, $settings);
    }

    public function getSetting($id)
    {
        try
        {
            $setting = $this->objectLoader->getObject($id);
            return $setting;
        }
        catch(\Exception $e)
        {
            throw new SettingNotFoundException(sprintf(Translate::t("Setting `%s` does not exist."), $id));
        }
    }

    public function getSettings(array $idList)
    {
        $settingList = [];

        foreach ($idList as $id)
            $settingList[$id] = $this->getSetting($id);

        return $settingList;
    }

    public function saveSetting(AbstractSetting $setting, $force = false)
    {
        $this->persistSetting($setting, $force);
        $this->entityManager->flush();
    }

    public function saveSettings(array $settingList, $force = false)
    {
        try
        {
            $this->entityManager->getConnection()->beginTransaction();

            foreach ($settingList as $setting)
                $this->persistSetting($setting, $force);

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    private function persistSetting($setting, $force = false)
    {
        if (!$force && $setting->isReadonly())
            throw new SettingReadonlyException(sprintf(Translate::t("Setting `%s` is read-only."), $setting->getId()));

        $entity = $this->getSettingEntity($setting->getId());
        $entity->setValue($setting->getValue());
        $this->entityManager->persist($entity);
    }

    private function getSettingEntity($id)
    {
        $entity = $this->entityManager->find($this->entityName, $id);

        if (!$entity)
            throw new SettingNotFoundException(sprintf(Translate::t("Setting `%s` does not exist."), $id));

        return $entity;
    }
}

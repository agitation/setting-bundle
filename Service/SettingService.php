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
    private $ObjectLoader;

    private $EntityManager;

    private $SettingList = [];

    public function __construct(ObjectLoader $ObjectLoader, EntityManager $EntityManager)
    {
        $this->EntityManager = $EntityManager;
        $this->ObjectLoader = $ObjectLoader;

        $this->ObjectLoader->setObjectFactory(function ($id, $className)
        {
            $SettingEntity = $this->getSettingEntity($id);
            $Setting = new $className($SettingEntity->getValue());
            return $Setting;
        });
    }

    public function getSetting($id)
    {
        try
        {
            $Setting = $this->ObjectLoader->getObject($id);
        }
        catch(\Exception $e)
        {
            throw new SettingNotFoundException(sprintf(Translate::getInstance()->t("Setting `%s` could not be loaded."), $id));
        }

        return $Setting;
    }

    private function getSettingEntity($id)
    {
        $Setting = $this->EntityManager->find('AgitSettingBundle:Setting', $id);

        if (!$Setting)
            throw new SettingNotFoundException(sprintf(Translate::getInstance()->t("Setting `%s` does not exist."), $id));

        return $Setting;
    }

    public function getSettings(array $idList)
    {
        $SettingList = [];

        foreach ($idList as $id)
            $SettingList[] = $this->getSetting($id);

        return $SettingList;
    }

    public function saveSetting(AbstractSetting $Setting, $force = false)
    {
        if (!$force && $Setting->isReadonly())
            throw new SettingReadonlyException(sprintf(Translate::getInstance()->t("Setting `%s` is read-only."), $Setting->getId()));

        $this->persistSetting($Setting);
        $this->EntityManager->flush();
    }

    public function saveSettings(array $SettingList)
    {
        try
        {
            $this->EntityManager->getConnection()->beginTransaction();

            foreach ($SettingList as $Setting)
                $this->persistSetting($Setting);

            $this->EntityManager->flush();
            $this->EntityManager->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $this->EntityManager->getConnection()->rollback();
            throw $e;
        }
    }

    private function persistSetting($Setting)
    {
        $SettingEntity = $this->getSettingEntity($Setting->getId());
        $SettingEntity->setValue($Setting->getValue());
        $this->EntityManager->persist($SettingEntity);
    }
}

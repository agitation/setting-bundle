<?php
declare(strict_types=1);
/*
 * @package    agitation/setting-bundle
 * @link       http://github.com/agitation/setting-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\SettingBundle\Service;

use Agit\IntlBundle\Tool\Translate;
use Agit\SeedBundle\Event\SeedEvent;
use Agit\SettingBundle\Event\SettingsLoadedEvent;
use Agit\SettingBundle\Event\SettingsModifiedEvent;
use Agit\SettingBundle\Exception\InvalidSettingValueException;
use Agit\SettingBundle\Exception\SettingNotFoundException;
use Agit\SettingBundle\Exception\SettingReadonlyException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SettingService
{
    const ENTITY_NAME = 'AgitSettingBundle:Setting';

    const RESULT_CACHE_KEY = 'agit.settings.all';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    private $entities;

    private $settings = [];

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function addSetting(AbstractSetting $setting)
    {
        $this->settings[$setting->getId()] = $setting;
    }

    public function registerSeed(SeedEvent $event)
    {
        foreach ($this->settings as $setting)
        {
            $event->addSeedEntry(self::ENTITY_NAME, [
                'id' => $setting->getId(),
                'value' => $setting->getDefaultValue()
            ]);
        }
    }

    public function getNameOf($id)
    {
        $this->loadSettings();

        return $this->settings[$id]->getName();
    }

    public function getNamesOf(array $idList)
    {
        $this->loadSettings();
        $settings = $this->getSettings($idList);

        return array_map(function ($setting) {
            return $setting->getName();
        }, $settings);
    }

    public function getValueOf($id)
    {
        $this->loadSettings();

        return $this->settings[$id]->getValue();
    }

    public function getValuesOf(array $idList)
    {
        $this->loadSettings();
        $settings = $this->getSettings($idList);

        return array_map(function ($setting) {
            return $setting->getValue();
        }, $settings);
    }

    public function getSettings(array $idList)
    {
        $this->loadSettings();

        $settings = [];

        foreach ($idList as $id)
        {
            if (! isset($this->settings[$id]))
            {
                throw new SettingNotFoundException("The setting `$id` does not exist.");
            }

            $settings[$id] = $this->settings[$id];
        }

        return $settings;
    }

    public function saveSetting($id, $value, $force = false)
    {
        $this->saveSettings([$id => $value], $force);
    }

    public function saveSettings(array $settings, $force = false)
    {
        $this->loadSettings();
        $changedSettings = [];
        $changedSettingNames = [];

        foreach ($settings as $id => $value)
        {
            if (! isset($this->settings[$id]))
            {
                throw new SettingNotFoundException(sprintf(Translate::t('A setting `%s` does not exist.'), $id));
            }

            $setting = $this->settings[$id];

            if (! $force && $setting->isReadonly())
            {
                throw new SettingReadonlyException(sprintf('Setting `%s` is read-only.', $id));
            }

            try
            {
                $oldValue = $setting->getValue();
                $setting->setValue($value); // implicitely validates
                $this->entities[$id]->setValue($value);

                if ($oldValue !== $value)
                {
                    $changedSettings[$id] = ['old' => $oldValue, 'new' => $value];
                    $changedSettingNames[] = $setting->getName();
                }
            }
            catch (Exception $e)
            {
                throw new InvalidSettingValueException(sprintf(
                    Translate::t('Invalid value for “%s”: %s'),
                    $setting->getName(),
                    $e->getMessage()
                ));
            }

            $this->entityManager->persist($this->entities[$id]);
        }

        $this->entityManager->flush();

        if ($changedSettings)
        {
            $this->entityManager->getConfiguration()->getResultCacheImpl()->delete(self::RESULT_CACHE_KEY);

            $this->eventDispatcher->dispatch(
                'agit.settings.modified',
                new SettingsModifiedEvent($this, $changedSettings)
            );

            $this->eventDispatcher->dispatch(
                'agit.settings.loaded',
                new SettingsLoadedEvent($this)
            );
        }
    }

    private function loadSettings()
    {
        if ($this->entities === null)
        {
            $this->entities = $this->entityManager->createQueryBuilder()
                ->select('setting')
                ->from(self::ENTITY_NAME, 'setting', 'setting.id')
                ->getQuery()
                ->useResultCache(true, 86400, self::RESULT_CACHE_KEY)
                ->getResult();

            foreach ($this->settings as $id => $setting)
            {
                $value = isset($this->entities[$id])
                    ? $this->entities[$id]->getValue()
                    : $setting->getDefaultValue();

                $setting->_restoreValue($value);
            }

            $this->eventDispatcher->dispatch(
                'agit.settings.loaded',
                new SettingsLoadedEvent($this)
            );
        }
    }
}

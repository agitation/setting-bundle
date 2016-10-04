<?php

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
use Doctrine\ORM\EntityManager;
use Exception;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Agit\LoggingBundle\Service\Logger;

class SettingService
{
    const ENTITY_NAME = "AgitSettingBundle:Setting";

    private $entityManager;

    private $eventDispatcher;

    private $logger;

    private $entities;

    private $settings = [];

    public function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher, Logger $logger = null)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    public function addSetting(AbstractSetting $setting)
    {
        $this->settings[$setting->getId()] = $setting;
    }

    public function registerSeed(SeedEvent $event)
    {
        foreach ($this->settings as $setting) {
            $event->addSeedEntry(self::ENTITY_NAME, [
                "id"    => $setting->getId(),
                "value" => $setting->getDefaultValue()
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

        foreach ($idList as $id) {
            if (! isset($this->settings[$id])) {
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

        try {
            $this->entityManager->beginTransaction();

            foreach ($settings as $id => $value) {
                if (! isset($this->settings[$id])) {
                    throw new SettingNotFoundException(sprintf(Translate::t("A setting `%s` does not exist."), $id));
                }

                $setting = $this->settings[$id];

                if (! $force && $setting->isReadonly()) {
                    throw new SettingReadonlyException(sprintf("Setting `%s` is read-only.", $id));
                }


                try {
                    $oldValue = $setting->getValue();
                    $setting->setValue($value); // implicitely validates
                    $this->entities[$id]->setValue($value);

                    if ($oldValue !== $value) {
                        $changedSettings[$id] = ["old" => $oldValue, "new" => $value];
                        $changedSettingNames[] = $setting->getName();
                    }

                } catch (Exception $e) {
                    throw new InvalidSettingValueException(sprintf(
                        Translate::t("Invalid value for “%s”: %s"),
                        $setting->getName(),
                        $e->getMessage()
                    ));
                }

                $this->entityManager->persist($this->entities[$id]);
            }

            $this->entityManager->flush();

            if ($this->logger && count($changedSettings)) {
                $this->logger->log(
                    LogLevel::INFO,
                    "agit.settings",
                    sprintf(Translate::tl("The following settings have been changed: %s"), implode(", ", $changedSettingNames)),
                    true
                );
            }

            $this->entityManager->commit();
        } catch (Exception $e) {
            $this->entityManager->rollBack();
            throw $e;
        }

        if ($changedSettings)
        {
            $this->eventDispatcher->dispatch(
                "agit.settings.modified",
                new SettingsModifiedEvent($this, $changedSettings)
            );

            $this->eventDispatcher->dispatch(
                "agit.settings.loaded",
                new SettingsLoadedEvent($this)
            );
        }
    }

    private function loadSettings()
    {
        if (is_null($this->entities)) {
            $this->entities = $this->entityManager->createQueryBuilder()
                ->select("setting")
                ->from(self::ENTITY_NAME, "setting", "setting.id")
                ->getQuery()->getResult();

            foreach ($this->settings as $id => $setting) {
                if (! isset($this->entities[$id])) {
                    throw new SettingNotFoundException(sprintf("Oops, setting `%s` not found in database.", $id));
                }

                $setting->_restoreValue($this->entities[$id]->getValue());
            }

            $this->eventDispatcher->dispatch(
                "agit.settings.loaded",
                new SettingsLoadedEvent($this)
            );
        }
    }
}

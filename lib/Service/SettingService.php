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
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SettingService
{
    const ENTITY_NAME = 'AgitSettingBundle:Setting';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * setting entities loaded from the database
     *
     * @var array|null
     */
    private $entities;

    /**
     * available settings
     *
     * @var array
     */
    private $settings = [];

    /**
     * settings which must be refreshed
     *
     * @var array
     */
    private $pending = [];

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function addSetting(AbstractSetting $setting)
    {
        // we cannot add the setting immediately to the $this->settings array, because
        // it has to be initialized first. And we cannot initialize it here right now,
        // because this could trigger dependencies calling our getValueOf method too
        // early, so we have to defer it until the first actual request.

        $this->pending[$setting->getId()] = $setting;
    }

    public function registerSeed(SeedEvent $event)
    {
        $this->load();

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
        $this->load();

        return $this->settings[$id]->getName();
    }

    public function getSetting($id)
    {
        $this->load();

        if (! isset($this->settings[$id]))
        {
            throw new BadRequestHttpException(sprintf('A setting `%s` does not exist.', $id));
        }

        return $this->settings[$id];
    }

    public function getNamesOf(array $idList)
    {
        $this->load();
        $settings = $this->getSettings($idList);

        return array_map(function ($setting) {
            return $setting->getName();
        }, $settings);
    }

    public function getValueOf($id)
    {
        $this->load();

        return $this->settings[$id]->getValue();
    }

    public function getValuesOf(array $idList)
    {
        $this->load();
        $settings = $this->getSettings($idList);

        return array_map(function ($setting) {
            return $setting->getValue();
        }, $settings);
    }

    public function saveSetting($id, $value, $force = false)
    {
        $this->saveSettings([$id => $value], $force);
    }

    public function saveSettings(array $settings, $force = false)
    {
        $this->load();
        $changedSettings = [];
        $changedSettingNames = [];

        foreach ($settings as $id => $value)
        {
            if (! isset($this->settings[$id]))
            {
                throw new BadRequestHttpException(sprintf('A setting `%s` does not exist.', $id));
            }

            $setting = $this->settings[$id];

            if (! $force && $setting->isReadonly())
            {
                throw new BadRequestHttpException(sprintf('Setting `%s` is read-only.', $id));
            }

            try
            {
                $oldValue = $setting->_getRealValue();
                $setting->setValue($value); // implicitely validates
                $newValue = $setting->_getRealValue();
                $this->entities[$id]->setValue($newValue);

                if ($oldValue !== $newValue)
                {
                    $changedSettings[$id] = ['old' => $oldValue, 'new' => $value];
                    $changedSettingNames[] = $setting->getName();
                }
            }
            catch (Exception $e)
            {
                throw new BadRequestHttpException(sprintf(
                    Translate::t('Invalid value for “%s”: %s'),
                    $setting->getName(),
                    $e->getMessage()
                ));
            }

            $this->entityManager->persist($this->entities[$id]);
        }

        $this->entityManager->flush();
    }

    private function getSettings(array $idList)
    {
        $this->load();

        $settings = [];

        foreach ($idList as $id)
        {
            if (! isset($this->settings[$id]))
            {
                throw new BadRequestHttpException("The setting `$id` does not exist.");
            }

            $settings[$id] = $this->settings[$id];
        }

        return $settings;
    }

    private function load()
    {
        if ($this->entities === null)
        {
            // the following construct helps avoiding exceptions during cache warming
            // when trying to access a not-yet-created database.

            try
            {
                $this->entityManager->getConnection()->ping();

                $this->entities = $this->entityManager->createQueryBuilder()
                    ->select('setting')
                    ->from(self::ENTITY_NAME, 'setting', 'setting.id')
                    ->getQuery()->getResult();
            }
            catch (Exception $e)
            {
                $this->entities = [];
            }
        }

        $loaded = [];

        foreach ($this->pending as $id => $setting)
        {
            $value = isset($this->entities[$id])
                ? $this->entities[$id]->getValue()
                : $setting->getDefaultValue();

            $setting->_setRealValue($value);
            $this->settings[$id] = $loaded[$id] = $setting;
        }

        $this->eventDispatcher->dispatch('agit.settings.loaded', new SettingsLoadedEvent($loaded));

        $this->pending = [];
    }
}

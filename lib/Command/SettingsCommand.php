<?php
declare(strict_types=1);

namespace Agit\SettingBundle\Command;

use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SettingsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('agit:settings')
            ->setDescription('create or update settings')
            ->addArgument('settings', InputArgument::REQUIRED, 'settings as JSON string')
            ->addOption('force', "f", InputOption::VALUE_NONE, 'force writing of “readonly” settings');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $settings = json_decode($input->getArgument("settings"), true);

        if (!is_array($settings))
            throw new RuntimeException("A valid JSON object must be passed for settings.");

        // the values themselves are validated by the settings service

        $this->getContainer()->get("agit.setting")->saveSettings($settings, $input->getOption("force"));
    }
}

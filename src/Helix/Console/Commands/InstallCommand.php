<?php

namespace Helix\Console\Commands;

use Helix\Installer\Installer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'install', description: 'Run the first-time setup (PHP checks, permissions, database, .env)')]
class InstallCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $installer = new Installer(getcwd());
        return $installer->run() ? Command::SUCCESS : Command::FAILURE;
    }
}

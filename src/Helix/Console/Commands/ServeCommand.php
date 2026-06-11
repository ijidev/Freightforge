<?php

namespace Helix\Console\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'serve', description: 'Start the Helix-Forge development server')]
class ServeCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host address', '127.0.0.1')
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'Port number', '8080');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $host = $input->getOption('host');
        $port = $input->getOption('port');

        $output->writeln("<info>Helix-Forge development server started:</info> http://{$host}:{$port}");
        $output->writeln("<comment>Press Ctrl+C to stop.</comment>");

        $publicDir = getcwd() . '/public';

        $command = sprintf(
            'php -S %s:%s -t %s %s/index.php',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($publicDir),
            escapeshellarg($publicDir)
        );

        passthru($command);

        return Command::SUCCESS;
    }
}

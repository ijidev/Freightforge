<?php

namespace Helix\Console\Commands;

use Helix\Foundation\Application;
use Helix\Routing\RouteCompiler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cache:routes', description: 'Compile and cache route attributes to plain PHP')]
class CacheRoutesCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $app = Application::create();
        $app->boot();

        $router = $app->getContainer()->get(\Helix\Routing\Router::class);
        $routes = $router->compileRoutes();

        $compiler = new RouteCompiler();
        $compiler->writeCache('routes', $routes);

        $count = count($routes);
        $output->writeln("<info>Routes cached successfully.</info> {$count} routes compiled.");

        return Command::SUCCESS;
    }
}

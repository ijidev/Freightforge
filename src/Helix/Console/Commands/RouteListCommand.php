<?php

namespace Helix\Console\Commands;

use Helix\Foundation\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

#[AsCommand(name: 'route:list', description: 'List all registered routes')]
class RouteListCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $app = Application::create();
        $app->boot();

        $router = $app->getContainer()->get(\Helix\Routing\Router::class);
        $routes = $router->compileRoutes();

        if (empty($routes)) {
            $output->writeln('<comment>No routes registered.</comment>');
            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['Method', 'Path', 'Name', 'Handler']);

        foreach ($routes as $route) {
            $table->addRow([
                $route['method'],
                $route['path'],
                $route['name'] ?? '-',
                $route['handler'],
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}

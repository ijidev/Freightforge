<?php

namespace Helix\Console;

use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication
{
    public function __construct()
    {
        parent::__construct('Helix-Forge', '1.0.0');

        $this->addCommands([
            new Commands\InstallCommand(),
            new Commands\ServeCommand(),
            new Commands\MakeControllerCommand(),
            new Commands\MakeModelCommand(),
            new Commands\RouteListCommand(),
            new Commands\CacheRoutesCommand(),
        ]);
    }
}

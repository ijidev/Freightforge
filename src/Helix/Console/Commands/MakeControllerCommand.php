<?php

namespace Helix\Console\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:controller', description: 'Create a new controller')]
class MakeControllerCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Controller class name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $className = str_contains($name, 'Controller') ? $name : $name . 'Controller';

        $path = getcwd() . '/app/Http/Controllers/' . $className . '.php';

        if (file_exists($path)) {
            $output->writeln("<error>Controller {$className} already exists!</error>");
            return Command::FAILURE;
        }

        $stub = <<<PHP
<?php

namespace App\Http\Controllers;

use Helix\Http\JsonResponse;
use Helix\Http\Request;
use Helix\Routing\Attributes\Route;

class {$className}
{
    #[Route('/{$this->toKebab($className)}', method: 'GET')]
    public function index(Request \$request): JsonResponse
    {
        return new JsonResponse(['message' => 'Hello from {$className}!']);
    }
}

PHP;

        file_put_contents($path, $stub);
        $output->writeln("<info>Created controller:</info> app/Http/Controllers/{$className}.php");

        return Command::SUCCESS;
    }

    private function toKebab(string $name): string
    {
        $name = preg_replace('/Controller$/', '', $name);
        $parts = preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY);
        return strtolower(implode('-', $parts));
    }
}

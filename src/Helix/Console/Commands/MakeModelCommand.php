<?php

namespace Helix\Console\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:model', description: 'Create a new domain model and repository')]
class MakeModelCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Model class name')
            ->addArgument('fields', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Fields (name:type)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $fields = $input->getArgument('fields') ?? [];

        $domainDir = getcwd() . '/app/Domain/' . $name;
        if (!is_dir($domainDir)) {
            mkdir($domainDir, 0775, true);
        }

        $entityCode = $this->buildEntity($name, $fields);
        file_put_contents($domainDir . '/' . $name . '.php', $entityCode);
        $output->writeln("<info>Created model:</info> app/Domain/{$name}/{$name}.php");

        $repoCode = $this->buildRepository($name);
        file_put_contents($domainDir . '/' . $name . 'Repository.php', $repoCode);
        $output->writeln("<info>Created repository:</info> app/Domain/{$name}/{$name}Repository.php");

        return Command::SUCCESS;
    }

    private function buildEntity(string $name, array $fields): string
    {
        $table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name)) . 's';
        $properties = '';
        $imports = "use Helix\Database\Attributes\Entity;\nuse Helix\Database\Attributes\Column;\n";

        if (!empty($fields)) {
            foreach ($fields as $field) {
                $parts = explode(':', $field);
                $fieldName = $parts[0];
                $fieldType = $parts[1] ?? 'string';
                $colType = match ($fieldType) {
                    'int', 'integer' => 'integer',
                    'float', 'double', 'decimal' => 'decimal',
                    'bool', 'boolean' => 'boolean',
                    'text' => 'text',
                    'datetime' => 'datetime',
                    default => 'string',
                };
                $extra = match ($colType) {
                    'string' => ', length: 255',
                    'decimal' => ', precision: 10, scale: 2',
                    default => '',
                };
                $properties .= <<<PROP

    #[Column(type: '{$colType}'{$extra})]
    public {$fieldType} \${$fieldName};

PROP;
            }
        } else {
            $properties = <<<PROP

    #[Column(type: 'string', length: 255)]
    public string \$name;

    #[Column(type: 'string', unique: true)]
    public string \$email;

    #[Column(type: 'datetime', nullable: true)]
    public ?\DateTime \$created_at = null;

PROP;
        }

        return <<<PHP
<?php

namespace App\Domain\\{$name};

{$imports}
#[Entity(table: '{$table}')]
class {$name}
{
    #[Column(type: 'id')]
    public int \$id;
{$properties}
}

PHP;
    }

    private function buildRepository(string $name): string
    {
        $table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name)) . 's';

        return <<<PHP
<?php

namespace App\Domain\\{$name};

use Helix\Database\Repository;

class {$name}Repository extends Repository
{
    protected string \$table = '{$table}';
    protected string \$entityClass = {$name}::class;
}

PHP;
    }
}

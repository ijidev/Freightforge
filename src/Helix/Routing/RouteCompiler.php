<?php

namespace Helix\Routing;

class RouteCompiler
{
    private const CACHE_DIR = __DIR__ . '/../../../storage/framework/cache';

    public function compile(array $routes): string
    {
        $compiled = "<?php\n\nreturn " . var_export($routes, true) . ";\n";
        return str_replace("'Closure'", "'Closure'", $compiled);
    }

    public function writeCache(string $key, array $routes): void
    {
        $dir = self::CACHE_DIR;
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        file_put_contents(
            $dir . '/' . $key . '.php',
            $this->compile($routes)
        );
    }

    public function loadCache(string $key): ?array
    {
        $path = self::CACHE_DIR . '/' . $key . '.php';
        if (file_exists($path)) {
            return require $path;
        }
        return null;
    }

    public static function clearCache(): void
    {
        $dir = self::CACHE_DIR;
        if (is_dir($dir)) {
            $files = glob($dir . '/*.php');
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }
}

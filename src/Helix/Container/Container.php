<?php

namespace Helix\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;

class Container implements ContainerInterface, PsrContainerInterface
{
    private array $bindings = [];
    private array $instances = [];
    private array $aliases = [];
    private static ?Container $instance = null;

    public function __construct()
    {
        static::$instance = $this;
        $this->instances[ContainerInterface::class] = $this;
        $this->instances[PsrContainerInterface::class] = $this;
        $this->instances[static::class] = $this;
    }

    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function get(string $id): mixed
    {
        $id = $this->resolveAlias($id);

        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (array_key_exists($id, $this->bindings)) {
            $concrete = $this->bindings[$id];
            $object = $concrete($this);
            return $object;
        }

        if (class_exists($id)) {
            return $this->autoWire($id);
        }

        throw new NotFoundException("No binding or class found for: {$id}");
    }

    public function has(string $id): bool
    {
        $id = $this->resolveAlias($id);
        return isset($this->instances[$id]) || isset($this->bindings[$id]) || class_exists($id);
    }

    public function set(string $id, mixed $concrete): void
    {
        if (is_array($concrete) || (is_object($concrete) && !is_callable($concrete))) {
            $this->instances[$id] = $concrete;
            return;
        }

        if (is_string($concrete) && class_exists($concrete)) {
            $this->bindings[$id] = fn(self $c) => $c->autoWire($concrete);
            return;
        }

        $this->bindings[$id] = $concrete;
    }

    public function singleton(string $id, mixed $concrete): void
    {
        if (is_array($concrete) || (is_object($concrete) && !is_callable($concrete))) {
            $this->instances[$id] = $concrete;
            return;
        }

        $factory = is_string($concrete) && class_exists($concrete)
            ? fn(self $c) => $c->autoWire($concrete)
            : $concrete;

        $this->bindings[$id] = function ($c) use ($factory, $id) {
            $object = $factory($c);
            $this->instances[$id] = $object;
            return $object;
        };
    }

    public function alias(string $alias, string $id): void
    {
        $this->aliases[$alias] = $id;
    }

    public function autoWire(string $class): object
    {
        $reflection = new \ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new ContainerException("Cannot instantiate abstract class: {$class}");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $paramType = $parameter->getType();

            if ($paramType === null || ($paramType instanceof \ReflectionNamedType && $paramType->isBuiltin())) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new ContainerException("Cannot resolve parameter \${$parameter->getName()} for {$class}");
                }
            } elseif ($paramType instanceof \ReflectionNamedType) {
                $typeName = $paramType->getName();

                if ($typeName === 'array' && $parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    $dependencies[] = $this->get($typeName);
                }
            } else {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new ContainerException("Cannot resolve parameter \${$parameter->getName()} for {$class}");
                }
            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    private function resolveAlias(string $id): string
    {
        while (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }
        return $id;
    }
}

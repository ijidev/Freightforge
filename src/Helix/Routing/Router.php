<?php

namespace Helix\Routing;

use Helix\Container\Container;
use Helix\Http\Request;
use Helix\Http\Response;
use Helix\Routing\Attributes\Route as RouteAttribute;

class Router
{
    private RouteCollection $collection;
    private array $controllers = [];
    private bool $compiled = false;

    public function __construct(
        private readonly Container $container
    ) {
        $this->collection = new RouteCollection();
    }

    public function registerController(string $controllerClass): void
    {
        $this->controllers[] = $controllerClass;
    }

    public function registerControllers(array $controllerClasses): void
    {
        foreach ($controllerClasses as $class) {
            $this->registerController($class);
        }
    }

    public function scan(): void
    {
        if ($this->compiled) {
            return;
        }
        foreach ($this->controllers as $controllerClass) {
            $this->scanController($controllerClass);
        }
        $this->compiled = true;
    }

    private function scanController(string $controllerClass): void
    {
        $reflection = new \ReflectionClass($controllerClass);

        foreach ($reflection->getMethods() as $method) {
            $attributes = $method->getAttributes(RouteAttribute::class);

            foreach ($attributes as $attribute) {
                $route = $attribute->newInstance();

                $this->collection->add(
                    $route->method,
                    $route->path,
                    [$controllerClass, $method->getName()],
                    $route->name
                );
            }
        }
    }

    public function add(string $method, string $path, callable|array $handler, ?string $name = null): void
    {
        $this->collection->add($method, $path, $handler, $name);
    }

    public function dispatch(Request $request): Response
    {
        if (!$this->compiled) {
            $this->scan();
            $this->compiled = true;
        }

        $match = $this->collection->match($request->method(), $request->uri());

        if ($match === null) {
            throw new RouteNotFoundException($request->method(), $request->uri());
        }

        foreach ($match['params'] as $key => $value) {
            $request->setRouteParam($key, $value);
        }

        return $this->resolveHandler($match['handler'], $request);
    }

    public function resolveHandler(callable|array $handler, Request $request): Response
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $instance = $this->container->get($class);
            $result = $this->callMethod($instance, $method, $request);
        } else {
            $result = $handler($request);
        }

        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result) || is_object($result)) {
            return new \Helix\Http\JsonResponse($result);
        }

        return Response::html((string) $result);
    }

    private function callMethod(object $instance, string $method, Request $request): mixed
    {
        $reflection = new \ReflectionMethod($instance, $method);
        $parameters = $reflection->getParameters();
        $args = [];

        foreach ($parameters as $param) {
            $paramType = $param->getType();
            $paramName = $param->getName();

            if ($paramType instanceof \ReflectionNamedType) {
                $typeName = $paramType->getName();

                if ($typeName === Request::class) {
                    $args[] = $request;
                } elseif ($typeName === \Helix\Http\JsonResponse::class || is_subclass_of($typeName, Response::class)) {
                    $args[] = $this->container->get($typeName);
                } elseif (class_exists($typeName)) {
                    $args[] = $this->container->get($typeName);
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    $args[] = $request->routeParam($paramName);
                }
            } else {
                $args[] = $request->routeParam($paramName, $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null);
            }
        }

        return $reflection->invokeArgs($instance, $args);
    }

    public function getCollection(): RouteCollection
    {
        return $this->collection;
    }

    public function compileRoutes(): array
    {
        $this->scan();
        return $this->collection->toArray();
    }

    public function loadCompiled(array $routes): void
    {
        foreach ($routes as $route) {
            $this->collection->addRoute($route);
        }
        $this->compiled = true;
    }
}

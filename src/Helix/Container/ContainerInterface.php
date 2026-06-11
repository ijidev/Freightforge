<?php

namespace Helix\Container;

interface ContainerInterface
{
    public function get(string $id): mixed;
    public function has(string $id): bool;
}

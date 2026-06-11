<?php

namespace App\Domain\Product;

use Helix\Database\Attributes\Entity;
use Helix\Database\Attributes\Column;

#[Entity(table: 'products')]
class Product
{
    #[Column(type: 'id')]
    public int $id;

    #[Column(type: 'string', length: 255)]
    public string $name;

    #[Column(type: 'string', length: 255)]
    public float $price;

}

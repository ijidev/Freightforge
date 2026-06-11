<?php

namespace App\Domain\Product;

use Helix\Database\Repository;

class ProductRepository extends Repository
{
    protected string $table = 'products';
    protected string $entityClass = Product::class;
}

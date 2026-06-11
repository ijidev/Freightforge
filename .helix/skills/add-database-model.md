# Skill: Add a Database Model

## Steps

1. **Generate model and repository**:
```bash
php helix make:model ModelName field1:type field2:type
```

2. **Define the entity** with attributes in `app/Domain/ModelName/ModelName.php`:
```php
#[Entity(table: 'model_names')]
class ModelName
{
    #[Column(type: 'id')]
    public int $id;

    #[Column(type: 'string', length: 255)]
    public string $name;
}
```

3. **Use the repository** in your controller:
```php
class ModelNameController
{
    public function __construct(private ModelNameRepository $repo) {}
}
```

## Column Types

| PHP Type | Column Type | Attributes |
|----------|-------------|-----------|
| `int` | `id` | Auto-increment primary key |
| `string` | `string` | `length: 255` |
| `string` | `text` | Large text content |
| `int` | `integer` | Whole number |
| `float` | `decimal` | Decimal number |
| `bool` | `boolean` | True/false |
| `\DateTime` | `datetime` | Date + time |
| `?type` | any | Add `nullable: true` |

## Example

```bash
php helix make:model Product name:string price:float description:text
```

```php
#[Entity(table: 'products')]
class Product
{
    #[Column(type: 'id')]
    public int $id;

    #[Column(type: 'string', length: 255)]
    public string $name;

    #[Column(type: 'decimal')]
    public float $price;

    #[Column(type: 'text')]
    public string $description;
}
```

## Repository Usage

```php
$repo = new ProductRepository();
$all = $repo->findAll();
$one = $repo->findById(1);
$created = $repo->create(['name' => 'Widget', 'price' => 9.99]);
$updated = $repo->update(1, ['name' => 'Widget Pro']);
$repo->delete(1);
```

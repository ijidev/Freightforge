<?php

namespace App\Domain\Shipment;

use Helix\Database\Attributes\Column;
use Helix\Database\Attributes\Entity;

#[Entity(table: 'shipments')]
class Shipment
{
    #[Column(type: 'id')]
    public int $id;

    #[Column(type: 'string', unique: true)]
    public string $tracking_number;

    #[Column(type: 'string', length: 255)]
    public string $origin;

    #[Column(type: 'string', length: 255)]
    public string $destination;

    #[Column(type: 'string', length: 50)]
    public string $status;

    #[Column(type: 'string', length: 255)]
    public string $sender_name;

    #[Column(type: 'string', length: 255)]
    public string $sender_email;

    #[Column(type: 'string', length: 50, nullable: true)]
    public ?string $sender_phone = null;

    #[Column(type: 'string', length: 255)]
    public string $recipient_name;

    #[Column(type: 'string', length: 255)]
    public string $recipient_email;

    #[Column(type: 'string', length: 50, nullable: true)]
    public ?string $recipient_phone = null;

    #[Column(type: 'float', nullable: true)]
    public ?float $weight = null;

    #[Column(type: 'text', nullable: true)]
    public ?string $description = null;

    #[Column(type: 'datetime', nullable: true)]
    public ?\DateTime $created_at = null;

    #[Column(type: 'datetime', nullable: true)]
    public ?\DateTime $updated_at = null;
}

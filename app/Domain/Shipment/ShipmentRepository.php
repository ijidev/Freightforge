<?php

namespace App\Domain\Shipment;

use Helix\Database\Repository;

class ShipmentRepository extends Repository
{
    protected string $table = 'shipments';
    protected string $entityClass = Shipment::class;

    public function findByTrackingNumber(string $number): ?array
    {
        return $this->findOneBy('tracking_number', $number);
    }

    public function findByStatus(string $status): array
    {
        return $this->findBy('status', $status);
    }

    public function generateTrackingNumber(): string
    {
        $prefix = 'FF';
        $timestamp = time();
        $random = strtoupper(substr(uniqid(), -4));
        return "{$prefix}-{$timestamp}-{$random}";
    }
}

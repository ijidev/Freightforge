<?php

namespace App\Domain\ShipmentStatus;

use Helix\Database\Repository;

class ShipmentStatusRepository extends Repository
{
    protected string $table = 'shipment_statuses';

    public function findByShipment(int $shipmentId): array
    {
        return $this->findBy('shipment_id', $shipmentId);
    }

    public function findByShipmentOrdered(int $shipmentId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE shipment_id = ? ORDER BY created_at ASC"
        );
        $stmt->execute([$shipmentId]);
        return $stmt->fetchAll();
    }

    public function addStatus(int $shipmentId, string $status, string $remark = ''): array
    {
        return $this->create([
            'shipment_id' => $shipmentId,
            'status' => $status,
            'remark' => $remark,
        ]);
    }
}

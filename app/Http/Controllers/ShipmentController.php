<?php

namespace App\Http\Controllers;

use App\Domain\Shipment\ShipmentRepository;
use App\Domain\ShipmentStatus\ShipmentStatusRepository;
use App\Domain\Status\StatusRepository;
use App\Domain\Setting\SettingRepository;
use Helix\Http\JsonResponse;
use Helix\Http\Request;
use Helix\Http\Response;
use Helix\Routing\Attributes\Route;
use Helix\View\Template;

class ShipmentController
{
    private Template $template;

    public function __construct()
    {
        $this->template = new Template();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function requireAuth(): ?Response
    {
        if (empty($_SESSION['admin_logged_in'])) {
            return Response::redirect('/admin/login');
        }
        return null;
    }

    private function siteName(): string
    {
        try {
            $repo = new SettingRepository();
            return $repo->get('site_name', 'FreightForge');
        } catch (\Throwable) {
            return 'FreightForge';
        }
    }

    private function render(string $view, array $data = []): string
    {
        $data['siteName'] = $this->siteName();
        $data['adminUser'] = $_SESSION['admin_user'] ?? null;
        return $this->template->render($view, $data);
    }

    private function getAvailableStatuses(): array
    {
        try {
            $repo = new StatusRepository();
            return $repo->findAllOrdered();
        } catch (\Throwable) {
            return [];
        }
    }

    private function getStatusHistory(int $shipmentId): array
    {
        try {
            $repo = new ShipmentStatusRepository();
            return $repo->findByShipmentOrdered($shipmentId);
        } catch (\Throwable) {
            return [];
        }
    }

    #[Route('/admin/shipments', method: 'GET')]
    public function index(Request $request): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $repo = new ShipmentRepository();
        $statuses = $this->getAvailableStatuses();

        $status = $request->input('status');
        $search = $request->input('search');

        if ($status) {
            $shipments = $repo->findByStatus($status);
        } else {
            $shipments = $repo->findAll();
        }

        if ($search) {
            $shipments = array_filter($shipments, function ($s) use ($search) {
                $q = strtolower($search);
                return str_contains(strtolower($s['tracking_number']), $q)
                    || str_contains(strtolower($s['origin']), $q)
                    || str_contains(strtolower($s['destination']), $q)
                    || str_contains(strtolower($s['sender_name']), $q)
                    || str_contains(strtolower($s['recipient_name']), $q);
            });
        }

        $flash = $this->getFlash();

        $html = $this->render('admin.shipments', [
            'page' => 'shipments',
            'title' => 'Shipments',
            'shipments' => $shipments,
            'statusFilter' => $status,
            'searchQuery' => $search,
            'availableStatuses' => $statuses,
            'flash' => $flash,
        ]);

        return Response::html($html);
    }

    #[Route('/admin/shipments/create', method: 'GET')]
    public function create(): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $html = $this->render('admin.shipment-form', [
            'page' => 'shipments',
            'title' => 'Create Shipment',
            'shipment' => null,
            'availableStatuses' => $this->getAvailableStatuses(),
            'statusHistory' => [],
        ]);

        return Response::html($html);
    }

    #[Route('/admin/shipments', method: 'POST')]
    public function store(Request $request): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $repo = new ShipmentRepository();
        $statusHistoryRepo = new ShipmentStatusRepository();
        $trackingNumber = $repo->generateTrackingNumber();

        $status = $request->input('status', 'pending');

        $data = [
            'tracking_number' => $trackingNumber,
            'origin' => $request->input('origin', ''),
            'destination' => $request->input('destination', ''),
            'status' => $status,
            'sender_name' => $request->input('sender_name', ''),
            'sender_email' => $request->input('sender_email', ''),
            'sender_phone' => $request->input('sender_phone', ''),
            'recipient_name' => $request->input('recipient_name', ''),
            'recipient_email' => $request->input('recipient_email', ''),
            'recipient_phone' => $request->input('recipient_phone', ''),
            'weight' => $request->input('weight'),
            'description' => $request->input('description', ''),
            'created_at' => date('c'),
            'updated_at' => date('c'),
        ];

        $shipment = $repo->create($data);

        $statusHistoryRepo->addStatus($shipment['id'], $status, 'Shipment created');

        $this->setFlash('success', "Shipment {$trackingNumber} created successfully.");
        return Response::redirect('/admin/shipments');
    }

    #[Route('/admin/shipments/{id}', method: 'GET')]
    public function show(string $id): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $repo = new ShipmentRepository();
        $shipment = $repo->findById((int) $id);

        if (!$shipment) {
            $this->setFlash('error', 'Shipment not found.');
            return Response::redirect('/admin/shipments');
        }

        $html = $this->render('admin.shipment-detail', [
            'page' => 'shipments',
            'title' => 'Shipment Detail',
            'shipment' => $shipment,
            'availableStatuses' => $this->getAvailableStatuses(),
            'statusHistory' => $this->getStatusHistory((int) $id),
        ]);

        return Response::html($html);
    }

    #[Route('/admin/shipments/{id}/edit', method: 'GET')]
    public function edit(string $id): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $repo = new ShipmentRepository();
        $shipment = $repo->findById((int) $id);

        if (!$shipment) {
            $this->setFlash('error', 'Shipment not found.');
            return Response::redirect('/admin/shipments');
        }

        $html = $this->render('admin.shipment-form', [
            'page' => 'shipments',
            'title' => 'Edit Shipment',
            'shipment' => $shipment,
            'availableStatuses' => $this->getAvailableStatuses(),
            'statusHistory' => $this->getStatusHistory((int) $id),
        ]);

        return Response::html($html);
    }

    #[Route('/admin/shipments/{id}', method: 'POST')]
    public function update(Request $request, string $id): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $repo = new ShipmentRepository();
        $statusHistoryRepo = new ShipmentStatusRepository();
        $shipment = $repo->findById((int) $id);

        if (!$shipment) {
            $this->setFlash('error', 'Shipment not found.');
            return Response::redirect('/admin/shipments');
        }

        $newStatus = $request->input('status', 'pending');

        $data = [
            'origin' => $request->input('origin', ''),
            'destination' => $request->input('destination', ''),
            'status' => $newStatus,
            'sender_name' => $request->input('sender_name', ''),
            'sender_email' => $request->input('sender_email', ''),
            'sender_phone' => $request->input('sender_phone', ''),
            'recipient_name' => $request->input('recipient_name', ''),
            'recipient_email' => $request->input('recipient_email', ''),
            'recipient_phone' => $request->input('recipient_phone', ''),
            'weight' => $request->input('weight'),
            'description' => $request->input('description', ''),
            'updated_at' => date('c'),
        ];

        $repo->update((int) $id, $data);

        if ($newStatus !== $shipment['status']) {
            $statusHistoryRepo->addStatus((int) $id, $newStatus, 'Status changed from ' . str_replace('_', ' ', $shipment['status']));
        }

        $this->setFlash('success', "Shipment {$shipment['tracking_number']} updated successfully.");
        return Response::redirect('/admin/shipments/' . $id);
    }

    #[Route('/admin/shipments/{id}/status', method: 'POST')]
    public function addStatusUpdate(Request $request, string $id): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $repo = new ShipmentRepository();
        $statusHistoryRepo = new ShipmentStatusRepository();
        $shipment = $repo->findById((int) $id);

        if (!$shipment) {
            $this->setFlash('error', 'Shipment not found.');
            return Response::redirect('/admin/shipments');
        }

        $newStatus = $request->input('status', '');
        $remark = $request->input('remark', '');

        if (!$newStatus) {
            $this->setFlash('error', 'Status is required.');
            return Response::redirect('/admin/shipments/' . $id . '/edit');
        }

        $statusHistoryRepo->addStatus((int) $id, $newStatus, $remark);

        $repo->update((int) $id, [
            'status' => $newStatus,
            'updated_at' => date('c'),
        ]);

        $this->setFlash('success', 'Status update added.');
        return Response::redirect('/admin/shipments/' . $id);
    }

    #[Route('/admin/shipments/{id}/delete', method: 'POST')]
    public function destroy(string $id): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $repo = new ShipmentRepository();
        $shipment = $repo->findById((int) $id);

        if ($shipment) {
            $repo->delete((int) $id);
            $this->setFlash('success', "Shipment {$shipment['tracking_number']} deleted.");
        } else {
            $this->setFlash('error', 'Shipment not found.');
        }

        return Response::redirect('/admin/shipments');
    }

    // Shipment Settings page
    #[Route('/admin/shipment-settings', method: 'GET')]
    public function settings(): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $repo = new StatusRepository();
        $statuses = $repo->findAllOrdered();
        $flash = $this->getFlash();

        $html = $this->render('admin.shipment-settings', [
            'page' => 'shipment-settings',
            'title' => 'Shipment Settings',
            'statuses' => $statuses,
            'flash' => $flash,
        ]);

        return Response::html($html);
    }

    private function slugify(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
        return trim($slug, '_');
    }

    // Admin statuses management
    #[Route('/admin/statuses', method: 'POST')]
    public function statusesStore(Request $request): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $name = $request->input('name', '');
        $slug = $this->slugify($name);
        $color = $request->input('color', 'blue');
        $sortOrder = (int) $request->input('sort_order', 0);

        if (!$name) {
            $this->setFlash('error', 'Status name is required.');
            return Response::redirect('/admin/shipment-settings');
        }

        $repo = new StatusRepository();
        if ($repo->exists($slug)) {
            $this->setFlash('error', "Status '{$name}' already exists.");
            return Response::redirect('/admin/shipment-settings');
        }

        $repo->create([
            'name' => $name,
            'slug' => $slug,
            'color' => $color,
            'sort_order' => $sortOrder,
        ]);

        $this->setFlash('success', "Status '{$name}' created.");
        return Response::redirect('/admin/shipment-settings');
    }

    #[Route('/admin/statuses/{id}', method: 'POST')]
    public function statusesUpdate(Request $request, string $id): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $repo = new StatusRepository();
        $status = $repo->findById((int) $id);

        if (!$status) {
            $this->setFlash('error', 'Status not found.');
            return Response::redirect('/admin/shipment-settings');
        }

        $name = $request->input('name', '');
        $color = $request->input('color', 'blue');
        $sortOrder = (int) $request->input('sort_order', 0);

        if (!$name) {
            $this->setFlash('error', 'Status name is required.');
            return Response::redirect('/admin/shipment-settings');
        }

        $newSlug = $this->slugify($name);
        if ($newSlug !== $status['slug'] && $repo->exists($newSlug)) {
            $this->setFlash('error', "A status with the name '{$name}' already exists.");
            return Response::redirect('/admin/shipment-settings');
        }

        $repo->update((int) $id, [
            'name' => $name,
            'slug' => $newSlug,
            'color' => $color,
            'sort_order' => $sortOrder,
        ]);

        $this->setFlash('success', "Status '{$name}' updated.");
        return Response::redirect('/admin/shipment-settings');
    }

    #[Route('/admin/statuses/{id}/delete', method: 'POST')]
    public function statusesDestroy(string $id): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $repo = new StatusRepository();
        $repo->delete((int) $id);

        $this->setFlash('success', 'Status deleted.');
        return Response::redirect('/admin/shipment-settings');
    }

    #[Route('/api/shipments/track', method: 'GET')]
    public function trackApi(Request $request): JsonResponse
    {
        $number = $request->input('number');

        if (!$number) {
            return new JsonResponse(['error' => 'Tracking number is required.'], 400);
        }

        $repo = new ShipmentRepository();
        $shipment = $repo->findByTrackingNumber($number);

        if (!$shipment) {
            return new JsonResponse(['error' => 'Shipment not found.'], 404);
        }

        $statusHistory = $this->getStatusHistory($shipment['id']);

        return new JsonResponse([
            'success' => true,
            'shipment' => $shipment,
            'status_history' => $statusHistory,
        ]);
    }

    private function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    private function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}

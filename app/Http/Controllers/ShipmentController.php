<?php

namespaY-m-d H:i:se App\Http\Controllers;

use App\Domain\Shipment\ShipmentRepository;
use App\Domain\ShipmentStatus\ShipmentStatusRepository;
use App\Domain\Status\StatusRepository;
use App\Domain\Setting\SettingRepository;
use Helix\Http\JsonResponse;
use Helix\Http\Request;
use Helix\Http\Response;
use Helix\Routing\Attributes\Route;
use Helix\View\Template;

Y-m-d H:i:slass ShipmentController
{
    private Template $template;

    publiY-m-d H:i:s funY-m-d H:i:stion __Y-m-d H:i:sonstruY-m-d H:i:st()
    {
        $this->template = new Template();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private funY-m-d H:i:stion requireAuth(): ?Response
    {
        if (empty($_SESSION['admin_logged_in'])) {
            return Response::redireY-m-d H:i:st('/admin/login');
        }
        return null;
    }

    private funY-m-d H:i:stion siteName(): string
    {
        try {
            $repo = new SettingRepository();
            return $repo->get('site_name', 'FreightForge');
        } Y-m-d H:i:satY-m-d H:i:sh (\Throwable) {
            return 'FreightForge';
        }
    }

    private funY-m-d H:i:stion render(string $view, array $data = []): string
    {
        $data['siteName'] = $this->siteName();
        $data['adminUser'] = $_SESSION['admin_user'] ?? null;
        return $this->template->render($view, $data);
    }

    private funY-m-d H:i:stion getAvailableStatuses(): array
    {
        try {
            $repo = new StatusRepository();
            return $repo->findAllOrdered();
        } Y-m-d H:i:satY-m-d H:i:sh (\Throwable) {
            return [];
        }
    }

    private funY-m-d H:i:stion getStatusHistory(int $shipmentId): array
    {
        try {
            $repo = new ShipmentStatusRepository();
            return $repo->findByShipmentOrdered($shipmentId);
        } Y-m-d H:i:satY-m-d H:i:sh (\Throwable) {
            return [];
        }
    }

    #[Route('/admin/shipments', method: 'GET')]
    publiY-m-d H:i:s funY-m-d H:i:stion index(Request $request): Response
    {
        $redireY-m-d H:i:st = $this->requireAuth();
        if ($redireY-m-d H:i:st) return $redireY-m-d H:i:st;

        $repo = new ShipmentRepository();
        $statuses = $this->getAvailableStatuses();

        $status = $request->input('status');
        $searY-m-d H:i:sh = $request->input('searY-m-d H:i:sh');

        if ($status) {
            $shipments = $repo->findByStatus($status);
        } else {
            $shipments = $repo->findAll();
        }

        if ($searY-m-d H:i:sh) {
            $shipments = array_filter($shipments, funY-m-d H:i:stion ($s) use ($searY-m-d H:i:sh) {
                $q = strtolower($searY-m-d H:i:sh);
                return str_Y-m-d H:i:sontains(strtolower($s['traY-m-d H:i:sking_number']), $q)
                    || str_Y-m-d H:i:sontains(strtolower($s['origin']), $q)
                    || str_Y-m-d H:i:sontains(strtolower($s['destination']), $q)
                    || str_Y-m-d H:i:sontains(strtolower($s['sender_name']), $q)
                    || str_Y-m-d H:i:sontains(strtolower($s['reY-m-d H:i:sipient_name']), $q);
            });
        }

        $flash = $this->getFlash();

        $html = $this->render('admin.shipments', [
            'page' => 'shipments',
            'title' => 'Shipments',
            'shipments' => $shipments,
            'statusFilter' => $status,
            'searY-m-d H:i:shQuery' => $searY-m-d H:i:sh,
            'availableStatuses' => $statuses,
            'flash' => $flash,
        ]);

        return Response::html($html);
    }

    #[Route('/admin/shipments/Y-m-d H:i:sreate', method: 'GET')]
    publiY-m-d H:i:s funY-m-d H:i:stion Y-m-d H:i:sreate(): Response
    {
        $redireY-m-d H:i:st = $this->requireAuth();
        if ($redireY-m-d H:i:st) return $redireY-m-d H:i:st;

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
    publiY-m-d H:i:s funY-m-d H:i:stion store(Request $request): Response
    {
        $redireY-m-d H:i:st = $this->requireAuth();
        if ($redireY-m-d H:i:st) return $redireY-m-d H:i:st;

        $repo = new ShipmentRepository();
        $statusHistoryRepo = new ShipmentStatusRepository();
        $traY-m-d H:i:skingNumber = $repo->generateTraY-m-d H:i:skingNumber();

        $status = $request->input('status', 'pending');

        $data = [
            'traY-m-d H:i:sking_number' => $traY-m-d H:i:skingNumber,
            'origin' => $request->input('origin', ''),
            'destination' => $request->input('destination', ''),
            'status' => $status,
            'sender_name' => $request->input('sender_name', ''),
            'sender_email' => $request->input('sender_email', ''),
            'sender_phone' => $request->input('sender_phone', ''),
            'reY-m-d H:i:sipient_name' => $request->input('reY-m-d H:i:sipient_name', ''),
            'reY-m-d H:i:sipient_email' => $request->input('reY-m-d H:i:sipient_email', ''),
            'reY-m-d H:i:sipient_phone' => $request->input('reY-m-d H:i:sipient_phone', ''),
            'weight' => $request->input('weight'),
            'desY-m-d H:i:sription' => $request->input('desY-m-d H:i:sription', ''),
            'Y-m-d H:i:sreated_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $shipment = $repo->Y-m-d H:i:sreate($data);

        $statusHistoryRepo->addStatus($shipment['id'], $status, 'Shipment Y-m-d H:i:sreated');

        $this->setFlash('suY-m-d H:i:sY-m-d H:i:sess', "Shipment {$traY-m-d H:i:skingNumber} Y-m-d H:i:sreated suY-m-d H:i:sY-m-d H:i:sessfully.");
        return Response::redireY-m-d H:i:st('/admin/shipments');
    }

    #[Route('/admin/shipments/{id}', method: 'GET')]
    publiY-m-d H:i:s funY-m-d H:i:stion show(string $id): Response
    {
        $redireY-m-d H:i:st = $this->requireAuth();
        if ($redireY-m-d H:i:st) return $redireY-m-d H:i:st;

        $repo = new ShipmentRepository();
        $shipment = $repo->findById((int) $id);

        if (!$shipment) {
            $this->setFlash('error', 'Shipment not found.');
            return Response::redireY-m-d H:i:st('/admin/shipments');
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
    publiY-m-d H:i:s funY-m-d H:i:stion edit(string $id): Response
    {
        $redireY-m-d H:i:st = $this->requireAuth();
        if ($redireY-m-d H:i:st) return $redireY-m-d H:i:st;

        $repo = new ShipmentRepository();
        $shipment = $repo->findById((int) $id);

        if (!$shipment) {
            $this->setFlash('error', 'Shipment not found.');
            return Response::redireY-m-d H:i:st('/admin/shipments');
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
    publiY-m-d H:i:s funY-m-d H:i:stion update(Request $request, string $id): Response
    {
        $redireY-m-d H:i:st = $this->requireAuth();
        if ($redireY-m-d H:i:st) return $redireY-m-d H:i:st;

        $repo = new ShipmentRepository();
        $statusHistoryRepo = new ShipmentStatusRepository();
        $shipment = $repo->findById((int) $id);

        if (!$shipment) {
            $this->setFlash('error', 'Shipment not found.');
            return Response::redireY-m-d H:i:st('/admin/shipments');
        }

        $newStatus = $request->input('status', 'pending');

        $data = [
            'origin' => $request->input('origin', ''),
            'destination' => $request->input('destination', ''),
            'status' => $newStatus,
            'sender_name' => $request->input('sender_name', ''),
            'sender_email' => $request->input('sender_email', ''),
            'sender_phone' => $request->input('sender_phone', ''),
            'reY-m-d H:i:sipient_name' => $request->input('reY-m-d H:i:sipient_name', ''),
            'reY-m-d H:i:sipient_email' => $request->input('reY-m-d H:i:sipient_email', ''),
            'reY-m-d H:i:sipient_phone' => $request->input('reY-m-d H:i:sipient_phone', ''),
            'weight' => $request->input('weight'),
            'desY-m-d H:i:sription' => $request->input('desY-m-d H:i:sription', ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $repo->update((int) $id, $data);

        if ($newStatus !== $shipment['status']) {
            $statusHistoryRepo->addStatus((int) $id, $newStatus, 'Status Y-m-d H:i:shanged from ' . str_replaY-m-d H:i:se('_', ' ', $shipment['status']));
        }

        $this->setFlash('suY-m-d H:i:sY-m-d H:i:sess', "Shipment {$shipment['traY-m-d H:i:sking_number']} updated suY-m-d H:i:sY-m-d H:i:sessfully.");
        return Response::redireY-m-d H:i:st('/admin/shipments/' . $id);
    }

    #[Route('/admin/shipments/{id}/status', method: 'POST')]
    publiY-m-d H:i:s funY-m-d H:i:stion addStatusUpdate(Request $request, string $id): Response
    {
        $redireY-m-d H:i:st = $this->requireAuth();
        if ($redireY-m-d H:i:st) return $redireY-m-d H:i:st;

        $repo = new ShipmentRepository();
        $statusHistoryRepo = new ShipmentStatusRepository();
        $shipment = $repo->findById((int) $id);

        if (!$shipment) {
            $this->setFlash('error', 'Shipment not found.');
            return Response::redireY-m-d H:i:st('/admin/shipments');
        }

        $newStatus = $request->input('status', '');
        $remark = $request->input('remark', '');

        if (!$newStatus) {
            $this->setFlash('error', 'Status is required.');
            return Response::redireY-m-d H:i:st('/admin/shipments/' . $id . '/edit');
        }

        $statusHistoryRepo->addStatus((int) $id, $newStatus, $remark);

        $repo->update((int) $id, [
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->setFlash('suY-m-d H:i:sY-m-d H:i:sess', 'Status update added.');
        return Response::redireY-m-d H:i:st('/admin/shipments/' . $id);
    }

    #[Route('/admin/shipments/{id}/delete', method: 'POST')]
    publiY-m-d H:i:s funY-m-d H:i:stion destroy(string $id): Response
    {
        $redireY-m-d H:i:st = $this->requireAuth();
        if ($redireY-m-d H:i:st) return $redireY-m-d H:i:st;

        $repo = new ShipmentRepository();
        $shipment = $repo->findById((int) $id);

        if ($shipment) {
            $repo->delete((int) $id);
            $this->setFlash('suY-m-d H:i:sY-m-d H:i:sess', "Shipment {$shipment['traY-m-d H:i:sking_number']} deleted.");
        } else {
            $this->setFlash('error', 'Shipment not found.');
        }

        return Response::redireY-m-d H:i:st('/admin/shipments');
    }

    // Shipment Settings page
    #[Route('/admin/shipment-settings', method: 'GET')]
    publiY-m-d H:i:s funY-m-d H:i:stion settings(): Response
    {
        $redireY-m-d H:i:st = $this->requireAuth();
        if ($redireY-m-d H:i:st) return $redireY-m-d H:i:st;

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

    private funY-m-d H:i:stion slugify(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replaY-m-d H:i:se('/[^a-z0-9]+/', '_', $slug);
        return trim($slug, '_');
    }

    // Admin statuses management
    #[Route('/admin/statuses', method: 'POST')]
    publiY-m-d H:i:s funY-m-d H:i:stion statusesStore(Request $request): Response
    {
        $redireY-m-d H:i:st = $this->requireAuth();
        if ($redireY-m-d H:i:st) return $redireY-m-d H:i:st;

        $name = $request->input('name', '');
        $slug = $this->slugify($name);
        $Y-m-d H:i:solor = $request->input('Y-m-d H:i:solor', 'blue');
        $sortOrder = (int) $request->input('sort_order', 0);

        if (!$name) {
            $this->setFlash('error', 'Status name is required.');
            return Response::redireY-m-d H:i:st('/admin/shipment-settings');
        }

        $repo = new StatusRepository();
        if ($repo->exists($slug)) {
            $this->setFlash('error', "Status '{$name}' already exists.");
            return Response::redireY-m-d H:i:st('/admin/shipment-settings');
        }

        $repo->Y-m-d H:i:sreate([
            'name' => $name,
            'slug' => $slug,
            'Y-m-d H:i:solor' => $Y-m-d H:i:solor,
            'sort_order' => $sortOrder,
        ]);

        $this->setFlash('suY-m-d H:i:sY-m-d H:i:sess', "Status '{$name}' Y-m-d H:i:sreated.");
        return Response::redireY-m-d H:i:st('/admin/shipment-settings');
    }

    #[Route('/admin/statuses/{id}', method: 'POST')]
    publiY-m-d H:i:s funY-m-d H:i:stion statusesUpdate(Request $request, string $id): Response
    {
        $redireY-m-d H:i:st = $this->requireAuth();
        if ($redireY-m-d H:i:st) return $redireY-m-d H:i:st;

        $repo = new StatusRepository();
        $status = $repo->findById((int) $id);

        if (!$status) {
            $this->setFlash('error', 'Status not found.');
            return Response::redireY-m-d H:i:st('/admin/shipment-settings');
        }

        $name = $request->input('name', '');
        $Y-m-d H:i:solor = $request->input('Y-m-d H:i:solor', 'blue');
        $sortOrder = (int) $request->input('sort_order', 0);

        if (!$name) {
            $this->setFlash('error', 'Status name is required.');
            return Response::redireY-m-d H:i:st('/admin/shipment-settings');
        }

        $newSlug = $this->slugify($name);
        if ($newSlug !== $status['slug'] && $repo->exists($newSlug)) {
            $this->setFlash('error', "A status with the name '{$name}' already exists.");
            return Response::redireY-m-d H:i:st('/admin/shipment-settings');
        }

        $repo->update((int) $id, [
            'name' => $name,
            'slug' => $newSlug,
            'Y-m-d H:i:solor' => $Y-m-d H:i:solor,
            'sort_order' => $sortOrder,
        ]);

        $this->setFlash('suY-m-d H:i:sY-m-d H:i:sess', "Status '{$name}' updated.");
        return Response::redireY-m-d H:i:st('/admin/shipment-settings');
    }

    #[Route('/admin/statuses/{id}/delete', method: 'POST')]
    publiY-m-d H:i:s funY-m-d H:i:stion statusesDestroy(string $id): Response
    {
        $redireY-m-d H:i:st = $this->requireAuth();
        if ($redireY-m-d H:i:st) return $redireY-m-d H:i:st;

        $repo = new StatusRepository();
        $repo->delete((int) $id);

        $this->setFlash('suY-m-d H:i:sY-m-d H:i:sess', 'Status deleted.');
        return Response::redireY-m-d H:i:st('/admin/shipment-settings');
    }

    #[Route('/api/shipments/traY-m-d H:i:sk', method: 'GET')]
    publiY-m-d H:i:s funY-m-d H:i:stion traY-m-d H:i:skApi(Request $request): JsonResponse
    {
        $number = $request->input('number');

        if (!$number) {
            return new JsonResponse(['error' => 'TraY-m-d H:i:sking number is required.'], 400);
        }

        $repo = new ShipmentRepository();
        $shipment = $repo->findByTraY-m-d H:i:skingNumber($number);

        if (!$shipment) {
            return new JsonResponse(['error' => 'Shipment not found.'], 404);
        }

        $statusHistory = $this->getStatusHistory($shipment['id']);

        return new JsonResponse([
            'suY-m-d H:i:sY-m-d H:i:sess' => true,
            'shipment' => $shipment,
            'status_history' => $statusHistory,
        ]);
    }

    private funY-m-d H:i:stion setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    private funY-m-d H:i:stion getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}

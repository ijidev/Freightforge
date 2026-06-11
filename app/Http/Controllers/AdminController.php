<?php

namespace App\Http\Controllers;

use App\Domain\Section\SectionRepository;
use App\Domain\Setting\SettingRepository;
use App\Domain\Shipment\ShipmentRepository;
use App\Domain\User\UserRepository;
use Helix\Http\Request;
use Helix\Http\Response;
use Helix\Routing\Attributes\Route;
use Helix\View\Template;

class AdminController
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

    #[Route('/admin', method: 'GET')]
    public function dashboard(): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $shipmentRepo = new ShipmentRepository();
        $shipments = $shipmentRepo->findAll();

        $total = count($shipments);
        $pending = count(array_filter($shipments, fn($s) => $s['status'] === 'pending'));
        $inTransit = count(array_filter($shipments, fn($s) => $s['status'] === 'in_transit'));
        $delivered = count(array_filter($shipments, fn($s) => $s['status'] === 'delivered'));

        $recent = array_slice(array_reverse($shipments), 0, 5);
        $flash = $this->getFlash();

        $html = $this->render('admin.dashboard', [
            'page' => 'dashboard',
            'title' => 'Dashboard',
            'stats' => compact('total', 'pending', 'inTransit', 'delivered'),
            'recentShipments' => $recent,
            'flash' => $flash,
        ]);

        return Response::html($html);
    }

    #[Route('/admin/settings', method: 'GET')]
    public function settings(): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $settingsRepo = new SettingRepository();
        $settings = $settingsRepo->getAllAsArray();
        $flash = $this->getFlash();

        $html = $this->render('admin.settings', [
            'page' => 'settings',
            'title' => 'Site Settings',
            'settings' => $settings,
            'flash' => $flash,
        ]);

        return Response::html($html);
    }

    #[Route('/admin/settings', method: 'POST')]
    public function updateSettings(Request $request): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $settingsRepo = new SettingRepository();

        $keys = ['site_name', 'site_email', 'site_description', 'site_phone', 'site_address', 'site_hours'];
        foreach ($keys as $key) {
            $value = $request->input($key);
            if ($value !== null) {
                $settingsRepo->set($key, $value);
            }
        }

        $mailKeys = ['mail_driver', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'];
        foreach ($mailKeys as $key) {
            $value = $request->input($key);
            if ($value !== null) {
                $settingsRepo->set($key, $value);
            }
        }

        if (!empty($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
            $filename = 'logo-' . time() . '.' . $ext;
            $dest = $uploadDir . '/' . $filename;

            if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $dest)) {
                $settingsRepo->set('site_logo', '/uploads/' . $filename);
            }
        }

        $this->setFlash('success', 'Settings saved successfully.');
        return Response::redirect('/admin/settings');
    }

    #[Route('/admin/profile', method: 'GET')]
    public function profile(): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $user = $_SESSION['admin_user'] ?? null;
        $flash = $this->getFlash();

        $html = $this->render('admin.profile', [
            'page' => 'profile',
            'title' => 'Profile',
            'user' => $user,
            'flash' => $flash,
        ]);

        return Response::html($html);
    }

    #[Route('/admin/profile', method: 'POST')]
    public function updateProfile(Request $request): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $userRepo = new UserRepository();
        $user = $_SESSION['admin_user'] ?? null;

        if (!$user) {
            $this->setFlash('error', 'User not found.');
            return Response::redirect('/admin/profile');
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $passwordConfirm = $request->input('password_confirm');

        $data = [];
        if ($name) {
            $data['name'] = $name;
        }
        if ($email) {
            $data['email'] = $email;
        }
        if ($password) {
            if ($password !== $passwordConfirm) {
                $this->setFlash('error', 'Passwords do not match.');
                return Response::redirect('/admin/profile');
            }
            if (strlen($password) < 6) {
                $this->setFlash('error', 'Password must be at least 6 characters.');
                return Response::redirect('/admin/profile');
            }
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if (!empty($data)) {
            $updated = $userRepo->update($user['id'], $data);
            if ($updated) {
                $_SESSION['admin_user'] = $updated;
            }
        }

        $this->setFlash('success', 'Profile updated successfully.');
        return Response::redirect('/admin/profile');
    }

    #[Route('/admin/sections', method: 'GET')]
    public function sections(): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $sectionRepo = new SectionRepository();
        $grouped = $sectionRepo->getAllGrouped();
        $pages = ['home', 'about', 'contact'];
        $flash = $this->getFlash();

        $html = $this->render('admin.sections', [
            'page' => 'sections',
            'title' => 'Content Sections',
            'grouped' => $grouped,
            'pages' => $pages,
            'flash' => $flash,
        ]);

        return Response::html($html);
    }

    #[Route('/admin/sections/create', method: 'GET')]
    public function createSectionForm(): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $flash = $this->getFlash();

        $html = $this->render('admin.section-form', [
            'page' => 'sections',
            'title' => 'New Section',
            'section' => null,
            'sectionPage' => '',
            'sectionKey' => '',
            'isCreate' => true,
            'flash' => $flash,
        ]);

        return Response::html($html);
    }

    #[Route('/admin/sections/create', method: 'POST')]
    public function storeSection(Request $request): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $page = $request->input('page', '');
        $sectionKey = $request->input('section_key', '');

        if (!$page || !$sectionKey) {
            $this->setFlash('error', 'Page and section key are required.');
            return Response::redirect('/admin/sections/create');
        }

        $sectionRepo = new SectionRepository();
        $existing = $sectionRepo->findOne($page, $sectionKey);
        if ($existing) {
            $this->setFlash('error', "Section '{$sectionKey}' already exists on page '{$page}'.");
            return Response::redirect('/admin/sections/create');
        }

        $data = [
            'title' => $request->input('title', ''),
            'subtitle' => $request->input('subtitle', ''),
            'content' => $request->input('content', ''),
        ];

        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'section-' . $page . '-' . $sectionKey . '-' . time() . '.' . $ext;
            $dest = $uploadDir . '/' . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $data['image_path'] = '/uploads/' . $filename;
            }
        }

        $sectionRepo->upsert($page, $sectionKey, $data);
        $this->setFlash('success', "Section '{$sectionKey}' created on page '{$page}'.");

        return Response::redirect('/admin/sections');
    }

    #[Route('/admin/sections/{page}/{key}', method: 'GET')]
    public function editSection(string $page, string $key): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $sectionRepo = new SectionRepository();
        $section = $sectionRepo->findOne($page, $key);
        $flash = $this->getFlash();

        if (!$section) {
            $this->setFlash('error', "Section not found: {$page}/{$key}");
            return Response::redirect('/admin/sections');
        }

        $html = $this->render('admin.section-form', [
            'page' => 'sections',
            'title' => 'Edit Section',
            'section' => $section,
            'sectionPage' => $page,
            'sectionKey' => $key,
            'isCreate' => false,
            'flash' => $flash,
        ]);

        return Response::html($html);
    }

    #[Route('/admin/sections/{page}/{key}', method: 'POST')]
    public function updateSection(Request $request, string $page, string $key): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $sectionRepo = new SectionRepository();

        $data = [
            'title' => $request->input('title', ''),
            'subtitle' => $request->input('subtitle', ''),
            'content' => $request->input('content', ''),
        ];

        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'section-' . $page . '-' . $key . '-' . time() . '.' . $ext;
            $dest = $uploadDir . '/' . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $data['image_path'] = '/uploads/' . $filename;
            }
        }

        $sectionRepo->upsert($page, $key, $data);
        $this->setFlash('success', 'Section updated successfully.');

        return Response::redirect('/admin/sections');
    }

    #[Route('/admin/sections/{page}/{key}/delete', method: 'POST')]
    public function deleteSection(string $page, string $key): Response
    {
        $redirect = $this->requireAuth();
        if ($redirect) return $redirect;

        $sectionRepo = new SectionRepository();
        $section = $sectionRepo->findOne($page, $key);

        if ($section) {
            $sectionRepo->delete($section['id']);
            $this->setFlash('success', "Section '{$key}' deleted.");
        } else {
            $this->setFlash('error', "Section not found: {$page}/{$key}");
        }

        return Response::redirect('/admin/sections');
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

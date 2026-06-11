<?php

namespace App\Http\Controllers;

use App\Domain\Section\SectionRepository;
use App\Domain\Setting\SettingRepository;
use App\Domain\Shipment\ShipmentRepository;
use App\Domain\ShipmentStatus\ShipmentStatusRepository;
use Helix\Http\Request;
use Helix\Http\Response;
use Helix\Routing\Attributes\Route;
use Helix\View\Template;

class HomeController
{
    private function siteName(): string
    {
        try {
            $repo = new SettingRepository();
            return $repo->get('site_name', 'FreightForge');
        } catch (\Throwable) {
            return 'FreightForge';
        }
    }

    private function siteEmail(): string
    {
        try {
            $repo = new SettingRepository();
            return $repo->get('site_email', 'contact@freightforge.test');
        } catch (\Throwable) {
            return 'contact@freightforge.test';
        }
    }

    private function siteSetting(string $key, string $default): string
    {
        try {
            $repo = new SettingRepository();
            return $repo->get($key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }

    private function render(string $view, array $data = []): string
    {
        $template = new Template();
        $data['siteName'] = $this->siteName();
        return $template->render($view, $data);
    }

    private function pageSections(string $page): array
    {
        try {
            $repo = new SectionRepository();
            return $repo->findByPage($page);
        } catch (\Throwable) {
            return [];
        }
    }

    private function sectionsToMap(array $sections): array
    {
        $map = [];
        foreach ($sections as $s) {
            $map[$s['section_key']] = $s;
        }
        return $map;
    }

    #[Route('/', method: 'GET')]
    public function index(): Response
    {
        $sections = $this->pageSections('home');
        $html = $this->render('home', [
            'page' => 'home', 'title' => 'Home',
            'sections' => $sections,
            'sectionMap' => $this->sectionsToMap($sections),
        ]);
        return Response::html($html);
    }

    #[Route('/track', method: 'GET')]
    public function track(Request $request): Response
    {
        $number = $request->input('number');

        if ($number) {
            $repo = new ShipmentRepository();
            $shipment = $repo->findByTrackingNumber($number);

            if ($shipment) {
                $statusHistory = [];
                try {
                    $statusRepo = new ShipmentStatusRepository();
                    $statusHistory = $statusRepo->findByShipmentOrdered($shipment['id']);
                } catch (\Throwable) {
                }

                $html = $this->render('track', [
                    'page' => 'track',
                    'title' => 'Track Shipment',
                    'trackingNumber' => $number,
                    'shipment' => $shipment,
                    'statusHistory' => $statusHistory,
                ]);
            } else {
                $html = $this->render('track', [
                    'page' => 'track',
                    'title' => 'Track Shipment',
                    'trackingNumber' => $number,
                    'error' => "No shipment found with tracking number: {$number}",
                ]);
            }
        } else {
            $html = $this->render('track', [
                'page' => 'track',
                'title' => 'Track Shipment',
            ]);
        }

        return Response::html($html);
    }

    #[Route('/about', method: 'GET')]
    public function about(): Response
    {
        $sections = $this->pageSections('about');
        $html = $this->render('about', [
            'page' => 'about', 'title' => 'About',
            'sections' => $sections,
            'sectionMap' => $this->sectionsToMap($sections),
        ]);
        return Response::html($html);
    }

    #[Route('/contact', method: 'GET')]
    public function contact(): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $success = $_SESSION['contact_success'] ?? null;
        $error = $_SESSION['contact_error'] ?? null;
        unset($_SESSION['contact_success'], $_SESSION['contact_error']);

        $html = $this->render('contact', [
            'page' => 'contact',
            'title' => 'Contact',
            'siteEmail' => $this->siteEmail(),
            'sitePhone' => $this->siteSetting('site_phone', '+1 (555) 123-4567'),
            'siteAddress' => $this->siteSetting('site_address', "123 Logistics Ave, Suite 100\nPort City, PC 10001"),
            'siteHours' => $this->siteSetting('site_hours', "Mon — Fri: 8:00 AM — 6:00 PM\nSat: 9:00 AM — 2:00 PM"),
            'success' => $success,
            'error' => $error,
        ]);
        return Response::html($html);
    }

    #[Route('/contact', method: 'POST')]
    public function submitContact(Request $request): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $name = $request->input('name', '');
        $email = $request->input('email', '');
        $subject = $request->input('subject', '');
        $message = $request->input('message', '');

        if (!$name || !$email || !$subject || !$message) {
            $_SESSION['contact_error'] = 'All fields are required.';
            return Response::redirect('/contact');
        }

        $_SESSION['contact_success'] = 'Thank you! Your message has been received. We will get back to you within 24 hours.';
        return Response::redirect('/contact');
    }
}

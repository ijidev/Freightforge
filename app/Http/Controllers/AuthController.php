<?php

namespace App\Http\Controllers;

use App\Domain\User\UserRepository;
use Helix\Http\Request;
use Helix\Http\Response;
use Helix\Routing\Attributes\Route;
use Helix\View\Template;

class AuthController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    #[Route('/admin/login', method: 'GET')]
    public function loginForm(): Response
    {
        if (!empty($_SESSION['admin_logged_in'])) {
            return Response::redirect('/admin');
        }

        $template = new Template();
        $html = $template->render('admin.login', [
            'title' => 'Admin Login',
            'error' => $_SESSION['login_error'] ?? null,
        ]);
        unset($_SESSION['login_error']);

        return Response::html($html);
    }

    #[Route('/admin/login', method: 'POST')]
    public function login(Request $request): Response
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (!$email || !$password) {
            $_SESSION['login_error'] = 'Email and password are required.';
            return Response::redirect('/admin/login');
        }

        $userRepo = new UserRepository();
        $user = $userRepo->findOneBy('email', $email);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Invalid email or password.';
            return Response::redirect('/admin/login');
        }

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $user;

        return Response::redirect('/admin');
    }

    #[Route('/admin/logout', method: 'GET')]
    public function logout(): Response
    {
        unset($_SESSION['admin_logged_in'], $_SESSION['admin_user']);
        return Response::redirect('/admin/login');
    }
}

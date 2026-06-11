<?php

// Web routes are primarily handled by #[Route] attributes on controllers.
// This file can be used for additional closure-based routes if needed.

$router->add('GET', '/install', [\App\Http\Controllers\InstallController::class, 'index']);
$router->add('POST', '/install/check', [\App\Http\Controllers\InstallController::class, 'runChecks']);
$router->add('POST', '/install/setup', [\App\Http\Controllers\InstallController::class, 'setup']);

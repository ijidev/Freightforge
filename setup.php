<?php

require_once __DIR__ . '/vendor/autoload.php';

use Helix\Installer\Installer;

$installer = new Installer(__DIR__);
$installer->run();

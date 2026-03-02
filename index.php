<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

header('Location: ' . (isLoggedIn() ? 'home.php' : 'login.php'));
exit;

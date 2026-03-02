<?php

declare(strict_types=1);

const APP_NAME = 'ProConnect Portal';
const DB_PATH = __DIR__ . '/database.sqlite';
const UPLOAD_DIR = __DIR__ . '/uploads';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0775, true);
}

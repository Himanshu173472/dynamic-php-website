<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars(APP_NAME) ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="nav">
    <strong><?= htmlspecialchars(APP_NAME) ?></strong>
    <div>
      <?php if (isLoggedIn()): ?>
        <a href="home.php">Home</a>
        <a href="users.php">Registered Users</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
    </div>
  </nav>

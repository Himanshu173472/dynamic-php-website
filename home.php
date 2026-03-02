<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
requireAuth();

$user = currentUser();

include __DIR__ . '/header.php';
?>
<div class="container">
  <div class="card">
    <h2>Home page</h2>
    <p>Welcome, <strong><?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?></strong>.</p>
    <p class="muted">Use the navigation bar to view registered users in a grid format or manage your session.</p>
    <a class="btn btn-primary" href="users.php">View Registered Users</a>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>

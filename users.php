<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
requireAuth();

$users = getPdo()->query('SELECT first_name, middle_name, last_name, gender, address, contact_no, email, photograph, created_at FROM users ORDER BY id DESC')->fetchAll();

include __DIR__ . '/header.php';
?>
<div class="container">
  <div class="card">
    <h2>Registered users</h2>
    <p class="muted">Grid view of all users registered on the portal.</p>

    <div class="user-grid">
      <?php foreach ($users as $u): ?>
        <article class="user-card">
          <?php if (!empty($u['photograph'])): ?>
            <img class="avatar" src="<?= htmlspecialchars($u['photograph']) ?>" alt="<?= htmlspecialchars($u['first_name']) ?>">
          <?php else: ?>
            <div class="avatar" style="display:grid;place-items:center;color:#64748b;">No photo</div>
          <?php endif; ?>
          <h3><?= htmlspecialchars(trim($u['first_name'] . ' ' . $u['middle_name'] . ' ' . $u['last_name'])) ?></h3>
          <p><span class="pill"><?= htmlspecialchars($u['gender']) ?></span></p>
          <p class="muted"><?= htmlspecialchars($u['address']) ?></p>
          <p><strong>Contact:</strong> <?= htmlspecialchars($u['contact_no']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($u['email']) ?></p>
          <p class="muted">Registered: <?= htmlspecialchars(date('M d, Y', strtotime($u['created_at']))) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>

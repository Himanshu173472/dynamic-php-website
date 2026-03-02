<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

if (isLoggedIn()) {
    header('Location: home.php');
    exit;
}

$error = null;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');

    $stmt = getPdo()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $error = 'Invalid email or password.';
    } else {
        $_SESSION['user_id'] = (int)$user['id'];
        header('Location: home.php');
        exit;
    }
}

include __DIR__ . '/header.php';
?>
<div class="container">
  <div class="card" style="max-width: 500px; margin-inline: auto;">
    <h2>Welcome back</h2>
    <p class="muted">Login with your registered account.</p>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div style="margin-bottom: 1rem;">
        <label for="email">E-mail</label>
        <input id="email" type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
      </div>
      <div style="margin-bottom: 1rem;">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>
      </div>
      <div class="actions">
        <span class="muted">No account? <a href="register.php">Register now</a></span>
        <button class="btn btn-primary" type="submit">Login</button>
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>

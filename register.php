<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

$errors = [];
$success = null;
$old = [
    'first_name' => '',
    'middle_name' => '',
    'last_name' => '',
    'address' => '',
    'gender' => '',
    'contact_no' => '',
    'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $_) {
        $old[$key] = trim((string)($_POST[$key] ?? ''));
    }

    $password = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');
    $acceptedTerms = isset($_POST['accepted_terms']) ? 1 : 0;

    if ($old['first_name'] === '') $errors[] = 'First name is required.';
    if ($old['last_name'] === '') $errors[] = 'Last name is required.';
    if ($old['address'] === '') $errors[] = 'Address is required.';
    if (!in_array($old['gender'], ['Male', 'Female', 'Other'], true)) $errors[] = 'Please choose a valid gender.';
    if ($old['contact_no'] === '' || !preg_match('/^[0-9+\-\s]{7,20}$/', $old['contact_no'])) $errors[] = 'Enter a valid contact number.';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirmPassword) $errors[] = 'Password and confirm password do not match.';
    if ($acceptedTerms !== 1) $errors[] = 'You must accept terms and conditions.';

    $photoPath = null;
    if (isset($_FILES['photograph']) && $_FILES['photograph']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['photograph']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Unable to upload photograph.';
        } else {
            $tmp = $_FILES['photograph']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['photograph']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowed, true)) {
                $errors[] = 'Photograph must be jpg, jpeg, png, or webp.';
            } else {
                $filename = uniqid('user_', true) . '.' . $ext;
                $destination = UPLOAD_DIR . '/' . $filename;
                if (!move_uploaded_file($tmp, $destination)) {
                    $errors[] = 'Failed to save photograph.';
                } else {
                    $photoPath = 'uploads/' . $filename;
                }
            }
        }
    } else {
        $errors[] = 'Photograph is required.';
    }

    if (!$errors) {
        try {
            $stmt = getPdo()->prepare(
                'INSERT INTO users (first_name, middle_name, last_name, address, gender, contact_no, email, photograph, password_hash, accepted_terms, created_at)
                VALUES (:first_name, :middle_name, :last_name, :address, :gender, :contact_no, :email, :photograph, :password_hash, :accepted_terms, :created_at)'
            );
            $stmt->execute([
                'first_name' => $old['first_name'],
                'middle_name' => $old['middle_name'],
                'last_name' => $old['last_name'],
                'address' => $old['address'],
                'gender' => $old['gender'],
                'contact_no' => $old['contact_no'],
                'email' => strtolower($old['email']),
                'photograph' => $photoPath,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'accepted_terms' => $acceptedTerms,
                'created_at' => date('c'),
            ]);

            $success = 'Registration successful. You can login now.';
            $old = array_map(fn () => '', $old);
        } catch (PDOException $e) {
            $errors[] = str_contains($e->getMessage(), 'UNIQUE')
                ? 'This email is already registered.'
                : 'Something went wrong while registering.';
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="container">
  <div class="card">
    <h2>Create your account</h2>
    <p class="muted">Fill in all required fields to register.</p>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="form-grid">
        <div>
          <label for="first_name">First name *</label>
          <input id="first_name" name="first_name" value="<?= htmlspecialchars($old['first_name']) ?>" required>
        </div>
        <div>
          <label for="middle_name">Middle name</label>
          <input id="middle_name" name="middle_name" value="<?= htmlspecialchars($old['middle_name']) ?>">
        </div>
        <div>
          <label for="last_name">Last name *</label>
          <input id="last_name" name="last_name" value="<?= htmlspecialchars($old['last_name']) ?>" required>
        </div>
        <div>
          <label for="gender">Gender *</label>
          <select id="gender" name="gender" required>
            <option value="">Select gender</option>
            <?php foreach (['Male', 'Female', 'Other'] as $gender): ?>
              <option value="<?= $gender ?>" <?= $old['gender'] === $gender ? 'selected' : '' ?>><?= $gender ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="full">
          <label for="address">Address *</label>
          <textarea id="address" name="address" required><?= htmlspecialchars($old['address']) ?></textarea>
        </div>
        <div>
          <label for="contact_no">Contact no *</label>
          <input id="contact_no" name="contact_no" value="<?= htmlspecialchars($old['contact_no']) ?>" required>
        </div>
        <div>
          <label for="email">E-mail *</label>
          <input id="email" name="email" type="email" value="<?= htmlspecialchars($old['email']) ?>" required>
        </div>
        <div>
          <label for="photograph">Photograph *</label>
          <input id="photograph" name="photograph" type="file" accept="image/*" required>
        </div>
        <div>
          <label for="password">Password *</label>
          <input id="password" name="password" type="password" required>
        </div>
        <div>
          <label for="confirm_password">Confirm password *</label>
          <input id="confirm_password" name="confirm_password" type="password" required>
        </div>
        <div class="full">
          <label>
            <input type="checkbox" name="accepted_terms" value="1" <?= isset($_POST['accepted_terms']) ? 'checked' : '' ?>>
            I agree to the Terms & Conditions.
          </label>
        </div>
      </div>
      <div class="actions">
        <span class="muted">Already registered? <a href="login.php">Login here</a></span>
        <button class="btn btn-primary" type="submit">Register</button>
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>

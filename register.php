<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $gender   = $_POST['gender'] ?? '';
    $age      = (int) ($_POST['age'] ?? 0);
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($fullName === '') $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if ($phone === '') $errors[] = 'Phone number is required.';
    if (!in_array($gender, ['Male', 'Female'], true)) $errors[] = 'Please select a gender.';
    if ($age < 1 || $age > 120) $errors[] = 'Please enter a valid age.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with that email already exists. Please log in instead.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO users (full_name, email, phone, gender, age, password_hash, role)
             VALUES (?, ?, ?, ?, ?, ?, "patient")'
        );
        $stmt->execute([$fullName, $email, $phone, $gender, $age, $hash]);

        $_SESSION['user_id']   = $pdo->lastInsertId();
        $_SESSION['full_name'] = $fullName;
        $_SESSION['role']      = 'patient';
        $_SESSION['flash_success'] = 'Welcome, ' . $fullName . '! Your account has been created.';
        redirect('dashboard.php');
    }
}

$pageTitle = 'Register';
require 'includes/header.php';
?>

<section class="section">
  <div class="container form-narrow">
    <div class="eyebrow">Step 1 of 3</div>
    <h1>Create your account</h1>
    <p>Registration is required so your results and appointments can be saved
      confidentially to your own record.</p>

    <?php if ($errors): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $e) echo '<div>' . h($e) . '</div>'; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="register.php" novalidate>
      <label for="full_name">Full name</label>
      <input type="text" id="full_name" name="full_name" value="<?= h($_POST['full_name'] ?? '') ?>" required>

      <label for="email">Email address</label>
      <input type="email" id="email" name="email" value="<?= h($_POST['email'] ?? '') ?>" required>

      <label for="phone">Phone number</label>
      <input type="tel" id="phone" name="phone" value="<?= h($_POST['phone'] ?? '') ?>" required>

      <label for="gender">Gender</label>
      <select id="gender" name="gender" required>
        <option value="">Select…</option>
        <option value="Male"   <?= (($_POST['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= (($_POST['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
      </select>

      <label for="age">Age</label>
      <input type="number" id="age" name="age" min="1" max="120" value="<?= h($_POST['age'] ?? '') ?>" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
      <div class="field-hint">At least 6 characters.</div>

      <label for="confirm_password">Confirm password</label>
      <input type="password" id="confirm_password" name="confirm_password" required>

      <button type="submit" class="btn btn-primary btn-block">Register</button>
    </form>

    <p style="margin-top:16px;">Already have an account? <a href="login.php">Log in</a></p>
  </div>
</section>

<?php require 'includes/footer.php'; ?>

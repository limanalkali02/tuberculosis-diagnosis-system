<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? h($pageTitle) . ' · ' : '' ?>TB Diagnosis System — Bayara Hospital</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600&family=Public+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">
  <div class="container">
    <a href="index.php" class="brand">
      <svg class="mark" viewBox="0 0 26 26" fill="none"><path d="M2 13H8L10.5 4L15.5 22L18 13H24" stroke="#D69A3C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      TB Diagnosis
    </a>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="contact.php">Contact</a></li>
      <?php if (is_logged_in()): ?>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="diagnose.php">Diagnose</a></li>
        <li><a href="history.php">History</a></li>
        <?php if (is_admin()): ?><li><a href="admin/index.php">Admin</a></li><?php endif; ?>
        <li><a href="logout.php" class="btn btn-outline btn-sm">Log out</a></li>
      <?php else: ?>
        <li><a href="login.php">Log in</a></li>
        <li><a href="register.php" class="btn btn-primary btn-sm">Register</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

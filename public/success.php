<?php
require_once __DIR__ . '/../includes/helpers.php';
$done = $_SESSION['done'] ?? null;
if (!$done) { header('Location: register.php'); exit; }
unset($_SESSION['done']);
page_head('Registration successful');
?>
<main class="container py-5 text-center" style="max-width: 560px;">
  <div class="card shadow-sm p-4 p-md-5">
    <div class="alert alert-success fs-5 fw-bold" role="status">✓ Registration successful</div>
    <p>Thank you for registering for the National Youth Empowerment Symposium 2026.</p>
    <p><span class="ref-box">Your reference: <b><?= e($done['ref']) ?></b></span></p>
    <p>Please save this reference number. See you at Rainbow Towers on 04 – 05 November 2026, and bring your National ID.</p>
    <a class="btn btn-primary mt-2" href="register.php">Register another person</a>
  </div>
</main>
<?php page_foot();

<?php
require_once __DIR__ . '/../includes/helpers.php';
page_head('Privacy notice: National Youth Empowerment Symposium 2026');
?>
<main class="container py-5" style="max-width: 760px;">
  <h1 class="h2 mb-4" style="color: var(--green-900);">Privacy notice</h1>

  <p>This notice explains how the Ministry of Youth Empowerment, Development and Vocational Training
     (“the Ministry”) handles your personal information when you register for the National Youth
     Empowerment Symposium 2026. We handle it in line with the Cyber and Data Protection Act [Chapter 12:07].</p>

  <h2 class="h5 mt-4">What we collect</h2>
  <p>Your full name, National ID number, age, gender, phone number, email address (if you give one),
     employment status, province, how you heard about the symposium, and the date and time you agreed to this notice.</p>

  <h2 class="h5 mt-4">Why we collect it</h2>
  <p>To register you, contact you about the symposium, check each person registers once,
     and plan the event and report on who took part (for example, numbers by province). We do not use your
     information for any other purpose without asking you first.</p>

  <h2 class="h5 mt-4">Who sees it</h2>
  <p>Only authorised Ministry staff working on the symposium. Our website hosting provider stores the information
     on our behalf and handles it only on our instructions. We do not sell your information.</p>

  <h2 class="h5 mt-4">How long we keep it</h2>
  <p>For <?= (int)RETENTION_MONTHS ?> months after the symposium, then we delete it, unless the law requires us to keep it longer.</p>

  <h2 class="h5 mt-4">Under 18</h2>
  <p>If you are under 18, a parent or guardian must agree to this notice on your behalf, and they may exercise
     your rights described below.</p>

  <h2 class="h5 mt-4">Your rights</h2>
  <p>You can ask to see the information we hold about you, ask us to correct it, ask us to delete it, or withdraw
     your consent, by contacting us at
     <a href="mailto:<?= e(PRIVACY_CONTACT_EMAIL) ?>"><?= e(PRIVACY_CONTACT_EMAIL) ?></a> or <?= e(PRIVACY_CONTACT_PHONE) ?>.
     If you are not satisfied with our response, you can complain to the Data Protection Authority, the Postal and
     Telecommunications Regulatory Authority of Zimbabwe (POTRAZ).</p>

  <a class="btn btn-primary mt-3" href="javascript:window.close()">Close</a>
</main>
<?php page_foot();

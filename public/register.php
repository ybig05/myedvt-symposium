<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';

$errors = []; $old = []; $serverError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
    try {
        if (!csrf_ok()) {
            $serverError = 'Your session expired. Please submit the form again.';
        } elseif (trim((string)($_POST['website'] ?? '')) !== ''          // hidden trap field was filled: a bot
               || !isset($_SESSION['form_t']) || time() - $_SESSION['form_t'] < 3) {   // submitted too fast: a bot
            $serverError = 'The form could not be submitted. Please wait a few seconds and try again.';
        } else {
            [$errors, $clean] = validate_registration($_POST);
            if (!$errors) {
                try {
                    $pdo = db();
                    $pdo->prepare(
                        'INSERT INTO registrations
                         (full_name, national_id, age, gender, email, phone, employment_status, province, heard_about_us, consent_at)
                         VALUES (?,?,?,?,?,?,?,?,?, NOW())'
                    )->execute(array_values($clean));
                    $_SESSION['done'] = ['ref' => refcode((int)$pdo->lastInsertId())];
                    header('Location: success.php');
                    exit;
                } catch (PDOException $ex) {
                    if (($ex->errorInfo[1] ?? 0) === 1062) $errors['national_id'] = 'This ID number is already registered.';
                    else throw $ex;
                }
            }
        }
    } catch (Throwable $ex) {
        error_log($ex->getMessage());
        $serverError = DEBUG ? 'DEBUG: ' . $ex->getMessage()
                             : 'Something went wrong on our side. Please try again.';
    }
}
$_SESSION['form_t'] = time();

function val(string $k): string { global $old; return e($old[$k] ?? ''); }
function bad(string $k): string { global $errors; return isset($errors[$k]) ? ' is-invalid' : ''; }
function msg(string $k): string { global $errors; return isset($errors[$k]) ? '<div class="invalid-feedback d-block">' . e($errors[$k]) . '</div>' : ''; }
function opts(array $list, string $k): string {
    global $old; $o = '';
    foreach ($list as $item) $o .= '<option' . (($old[$k] ?? '') === $item ? ' selected' : '') . '>' . e($item) . '</option>';
    return $o;
}

page_head('Register: National Youth Empowerment Symposium 2026');
?>
<header class="form-head">
  <div class="container" style="max-width: 680px;">
    <p class="ministry mb-2">Ministry of Youth Empowerment, Development and Vocational Training</p>
    <h1 class="h2 fw-bolder">Register for the National Youth Empowerment Symposium 2026</h1>
    <p class="meta mb-0">04 – 05 November 2026, Rainbow Towers</p>
  </div>
</header>

<main class="container pb-5" style="max-width: 680px;">
  <form method="post" class="card form-card shadow-sm p-3 p-md-4" novalidate>
    <?= csrf_field() ?>
    <div aria-hidden="true" style="position:absolute; left:-9999px; top:-9999px;">
      <label for="website">Leave this field empty</label>
      <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
    </div>

    <div class="mb-3">
      <label class="form-label" for="full_name">Full name</label>
      <input class="form-control<?= bad('full_name') ?>" id="full_name" name="full_name" value="<?= val('full_name') ?>" autocomplete="name" required>
      <?= msg('full_name') ?>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-sm-8">
        <label class="form-label" for="national_id">National ID number</label>
        <input class="form-control<?= bad('national_id') ?>" id="national_id" name="national_id" value="<?= val('national_id') ?>" placeholder="63-123456A42" autocomplete="off" required>
        <?= msg('national_id') ?>
      </div>
      <div class="col-sm-4">
        <label class="form-label" for="age">Age</label>
        <input class="form-control<?= bad('age') ?>" id="age" name="age" type="number" min="<?= MIN_AGE ?>" max="<?= MAX_AGE ?>" value="<?= val('age') ?>" required>
        <?= msg('age') ?>
      </div>
    </div>

    <div class="mb-3">
      <span class="form-label d-block">Gender</span>
      <?php foreach (GENDERS as $i => $g): ?>
        <input type="radio" class="btn-check" name="gender" id="gender<?= $i ?>" value="<?= e($g) ?>" <?= ($old['gender'] ?? '') === $g ? 'checked' : '' ?>>
        <label class="btn btn-outline-primary me-2 px-4" for="gender<?= $i ?>"><?= e($g) ?></label>
      <?php endforeach; ?>
      <?= msg('gender') ?>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-sm-6">
        <label class="form-label" for="phone">Phone number</label>
        <input class="form-control<?= bad('phone') ?>" id="phone" name="phone" type="tel" value="<?= val('phone') ?>" placeholder="0771234567" autocomplete="tel" required>
        <?= msg('phone') ?>
      </div>
      <div class="col-sm-6">
        <label class="form-label" for="email">Email <span class="optional">(optional)</span></label>
        <input class="form-control<?= bad('email') ?>" id="email" name="email" type="email" value="<?= val('email') ?>" autocomplete="email">
        <?= msg('email') ?>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label" for="employment_status">Employment status</label>
      <select class="form-select<?= bad('employment_status') ?>" id="employment_status" name="employment_status" required>
        <option value="">Select one</option><?= opts(EMPLOYMENT, 'employment_status') ?>
      </select>
      <?= msg('employment_status') ?>
    </div>

    <div class="mb-3">
      <label class="form-label" for="province">Province</label>
      <select class="form-select<?= bad('province') ?>" id="province" name="province" required>
        <option value="">Select your province</option><?= opts(PROVINCES, 'province') ?>
      </select>
      <?= msg('province') ?>
    </div>

    <div class="mb-3">
      <label class="form-label" for="heard_about_us">How did you hear about the symposium?</label>
      <select class="form-select<?= bad('heard_about_us') ?>" id="heard_about_us" name="heard_about_us" required>
        <option value="">Select one</option><?= opts(HEARD, 'heard_about_us') ?>
      </select>
      <?= msg('heard_about_us') ?>
    </div>

    <div class="form-check mb-4">
      <input class="form-check-input<?= bad('consent') ?>" type="checkbox" id="consent" name="consent" value="1" <?= ($old['consent'] ?? '') === '1' ? 'checked' : '' ?>>
      <label class="form-check-label" for="consent">
        I have read the <a href="privacy.php" target="_blank" rel="noopener">privacy notice</a> and agree that the
        Ministry may collect and use my personal information to organise the symposium. If I am under 18, my parent
        or guardian has agreed on my behalf.
      </label>
      <?= msg('consent') ?>
    </div>

    <?php if ($serverError): ?><div class="alert alert-danger" role="alert"><?= e($serverError) ?></div><?php endif; ?>
    <button class="btn btn-primary btn-lg w-100" type="submit">Register now</button>
  </form>
</main>
<?php page_foot();

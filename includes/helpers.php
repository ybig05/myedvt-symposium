<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

function is_https(): bool {
    return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
}
if (PHP_SAPI !== 'cli') {
    if (FORCE_HTTPS && !is_https()) {
        $host = SITE_HOST !== '' ? SITE_HOST : ($_SERVER['HTTP_HOST'] ?? '');
        header('Location: https://' . $host . ($_SERVER['REQUEST_URI'] ?? '/'), true, 301);
        exit;
    }
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: same-origin');
    if (is_https()) header('Strict-Transport-Security: max-age=31536000');
}
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => is_https()]);
    session_start();
}

const GENDERS    = ['Male', 'Female'];
const EMPLOYMENT = ['Employed', 'Unemployed', 'Student', 'Self-employed'];
const PROVINCES  = ['Harare', 'Mashonaland East', 'Mashonaland West', 'Mashonaland Central',
                    'Matabeleland South', 'Matabeleland North', 'Manicaland', 'Midlands',
                    'Bulawayo', 'Masvingo'];
const HEARD      = ['Social media (other than Ministry of Youth platforms)', 'Friends or family',
                    'An organisation', 'Ministry of Youth platforms (e.g., Facebook or X)'];
const ID_RE      = '/^(\d{2})-?(\d{6,7})\s?([A-Za-z])\s?(\d{2})$/';

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function refcode(int $id): string { return sprintf('MYE-%05d', $id); }

function csrf_field(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}
function csrf_ok(): bool {
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$_POST['csrf']);
}

function page_head(string $title): void { ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?></title>
  <link rel="icon" href="/favicon.icon" type="image/svg+xml">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<?php }
function page_foot(): void { ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php }

/** Returns [errors, cleanValues] */
function validate_registration(array $in): array {
    $err = [];
    $name = trim((string)($in['full_name'] ?? ''));
    if (mb_strlen($name) < 3) $err['full_name'] = 'Enter your full name.';

    $nid = trim((string)($in['national_id'] ?? ''));
    if (preg_match(ID_RE, $nid, $m)) $nid = $m[1] . '-' . $m[2] . strtoupper($m[3]) . $m[4];
    else $err['national_id'] = 'Use the format 63-123456A42.';

    $age = filter_var($in['age'] ?? '', FILTER_VALIDATE_INT);
    if ($age === false || $age < MIN_AGE || $age > MAX_AGE)
        $err['age'] = 'Age must be between ' . MIN_AGE . ' and ' . MAX_AGE . '.';

    $gender = (string)($in['gender'] ?? '');
    if (!in_array($gender, GENDERS, true)) $err['gender'] = 'Select your gender.';

    $email = trim((string)($in['email'] ?? ''));
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
        $err['email'] = 'Enter a valid email address or leave it blank.';

    $phone = preg_replace('/\s+/', '', (string)($in['phone'] ?? ''));
    if (!preg_match('/^(\+263|0)7\d{8}$/', $phone)) $err['phone'] = 'Use a number like 0771234567.';

    $emp = (string)($in['employment_status'] ?? '');
    if (!in_array($emp, EMPLOYMENT, true)) $err['employment_status'] = 'Select your employment status.';

    $prov = (string)($in['province'] ?? '');
    if (!in_array($prov, PROVINCES, true)) $err['province'] = 'Select your province.';

    $heard = (string)($in['heard_about_us'] ?? '');
    if (!in_array($heard, HEARD, true)) $err['heard_about_us'] = 'Tell us how you heard about the symposium.';

    if (($in['consent'] ?? '') !== '1') $err['consent'] = 'You must agree to the privacy notice to register.';

    return [$err, [
        'full_name' => $name, 'national_id' => $nid, 'age' => (int)$age, 'gender' => $gender,
        'email' => $email !== '' ? $email : null, 'phone' => $phone,
        'employment_status' => $emp, 'province' => $prov, 'heard_about_us' => $heard,
    ]];
}

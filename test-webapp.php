<?php
// Local QA exercise only: passwordless root and the intentional flaw are assignment requirements.
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "birthday_wishnote";
$conn = null;
$error = '';
$success = false;
$fields = [
    'FirstName' => ["Target's First Name", 30, true],
    'LastName' => ["Target's Last Name", 30, true],
    'GovPosition' => ["Target's Government Position", 20, true],
    'Location' => ["Target's Location", 40, false],
];
$values = array_fill_keys(array_keys($fields), '');
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset('utf8mb4');
} catch (Throwable $exception) {
    $error = 'Database unavailable. Start MariaDB and check the local database setup.';
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach ($fields as $name => [$label, $limit, $required]) {
        $raw = $_POST[$name] ?? '';
        $values[$name] = is_string($raw) ? trim($raw) : '';
        $length = preg_match_all('/./us', $values[$name]);
        if (!is_string($raw) || $length === false || $length > $limit || ($required && $values[$name] === '')) {
            $error = 'Please enter valid values within the stated character limits.';
        }
    }
    if ($error === '' && $conn !== null) {
        $FirstName = $values['FirstName'];
        $LastName = $values['LastName'];
        $GovPosition = $values['GovPosition'];
        $Location = $values['Location'];
        // INTENTIONAL QA FLAW: Location is captured and previewed, but omitted
        // from BOTH the INSERT columns and bind_param. Its database default is used.
        // Fix after the exercise: add Location, a fourth ?, and a fourth bound string.
        try {
            $stmt = $conn->prepare('INSERT INTO main_corrupt_politicians (FirstName, LastName, GovPosition) VALUES (?, ?, ?)');
            if (!$stmt) {
                throw new RuntimeException('Preparation failed');
            }
        } catch (Throwable $exception) {
            $error = 'Unable to prepare the database query. Please check the table setup.';
        }
        if ($error === '') {
            try {
                if (!$stmt->bind_param('sss', $FirstName, $LastName, $GovPosition) || !$stmt->execute()) {
                    throw new RuntimeException('Insertion failed');
                }
                $success = true;
                $values = array_fill_keys(array_keys($fields), '');
            } catch (Throwable $exception) {
                $error = 'Unable to send the wish note. Please try again.';
            } finally {
                $stmt->close();
            }
        }
    }
}
if ($conn !== null) {
    $conn->close();
}
function escape_html(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kara David's Wish Note</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; background: #f1f5f9; color: #16324f; font: 16px/1.5 system-ui, sans-serif; }
    main { width: min(100% - 32px, 540px); margin: 48px auto; padding: 32px; background: white; border-radius: 16px; box-shadow: 0 12px 36px #16324f12; border-top: 6px solid #2563eb; }
    h2 { margin: 0 0 8px; font-size: 28px; line-height: 1.2; }
    .intro { color: #64748b; margin: 0 0 24px; }
    label { display: block; font-weight: 600; margin-top: 18px; }
    small { display: block; color: #64748b; margin-top: 4px; }
    input { width: 100%; font: inherit; color: inherit; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; margin-top: 6px; }
    input:focus { outline: 3px solid #bfdbfe; border-color: #2563eb; }
    button { border: 0; border-radius: 8px; padding: 12px 18px; font: inherit; font-weight: 600; cursor: pointer; background: #2563eb; color: white; }
    button:hover { background: #1d4ed8; }
    button:focus-visible { outline: 3px solid #93c5fd; outline-offset: 3px; }
    .send { margin-top: 26px; width: 100%; }
    .notice { padding: 12px 16px; border-radius: 8px; margin: 20px 0; }
    .success { background: #dcfce7; color: #166534; }
    .error { background: #fee2e2; color: #991b1b; }
    dialog { width: min(100% - 32px, 480px); max-height: 90vh; border: 0; border-radius: 16px; padding: 28px; color: #16324f; box-shadow: 0 24px 64px #0003; }
    dialog::backdrop { background: #0f172a99; }
    dialog h3 { margin-top: 0; font-size: 24px; }
    dt { font-size: 14px; color: #64748b; margin-top: 16px; }
    dd { margin: 4px 0 0; overflow-wrap: anywhere; white-space: pre-wrap; }
    .actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; flex-wrap: wrap; }
    .cancel { background: #e2e8f0; color: #16324f; }
    .cancel:hover { background: #cbd5e1; }
    @media (max-width: 480px) { main { margin: 24px auto; padding: 24px; } h2 { font-size: 25px; } }
  </style>
</head>
<body>
<main>
  <h2>Kara David's Wish Note</h2>
  <p class="intro">Write a wish note. Review the details before sending.</p>
  <?php if ($success): ?><p class="notice success" role="status">Success! The wish note has been sent.</p><?php endif; ?>
  <?php if ($error !== ''): ?><p class="notice error" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
  <form id="wish-form" method="post">
    <?php foreach ($fields as $name => [$label, $limit, $required]): ?>
      <label for="<?= $name ?>"><?= escape_html($label) ?></label>
      <input id="<?= $name ?>" name="<?= $name ?>" type="text" maxlength="<?= $limit ?>" value="<?= escape_html($values[$name]) ?>" <?= $required ? 'required' : '' ?> aria-describedby="<?= $name ?>-hint">
      <small id="<?= $name ?>-hint"><?= $required ? 'Required' : 'Optional' ?> · Up to <?= $limit ?> characters</small>
    <?php endforeach; ?>
    <button class="send" type="submit">Send Wish Note</button>
  </form>
</main>
<dialog id="review-modal" aria-labelledby="review-heading">
  <h3 id="review-heading">Verify Target Data</h3>
  <dl>
    <?php foreach ($fields as $name => [$label]): ?>
      <dt><?= escape_html($label) ?></dt><dd id="preview-<?= $name ?>"></dd>
    <?php endforeach; ?>
  </dl>
  <div class="actions">
    <button class="cancel" id="cancel" type="button" autofocus>Cancel</button>
    <button id="confirm" type="button">Confirm &amp; Send</button>
  </div>
</dialog>
<script>
const form = document.getElementById('wish-form');
const modal = document.getElementById('review-modal');
const confirmButton = document.getElementById('confirm');
form.addEventListener('submit', (event) => {
  event.preventDefault();
  for (const name of ['FirstName', 'LastName', 'GovPosition', 'Location']) {
    document.getElementById('preview-' + name).textContent = form.elements[name].value;
  }
  modal.showModal();
});
document.getElementById('cancel').addEventListener('click', () => modal.close());
confirmButton.addEventListener('click', () => {
  if (!form.reportValidity()) { modal.close(); return; }
  confirmButton.disabled = true;
  form.submit();
});
</script>
</body>
</html>

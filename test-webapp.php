<?php
// ---------------------------------------------------------------
// Database connection (local MariaDB, passwordless root for dev only)
// ---------------------------------------------------------------
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "birthday_wishnote";

mysqli_report(MYSQLI_REPORT_OFF); // handle errors manually
$conn = @new mysqli($servername, $username, $password, $dbname);

$successMsg = "";
$errorMsg   = "";

if ($conn->connect_error) {
    $errorMsg = "Could not connect to the database. Please check that MariaDB is running.";
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Capture all four variables from the form
    $FirstName   = trim($_POST["FirstName"]   ?? "");
    $LastName    = trim($_POST["LastName"]    ?? "");
    $GovPosition = trim($_POST["GovPosition"] ?? "");
    $Location    = trim($_POST["Location"]    ?? "");

    // =============================================================
    // INTENTIONAL FLAW (for the exercise):
    // $Location is captured above and shown in the confirmation
    // modal, but it is deliberately LEFT OUT of this INSERT.
    //   - The Location column is missing from the SQL string.
    //   - $Location is missing from bind_param().
    // Result: only FirstName, LastName and GovPosition are saved;
    // the Location value the user typed is silently discarded.
    // =============================================================
    $sql  = "INSERT INTO main_corrupt_politicians (FirstName, LastName, GovPosition) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        // Fallback error if the query fails to prepare
        $errorMsg = "Error: the wish note could not be prepared. " . htmlspecialchars($conn->error);
    } else {
        $stmt->bind_param("sss", $FirstName, $LastName, $GovPosition); // no $Location here (flaw)
        if ($stmt->execute()) {
            $successMsg = "Success! The wish note has been sent.";
        } else {
            $errorMsg = "Error: the wish note could not be saved.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kara David's Wish Note</title>
<style>
  :root {
    --blue: #1e6fd9;
    --blue-dark: #1757ad;
    --bg: #eceff3;
    --text: #1f2937;
    --muted: #6b7280;
    --border: #cfd6df;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0; min-height: 100vh; padding: 24px 16px;
    display: flex; align-items: center; justify-content: center;
    background: var(--bg); color: var(--text);
    font-family: "Segoe UI", system-ui, -apple-system, Arial, sans-serif;
  }
  .card {
    width: 100%; max-width: 460px; background: #fff;
    border-radius: 10px; padding: 32px 28px;
    box-shadow: 0 4px 18px rgba(0,0,0,.12);
    border-top: 6px solid var(--blue);
  }
  h2 { margin: 0 0 20px; font-size: 1.5rem; color: var(--blue); }
  label { display: block; margin: 14px 0 6px; font-weight: 600; font-size: .92rem; }
  .opt { font-weight: 400; color: var(--muted); }
  input[type=text] {
    width: 100%; padding: 11px 12px; font-size: 1rem;
    border: 1px solid var(--border); border-radius: 6px; background: #fff;
  }
  input[type=text]:focus { outline: 2px solid var(--blue); outline-offset: 1px; border-color: var(--blue); }
  button {
    font: inherit; font-weight: 600; cursor: pointer;
    border: 0; border-radius: 6px; padding: 12px 18px;
  }
  .btn-primary { background: var(--blue); color: #fff; }
  .btn-primary:hover { background: var(--blue-dark); }
  .btn-secondary { background: #e5e9ef; color: var(--text); }
  .btn-secondary:hover { background: #d6dce5; }
  #submitBtn { width: 100%; margin-top: 24px; }
  .msg { padding: 12px 14px; border-radius: 6px; margin-bottom: 18px; font-weight: 600; }
  .msg.success { background: #e3f4e8; color: #17692f; border-left: 4px solid #2e9e50; }
  .msg.error   { background: #fde8e8; color: #9b1c1c; border-left: 4px solid #d93a3a; }

  .overlay {
    position: fixed; inset: 0; display: none;
    align-items: center; justify-content: center;
    background: rgba(15,23,42,.55); padding: 16px; z-index: 10;
  }
  .overlay.open { display: flex; }
  .modal {
    width: 100%; max-width: 420px; background: #fff; border-radius: 10px;
    padding: 26px 24px; box-shadow: 0 10px 30px rgba(0,0,0,.3);
  }
  .modal h3 { margin: 0 0 14px; color: var(--blue); }
  .modal dl { margin: 0 0 20px; }
  .modal dt { font-size: .8rem; color: var(--muted); margin-top: 10px; }
  .modal dd { margin: 2px 0 0; font-weight: 600; word-break: break-word; }
  .actions { display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap; }
</style>
</head>
<body>
<div class="card">
  <h2>Kara David's Wish Note</h2>

  <?php if ($successMsg): ?>
    <div class="msg success"><?= htmlspecialchars($successMsg) ?></div>
  <?php endif; ?>
  <?php if ($errorMsg): ?>
    <div class="msg error"><?= $errorMsg ?></div>
  <?php endif; ?>

  <form id="wishForm" method="POST" action="">
    <label for="FirstName">Target's First Name</label>
    <input type="text" id="FirstName" name="FirstName" maxlength="30" required>

    <label for="LastName">Target's Last Name</label>
    <input type="text" id="LastName" name="LastName" maxlength="30" required>

    <label for="GovPosition">Target's Government Position</label>
    <input type="text" id="GovPosition" name="GovPosition" maxlength="20" required>

    <label for="Location">Target's Location <span class="opt">(optional)</span></label>
    <input type="text" id="Location" name="Location" maxlength="40">

    <button type="submit" id="submitBtn" class="btn-primary">Submit</button>
  </form>
</div>

<div class="overlay" id="overlay" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal">
    <h3 id="modalTitle">Verify Target Data</h3>
    <dl>
      <dt>First Name</dt><dd id="vFirst"></dd>
      <dt>Last Name</dt><dd id="vLast"></dd>
      <dt>Government Position</dt><dd id="vPos"></dd>
      <dt>Location</dt><dd id="vLoc"></dd>
    </dl>
    <div class="actions">
      <button type="button" class="btn-secondary" id="cancelBtn">Cancel</button>
      <button type="button" class="btn-primary" id="confirmBtn">Confirm &amp; Send</button>
    </div>
  </div>
</div>

<script>
  const form = document.getElementById('wishForm');
  const overlay = document.getElementById('overlay');

  form.addEventListener('submit', function (e) {
    e.preventDefault(); // intercept; browser "required" checks have already passed
    document.getElementById('vFirst').textContent = form.FirstName.value;
    document.getElementById('vLast').textContent  = form.LastName.value;
    document.getElementById('vPos').textContent   = form.GovPosition.value;
    document.getElementById('vLoc').textContent   = form.Location.value || '(not provided)';
    overlay.classList.add('open');
    document.getElementById('confirmBtn').focus();
  });

  document.getElementById('cancelBtn').addEventListener('click', function () {
    overlay.classList.remove('open');
  });

  document.getElementById('confirmBtn').addEventListener('click', function () {
    form.submit(); // programmatic submit does not fire the submit event, so no interception
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') overlay.classList.remove('open');
  });
</script>
</body>
</html>

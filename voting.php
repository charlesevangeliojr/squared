<?php
session_start();
require_once 'php/config.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['student_id'];

/* Scanner access (unchanged) */
$scanner_sql = "SELECT 1 FROM event_scanners WHERE student_id = ? AND status = 'allow'";
$scanner_stmt = $conn->prepare($scanner_sql);
$scanner_stmt->bind_param("s", $student_id);
$scanner_stmt->execute();
$scanner_result = $scanner_stmt->get_result();
$isScanner = $scanner_result->num_rows > 0;
$scanner_stmt->close();
$scanner_result->free();

// 2) Load awards
$awards = [];
$res = $conn->query("SELECT id, slug, title FROM pca_awards ORDER BY id DESC");
while ($row = $res->fetch_assoc()) $awards[] = $row;
$res->free();

// Resolve selected award
$sel_award_slug = $_GET['award'] ?? ($awards[0]['slug'] ?? '');
$sel_award_id = null; $sel_award_title = '';
foreach ($awards as $a) {
  if ($a['slug'] === $sel_award_slug) { $sel_award_id = (int)$a['id']; $sel_award_title = $a['title']; break; }
}

// 3) Load nominees for selected award
$nominees = [];
if ($sel_award_id) {
  // Add optional fields (photo_url, description) if present in your schema
  $stmt = $conn->prepare("SELECT id, name, COALESCE(photo_url,'') AS photo_url, COALESCE(description,'') AS description FROM pca_nominees WHERE award_id=? ORDER BY name ASC");
  $stmt->bind_param("i", $sel_award_id);
  $stmt->execute();
  $r = $stmt->get_result();
  while ($row = $r->fetch_assoc()) $nominees[] = $row;
  $r->free(); $stmt->close();
}

$hasVoting = !empty($awards);

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>People’s Choice Award — Vote</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="images/Squared_Logo.png">
  <!-- Bootstrap 5.3.0 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <!-- <link rel="stylesheet" href="css/dashboard.css">
  <link rel="stylesheet" href="css/notify.css"> -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    body { background: #f6f7fb; }
    .navbar { background: #187c19; }
    .navbar .nav-link, .navbar .navbar-brand { color: #fff !important; }
    .brand-img { height: 28px; margin-right: .5rem; }
    .hero {
      background: linear-gradient(135deg, #187c19 0%, #69B41E 100%);
      color: #fff; border-radius: 18px; box-shadow: 0 10px 30px rgba(13,110,253,.25);
    }
    .hero .badge { background: rgba(255,255,255,.2); }
    .card { border: 0; border-radius: 14px; }
    .card-img-top {  object-fit: cover; border-top-left-radius: 14px; border-top-right-radius: 14px; }
    .nominee-card { transition: transform .08s ease, box-shadow .08s ease; }
    .nominee-card:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(0,0,0,.08); }
    .vote-btn { border-radius: 10px; }
    .category-select { min-width: 250px; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="home.php">
      <img class="brand-img" src="images/Squared_Logo.png" alt=""> Squared
    </a>
    <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="qrcard.php">QR-Card</a></li>
        <li class="nav-item"><a class="nav-link" href="notify.php">Announcements</a></li>
        <li class="nav-item"><a class="nav-link" href="myattendancerecord.php">My Attendance</a></li>
          <li class="nav-item"><a class="nav-link active fw-semibold" href="votingpca.php">Vote</a></li>
        <?php if ($isScanner): ?>
          <li class="nav-item"><a class="nav-link" href="event_scanner.php">Scan</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="php/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="container py-4 py-md-5">

  <!-- Hero -->
  <section class="hero p-4 p-lg-5 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <div class="d-flex align-items-center gap-2 mb-2">
          <span class="badge rounded-pill"><?= htmlspecialchars($sel_award_title) ?></span>
          <?php if ($hasVoting): ?>
            <span class="badge rounded-pill"><i class="bi bi-person-check"></i> One vote per category</span>
          <?php endif; ?>
        </div>
        <h1 class="h3 mb-1"><?= $hasVoting ? 'Cast your vote' : 'Voting unavailable' ?></h1>
        <p class="mb-0 opacity-75">
          <?= $hasVoting ? 'Choose your favorite nominee below.' : 'There are no active categories to vote on right now.' ?>
        </p>
      </div>
      <?php if ($hasVoting): ?>
        <div class="d-flex align-items-center gap-2">
          <label for="awardSel" class="form-label mb-0">Category</label>
          <select id="awardSel" class="form-select category-select">
            <?php foreach ($awards as $a): ?>
              <option value="<?= htmlspecialchars($a['slug']) ?>" <?= $a['slug'] === $sel_award_slug ? 'selected' : '' ?>>
                <?= htmlspecialchars($a['title']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Content -->
  <?php if (!$hasVoting): ?>
    <div class="alert alert-warning shadow-sm border-0">Please check back later. No active voting categories right now.</div>
  <?php else: ?>


<?php if (empty($nominees)): ?>
  <div class="alert alert-secondary shadow-sm border-0">No nominees yet for this category.</div>
<?php else: ?>
  <div class="row g-3 g-md-4">
    <?php foreach ($nominees as $n): ?>
      <div class="col-6 col-md-2"><!-- ✅ always 2 per row -->
        <div class="card nominee-card h-100 shadow-sm">
          <?php
            // Root-absolute path so it works from any directory
            $imgPath = !empty($n['photo_url'])
              ? 'admin/' . ltrim($n['photo_url'], '/')
              : 'images/placeholder.jpg';
          ?>
          <div class="ratio ratio-1x1 overflow-hidden rounded-top"><!-- ✅ square image box -->
            <img src="<?= htmlspecialchars($imgPath) ?>"
                 alt="<?= htmlspecialchars($n['name']) ?>"
                 class="w-100 h-100" style="object-fit:cover"
                 onerror="this.style.display='none'">
          </div>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title mb-1"><?= htmlspecialchars($n['name']) ?></h5>
            <p class="text-muted small flex-grow-1 mb-3"><?= htmlspecialchars($n['description']) ?></p>
            <button type="button" class="btn btn-success w-100 vote-btn" data-nid="<?= (int)$n['id'] ?>">
              <i class="bi bi-hand-thumbs-up me-1"></i> Vote
            </button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php endif; ?>

</main>

<!-- Toasts -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100">
  <div id="voteToast" class="toast text-bg-success border-0" role="alert" aria-live="polite" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body"><i class="bi bi-check2-circle me-1"></i> Vote recorded. Thank you!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
  <div id="errorToast" class="toast text-bg-danger border-0 mt-2" role="alert" aria-live="polite" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="errorMsg"><i class="bi bi-exclamation-octagon me-1"></i> Something went wrong.</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  const awardSlug = <?= json_encode($sel_award_slug) ?>;
  const awardSel = document.getElementById('awardSel');
  if (awardSel) {
    awardSel.addEventListener('change', () => {
      const url = new URL(window.location.href);
      url.searchParams.set('award', awardSel.value);
      window.location.href = url.toString();
    });
  }

  const voteToast = new bootstrap.Toast(document.getElementById('voteToast'));
  const errorToast = new bootstrap.Toast(document.getElementById('errorToast'));
  const errorMsg = document.getElementById('errorMsg');

document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.vote-btn');
  if (!btn) return;

  btn.disabled = true;
  const fd = new FormData();
  fd.append('award', awardSlug || '');
  fd.append('nominee_id', btn.dataset.nid);

  try {
    const r = await fetch('pca_vote.php', { method: 'POST', body: fd });
    const data = await r.json().catch(() => null);

    if (r.ok && data?.ok) {
      voteToast.show();
    } else if (data?.error === 'already_voted') {
      errorMsg.textContent = 'You have already voted in this category.';
      errorToast.show();
    } else {
      errorMsg.textContent = data?.message || data?.error || 'Vote failed.';
      errorToast.show();
    }
  } catch (err) {
    errorMsg.textContent = 'Network error';
    errorToast.show();
  } finally {
    btn.disabled = false;
  }
});
  </script>
  
</body>
</html>
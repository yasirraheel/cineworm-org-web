<?php
/**
 * CineWorm Auto-Deploy Hook
 * Executed via HTTP to trigger git pull on Hostinger server.
 */
$secret = 'cw_deploy_secret_9988776655';
if (($_GET['key'] ?? '') !== $secret && ($_POST['key'] ?? '') !== $secret) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

header('Content-Type: text/plain');
$baseDir = dirname(__DIR__);
chdir($baseDir);

$gitOutput = shell_exec('git pull origin master 2>&1');
echo "=== GIT PULL OUTPUT ===\n" . $gitOutput . "\n";

// Clear view cache
$artisanOutput = shell_exec('php artisan view:clear 2>&1');
echo "=== VIEW CLEAR ===\n" . $artisanOutput . "\n";

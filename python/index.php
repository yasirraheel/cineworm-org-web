<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pythonPath = "/home/u393044472/python/bin/python3";
$pythonScript = "/home/u393044472/domains/cineworm.org/public_html/python/test_python.py";

// Execute the Python script and capture output
$output = shell_exec("$pythonPath $pythonScript 2>&1");

// Show the output
echo "<pre>$output</pre>";
?>

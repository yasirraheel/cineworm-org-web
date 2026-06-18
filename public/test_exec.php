<?php
echo "disabled: " . ini_get('disable_functions') . "\n";
foreach(['exec', 'shell_exec', 'system', 'passthru', 'proc_open', 'popen'] as $f) {
    echo $f . ": " . (function_exists($f) ? 'YES' : 'NO') . "\n";
}

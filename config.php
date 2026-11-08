<?php
$DEFAULT_ROOT = 'D:/web/csv/studio/csv_data';
$DEFAULT_ROOT = rtrim(str_replace('\\','/',$DEFAULT_ROOT),'/');
$fallback = __DIR__ . '/csv_data';
if (!is_dir($DEFAULT_ROOT)) $DEFAULT_ROOT = $fallback;
$real = realpath($DEFAULT_ROOT);
define('DEFAULT_CSV_ROOT', $real ? str_replace('\\','/',$real) : $DEFAULT_ROOT);
define('BACKUP_DIR', str_replace('\\','/', __DIR__ . '/backups'));
?>

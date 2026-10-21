<?php
declare(strict_types=1);
// Run with: php tests/regression/create_partial_artifacts.php SOURCE_ROOT EMPTY_DATA_DIR
$source = $argv[1];
$data = $argv[2];
if (!is_dir($data) || count(scandir($data)) !== 2) { throw new RuntimeException('Use an empty isolated data directory'); }
define('LAZER_DATA_PATH', rtrim($data, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
spl_autoload_register(function ($class) use ($source) {
    if (strpos($class, 'Lazer\\') === 0) { require $source . '/src/' . str_replace('\\', '/', substr($class, 6)) . '.php'; }
});
$failures = 0;
foreach (['data-only', 'config-only', 'both', 'neither'] as $name) {
    $dataFile = LAZER_DATA_PATH . $name . '.data.json';
    $configFile = LAZER_DATA_PATH . $name . '.config.json';
    if ($name === 'data-only' || $name === 'both') { file_put_contents($dataFile, '[{"id":42,"name":"keep"}]'); }
    if ($name === 'config-only' || $name === 'both') { file_put_contents($configFile, '{"last_id":42,"schema":{"id":"integer","name":"string"},"relations":{}}'); }
    $before = [is_file($dataFile) ? file_get_contents($dataFile) : null, is_file($configFile) ? file_get_contents($configFile) : null];
    $rejected = false;
    try { Lazer\Classes\Database::create($name, ['name' => 'string']); }
    catch (Lazer\Classes\LazerException $e) { $rejected = true; }
    $after = [is_file($dataFile) ? file_get_contents($dataFile) : null, is_file($configFile) ? file_get_contents($configFile) : null];
    $ok = $name === 'neither' ? (!$rejected && json_decode($after[0], true) === [] && json_decode($after[1], true)['schema'] === ['id' => 'integer', 'name' => 'string']) : ($rejected && $before === $after);
    echo $name . ': ' . ($ok ? 'PASS' : 'FAIL') . PHP_EOL;
    if (!$ok) { ++$failures; }
}
exit($failures === 0 ? 0 : 1);

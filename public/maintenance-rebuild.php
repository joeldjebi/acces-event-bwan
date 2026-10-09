<?php

declare(strict_types=1);

$token = '66d85549ec10055f43de37075512311c666e550a1d7e682e';

if (! hash_equals($token, (string) ($_GET['token'] ?? ''))) {
    http_response_code(404);
    exit('Not found');
}

$basePath = dirname(__DIR__);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$deleted = [];
$errors = [];

$removeFiles = static function (string $pattern) use (&$deleted, &$errors): void {
    foreach (glob($pattern) ?: [] as $file) {
        if (! is_file($file) || basename($file) === '.gitignore') {
            continue;
        }

        if (@unlink($file)) {
            $deleted[] = $file;
        } else {
            $errors[] = "Impossible de supprimer {$file}";
        }
    }
};

$removeFiles($basePath.'/bootstrap/cache/*.php');
$removeFiles($basePath.'/storage/framework/views/*.php');
$removeFiles($basePath.'/storage/framework/cache/data/*');
$removeFiles($basePath.'/storage/framework/sessions/*');

$commands = [];

$runCommand = static function (string $command) use ($basePath): array {
    if (! function_exists('shell_exec')) {
        return [
            'ok' => false,
            'output' => 'shell_exec est desactive sur ce serveur.',
        ];
    }

    $output = shell_exec('cd '.escapeshellarg($basePath).' && '.$command.' 2>&1');

    return [
        'ok' => $output !== null,
        'output' => trim((string) $output),
    ];
};

$phpBinary = PHP_BINARY ?: 'php';

if (is_file($basePath.'/artisan')) {
    $commands['optimize_clear'] = $runCommand(escapeshellcmd($phpBinary).' artisan optimize:clear');
}

$npmBinary = trim((string) shell_exec('command -v npm 2>/dev/null'));

if ($npmBinary !== '') {
    $commands['build'] = $runCommand('PATH=/opt/homebrew/bin:/usr/local/bin:/usr/bin:/bin '.escapeshellarg($npmBinary).' run build');
} else {
    $commands['build'] = [
        'ok' => false,
        'output' => 'npm introuvable sur ce serveur. Le build deja present dans public/build reste utilise.',
    ];
}

echo json_encode([
    'ok' => count($errors) === 0,
    'message' => 'Maintenance executee.',
    'deleted_count' => count($deleted),
    'errors' => $errors,
    'commands' => $commands,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('name', 'like', '%Rifqy%')->orWhere('username', 'like', '%rifqy%')->first();

if (!$user) {
    echo 'User not found' . PHP_EOL;
    exit;
}

echo 'ID: ' . $user->id . PHP_EOL;
echo 'Name: ' . $user->name . PHP_EOL;
echo 'Username: ' . $user->username . PHP_EOL;
echo 'Role: ' . $user->role . PHP_EOL;
echo 'Has face_descriptor: ' . ($user->face_descriptor ? 'YES' : 'NO') . PHP_EOL;

<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('name', 'like', '%Rifqy%')->orWhere('username', 'like', '%rifqy%')->first();

if (!$user) {
    echo "User not found\n";
    exit;
}

echo 'raw profile_photo column: ' . var_export($user->getRawOriginal('profile_photo'), true) . "\n";
echo 'profile_photo_url accessor: ' . var_export($user->profile_photo_url, true) . "\n";
echo 'Storage disk public url() base: ' . \Illuminate\Support\Facades\Storage::disk('public')->url('') . "\n";
echo 'APP_URL config: ' . config('app.url') . "\n";

if ($user->getRawOriginal('profile_photo')) {
    $path = storage_path('app/public/' . $user->getRawOriginal('profile_photo'));
    echo 'File exists on disk at expected path: ' . (file_exists($path) ? 'YES' : 'NO') . " ($path)\n";
}

echo "storage symlink check: " . (is_link(public_path('storage')) ? 'public/storage IS a symlink' : 'public/storage is NOT a symlink (missing!)') . "\n";

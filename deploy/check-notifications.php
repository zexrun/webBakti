<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$notifications = App\Models\User::first()->notifications()->latest()->take(5)->get();

foreach ($notifications as $item) {
    echo $item->id . ' | ' . $item->type . ' | read_at:' . ($item->read_at ?? 'NULL') . ' | ' . json_encode($item->data) . PHP_EOL;
}

echo 'Total notifications table count: ' . DB::table('notifications')->count() . PHP_EOL;

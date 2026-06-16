<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$currentUserId = 2; // student
$selectedUserId = 6; // faculty

$messages = \App\Models\Message::where(function($q) use ($currentUserId, $selectedUserId) {
    $q->where('sender_id', $currentUserId)->where('receiver_id', $selectedUserId);
})->orWhere(function($q) use ($currentUserId, $selectedUserId) {
    $q->where('sender_id', $selectedUserId)->where('receiver_id', $currentUserId);
})
->orderBy('created_at', 'asc')
->get();

echo "Results Count: " . $messages->count() . "\n";
foreach ($messages as $m) {
    echo "ID: {$m->id} | Msg: {$m->message_text}\n";
}

unlink(__FILE__);

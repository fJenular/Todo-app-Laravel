<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Task;

$task = Task::create([
    'title' => 'CLI Created Task',
    'deadline' => date('Y-m-d H:i:s', strtotime('+1 day')),
    'priority' => 'medium',
]);

echo "CREATED: " . $task->id . PHP_EOL;

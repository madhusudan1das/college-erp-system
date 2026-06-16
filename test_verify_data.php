<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (App\Models\Student::all() as $s) {
    echo "Student: {$s->first_name} | Enrollment: '{$s->enrollment_no}'\n";
}
foreach (App\Models\Faculty::all() as $f) {
    echo "Faculty: {$f->first_name} | Phone: '{$f->phone}'\n";
}
unlink(__FILE__);

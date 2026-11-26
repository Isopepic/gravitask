<?php
// Author: Mauricio Letort

require_once 'util.php';
require_once 'auth.php';
require_once 'db.php';

$userId = requireUser();

$event = validateEvent([
    'type' => field('type'),
    'name' => field('name'),
    'description' => field('description'),
    'deadline' => field('deadline'),
    'weight' => field('weight')
]);

if (is_string($event)) {
    jsonError($event, 400);
}

$stored = addEvent($userId, $event);

jsonOut($stored);

<?php
// Author: Mauricio Letort

require_once 'util.php';
require_once 'auth.php';
require_once 'db.php';

$userId = requireUser();

$id = (int) field('id');
if ($id <= 0) {
    jsonError("Missing event ID", 400);
}

$old = getEvent($userId, $id);
if ($old === null) {
    jsonError("Event does not exist", 404);
}

$merged = $old;
foreach (['type', 'name', 'description', 'deadline', 'weight'] as $field) {
    $value = field($field);
    if ($value !== null) {
        $merged[$field] = $value;
    }
}
$event = validateEvent($merged);
if (is_string($event)) {
    jsonError($event, 400);
}


$stored = updateEvent($userId, $id, $event);

jsonOut($stored);
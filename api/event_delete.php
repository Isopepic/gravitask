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

$event = getEvent($userId, $id);
if ($event === null) {
    jsonError("Event does not exist", 404);
}

deleteEvent($userId, $id);
jsonOut(['ok' => true]);
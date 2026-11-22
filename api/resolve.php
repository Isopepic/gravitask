<?php
// Author: Ismaël
require_once 'util.php';
require_once 'auth.php';
require_once 'db.php';

$userId = requireUser();
$id = field('id');
$action = field('action');
if (!$id || !$action) {
    jsonError('missing fields', 400);
}
if (!getEvent($userId, $id)) {
    jsonError('event not found', 404);
}

if ($action == 'done') {
    setStatus($userId, $id, 'done');
    jsonOut(['ok' => true]);
}
if ($action == 'drop') {
    setStatus($userId, $id, 'dropped');
    jsonOut(['ok' => true]);
}
if ($action == 'postpone') {
    $newDeadline = field('newDeadline');
    if (!$newDeadline || !strtotime($newDeadline)) {
        jsonError('bad deadline', 400);
    }
    if (strlen($newDeadline) == 10) {
        $newDeadline .= ' 23:59:00';
    }
    $e = getEvent($userId, $id);
    $moved = updateEvent($userId, $id, $e['type'], $e['name'], $e['description'], $newDeadline, $e['weight']);
    jsonOut(['ok' => true, 'event' => $moved]);
}
jsonError('unknown action', 400);

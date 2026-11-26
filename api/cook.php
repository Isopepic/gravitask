<?php
// Author: Ismaël
require_once 'util.php';
require_once 'auth.php';
require_once 'db.php';

function loadBands() {
    $json = file_get_contents(__DIR__ . '/../shared/bands.json');
    return json_decode($json, true);
}

function bandFor($hoursLeft, $bands) {
    foreach ($bands['scales'] as $scale) {
        if ($hoursLeft <= $scale['rangeHours']) {
            return $scale;
        }
    }
    return null;
}

function cookScore($events, $bands) {
    $score = 0;
    foreach ($events as $e) {
        $hoursLeft = (strtotime($e['deadline']) - time()) / 3600;
        $scale = bandFor($hoursLeft, $bands);
        $coef = $scale ? $scale['coef'] : $bands['farCoef'];
        $score += $e['weight'] * $coef;
    }
    return $score;
}

function faceFor($score) {
    if ($score < 20) return 0;
    if ($score < 50) return 1;
    if ($score < 90) return 2;
    return 3;
}

$userId = requireUser();
$events = listEvents($userId);
$bands = loadBands();
$score = cookScore($events, $bands);
jsonOut(['score' => $score, 'face' => faceFor($score)]);

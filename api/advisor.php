<?php
// Author: Ismaël
require_once 'util.php';
require_once 'auth.php';
require_once 'db.php';

function rateLimitOk($userId) {
    $file = sys_get_temp_dir() . '/gravitask_advisor_' . $userId;
    if (file_exists($file) && time() - filemtime($file) < 60) return false;
    touch($file); return true;
}
function buildPrompt($events) {
    $list = [];
    foreach ($events as $e){
        $list[] = ['name' => $e['name'], 'type' => $e['type'], 'deadline' => $e['deadline'], 'weight' => $e['weight']];
    }
    $s = "You are a student advisor who reduces the student's stress and anxiety. Today's date and time: " . date('Y-m-d H:i') . ". The events: " . json_encode($list) . ". In at most 3 short sentences, say what to do first and why. Calm and concrete.";
    return $s;
}
function askLLM($prompt) {

 $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
$body = json_encode(['contents' => [['parts' => [['text' => $prompt]]]]]);
 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'x-goog-api-key: ' . LLM_KEY]);
   $reply = curl_exec($ch);
  curl_close($ch);
  if ($reply == false) { return null; }
  $data = json_decode($reply, true);
  return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

}
function fallbackTip($events) {
    if (!$events) return "You have nothing to do, maybe go touch some grass";
    $first = $events[0];
    return 'Start with "' . $first['name'] . '", it has the closest deadline (' . $first['deadline'] . ').';
}

$userId = requireUser();
if (!rateLimitOk($userId)) {
    jsonError('wait a minute before asking again', 429);
}
$events = listEvents($userId);
if (!$events) {
    jsonOut(['advice' => fallbackTip($events)]);
}
$advice = askLLM(buildPrompt($events));
if (!$advice) {
    $advice = fallbackTip($events);
}
jsonOut(['advice' => $advice]);

<?php
// Author: Mauricio Letort

require_once __DIR__ . '/../config.php';

function db() {
    static $conn = null;

    if ($conn === null) {
        if ($conn === null) {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            $conn->set_charset('utf8mb4');
            }
        }
        return $conn;
    }

function run($sql, $types = "", $params = []) {

  $stmt = db() -> prepare($sql);
  if ($types !== "") {
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();

  $result = $stmt->get_result();
  if ($result !== false) {
    return $result->fetch_all(MYSQLI_ASSOC);
  }
  if ($stmt->insert_id) {
    return $stmt->insert_id;
  }
  return $stmt->affected_rows;
}

function createUser($name, $hash) {
    $sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
    return run($sql, "ss", [$name, $hash]);
}

function getUserByName($name) {
    $sql = "SELECT * FROM users WHERE username = ?";
    $rows = run($sql, "s", [$name]);
    return $rows ? $rows[0] : null;
}

function getUserByTicket($ticket) {
    $sql = "SELECT * FROM users WHERE ticket = ?";
    $rows = run($sql, "s", [$ticket]);
    return $rows ? $rows[0] : null;
}

function setTicket($userId, $ticket) {
    $sql = "UPDATE users SET ticket = ? WHERE id = ?";
    return run($sql, "si", [$ticket, $userId]);
}

function listEvents($userId, $from=null, $to=null) {
    if ($from !== null && $to !== null) {
        $sql = "SELECT id, type, name, description, deadline, weight FROM events
                WHERE user_id = ? AND status = 'open' AND deadline BETWEEN ? AND ?
                ORDER BY deadline ASC";

        return run($sql, "iss", [$userId, $from, $to]);} 
    else 
    {
        $sql = "SELECT id, type, name, description, deadline, weight FROM events
                WHERE user_id = ? AND status = 'open'
                ORDER BY deadline ASC";
        return run($sql, "i", [$userId]);
    }
}

function getEvent($userId, $id) {
    $sql = "SELECT id, type, name, description, deadline, weight FROM events WHERE id = ? AND user_id = ?";
    $rows = run($sql, "ii", [$id, $userId]);
    return $rows ? $rows[0] : null;
}

function addEvent($userId, $event) {
    $sql = "INSERT INTO events (user_id, type, name, description, deadline, weight) VALUES (?, ?, ?, ?, ?, ?)";
    $id = run($sql, "issssi", [$userId, $event['type'], $event['name'], $event['description'], $event['deadline'], $event['weight']]);
    return getEvent($userId, $id);
}

function updateEvent($userId, $id, $fields) {
    $sql = "UPDATE events SET type = ?, name = ?, description = ?, deadline = ?, weight = ? WHERE id = ? AND user_id = ?";
    run($sql, "ssssiii", [$fields['type'], $fields['name'], $fields['description'], $fields['deadline'], $fields['weight'], $id, $userId]);
    return getEvent($userId, $id);
}

function deleteEvent($userId, $id) {
    $sql = "DELETE FROM events WHERE id = ? AND user_id = ?";
    return run($sql, "ii", [$id, $userId]);
}

function setStatus($userId, $id, $status) {
    $sql = "UPDATE events SET status = ? WHERE id = ? AND user_id = ?";
    return run($sql, "sii", [$status, $id, $userId]);
}

function validateEvent($data) {
  $type = $data['type'] ?? '';
  if ($type !== 'task' && $type !== 'happening') {
    return "type must be either task or happening";
  }

  $name = trim($data['name'] ?? '');
  if ($name === '') {
    return "name can't be empty";
  }

  $weight = (int) ($data['weight'] ?? 0);
  if ($weight < 1 || $weight > 10) {
    return "weight must be between 1 and 10, 1 being the least important and 10 the most important";
  }

  $deadline = trim($data['deadline'] ?? '');
  $deadline = str_replace('T', ' ', $deadline);                     
  if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $deadline)) {  
    $deadline .= ':00';}

  if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
    $deadline .= ' 23:59:00'; 
  }
  $dt = DateTime::createFromFormat('Y-m-d H:i:s', $deadline);
  if (!$dt || $dt->format('Y-m-d H:i:s') !== $deadline) {
    return "Please pick a valid date and time";
  }

  return [
    'type'        => $type,
    'name'        => $name,
    'description' => trim($data['description'] ?? ''),
    'deadline'    => $deadline,
    'weight'      => $weight,
  ];
}
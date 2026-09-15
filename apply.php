<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/telegram.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('contacts.php');
}

$user = current_user();
$course = trim($_POST['course'] ?? '');
$message = trim($_POST['message'] ?? '');
$isConsultation = (strpos($course, 'консультац') !== false) || $course === '';

$lead = [
    'id' => uid('l_'),
    'user_id' => $user['id'],
    'name' => $user['name'],
    'phone' => $user['phone'],
    'course' => $course,
    'message' => $message,
    'type' => $isConsultation ? 'consultation' : 'course',
    'status' => 'new',
    'created_at' => date('c'),
];

$leads = read_json('leads.json', []);
$leads[] = $lead;
write_json('leads.json', $leads);

notify_new_lead($lead);

redirect('contacts.php?sent=1');

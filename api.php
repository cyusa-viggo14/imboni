<?php
// ============================================================
//  IMBONI — JSON API used by the dashboard pages
//  (login & register stay in index.php)
// ============================================================
require_once 'config.php';
header('Content-Type: application/json');

function out(string $status, string $message = '', array $extra = []): void {
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') out('error', 'POST only.');
if (!isLoggedIn()) out('error', 'Not logged in.');
if (($_SESSION['is_approved'] ?? 0) != 1) out('error', 'Your account is not approved.');

$action  = $_POST['action'] ?? '';
$role    = $_SESSION['role'] ?? '';
$isHead  = $role === 'headmaster';
$user_id = (int)$_SESSION['user_id'];

switch ($action) {

    // ---- Record a conduct deduction (any approved role) ------
    case 'reduce_marks':
        $student_id = (int)($_POST['student_id'] ?? 0);
        $law_id     = (int)($_POST['law_id'] ?? 0);
        if ($student_id <= 0 || $law_id <= 0) out('error', 'Please select a student and a law.');

        $stmt = $conn->prepare("SELECT reduction_marks FROM laws WHERE id = ?");
        $stmt->bind_param("i", $law_id);
        $stmt->execute();
        $law = $stmt->get_result()->fetch_assoc();
        if (!$law) out('error', 'Invalid law.');
        $marks = (int)$law['reduction_marks'];

        $s = $conn->prepare("SELECT name, current_conduct_marks FROM students WHERE id = ?");
        $s->bind_param("i", $student_id);
        $s->execute();
        $row = $s->get_result()->fetch_assoc();
        if (!$row) out('error', 'Student not found.');
        if ((int)$row['current_conduct_marks'] < $marks) {
            out('error', 'Student does not have enough conduct marks to deduct.');
        }

        $update = $conn->prepare("UPDATE students SET current_conduct_marks = current_conduct_marks - ? WHERE id = ?");
        $update->bind_param("ii", $marks, $student_id);
        if (!$update->execute()) out('error', 'Update failed: ' . $update->error);

        $log = $conn->prepare("INSERT INTO conduct_logs (student_id, user_id, law_id, marks_deducted) VALUES (?, ?, ?, ?)");
        $log->bind_param("iiii", $student_id, $user_id, $law_id, $marks);
        if (!$log->execute()) out('error', 'Log failed: ' . $log->error);

        $newMarks = (int)$row['current_conduct_marks'] - $marks;
        out('success', "Deducted {$marks} marks from {$row['name']}. New total: {$newMarks}.", ['new_marks' => $newMarks]);

    // ---- Add a law (headmaster) ------------------------------
    case 'add_law':
        if (!$isHead) out('error', 'Unauthorized.');
        $name  = trim($_POST['name'] ?? '');
        $marks = (int)($_POST['marks'] ?? 0);
        if ($name === '')  out('error', 'Law name is required.');
        if ($marks < 1)    out('error', 'Marks must be at least 1.');

        $stmt = $conn->prepare("INSERT INTO laws (name, reduction_marks) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $marks);
        $stmt->execute() ? out('success', 'Law added successfully.') : out('error', 'Add law failed: ' . $stmt->error);
        break;

    // ---- Delete a law (headmaster) ---------------------------
    case 'delete_law':
        if (!$isHead) out('error', 'Unauthorized.');
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) out('error', 'Invalid law id.');
        $stmt = $conn->prepare("DELETE FROM laws WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute() ? out('success', 'Law deleted.') : out('error', 'Delete failed: ' . $stmt->error);
        break;

    // ---- Add a student (headmaster, DOD, DOS) ----------------
    case 'add_student':
        if (!in_array($role, ['headmaster', 'dod', 'dos'], true)) out('error', 'Unauthorized.');
        $name  = trim($_POST['name'] ?? '');
        $grade = trim($_POST['grade'] ?? '');
        if ($name === '' || $grade === '') out('error', 'Name and grade are required.');

        $stmt = $conn->prepare("INSERT INTO students (name, grade, current_conduct_marks) VALUES (?, ?, 100)");
        $stmt->bind_param("ss", $name, $grade);
        $stmt->execute() ? out('success', 'Student added.') : out('error', 'Add student failed: ' . $stmt->error);
        break;

    // ---- Approve a pending user (headmaster) -----------------
    case 'approve_user':
        if (!$isHead) out('error', 'Unauthorized.');
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) out('error', 'Invalid user id.');
        $stmt = $conn->prepare("UPDATE users SET is_approved = 1 WHERE id = ? AND role != 'headmaster'");
        $stmt->bind_param("i", $id);
        $stmt->execute() ? out('success', 'User approved.') : out('error', 'Approve failed: ' . $stmt->error);
        break;

    // ---- Reject (delete) a pending user (headmaster) ---------
    case 'reject_user':
        if (!$isHead) out('error', 'Unauthorized.');
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) out('error', 'Invalid user id.');
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND is_approved = 0 AND role != 'headmaster'");
        $stmt->bind_param("i", $id);
        $stmt->execute() ? out('success', 'Registration rejected.') : out('error', 'Reject failed: ' . $stmt->error);
        break;

    default:
        out('error', 'Unknown action.');
}

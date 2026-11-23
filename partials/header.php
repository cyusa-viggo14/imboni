<?php
// Shared header + sidebar for every dashboard page.
// Set $pageTitle (and optionally $pageSubtitle) BEFORE including this file.
require_once __DIR__ . '/../config.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}
if (($_SESSION['is_approved'] ?? 0) != 1) {
    header('Location: logout.php');
    exit;
}

$role      = $_SESSION['role'] ?? '';
$isHead    = $role === 'headmaster';
$userName  = $_SESSION['user_name'] ?? 'User';
$current   = basename($_SERVER['SCRIPT_NAME']);
$pageTitle = $pageTitle ?? 'Dashboard';
$pageSubtitle = $pageSubtitle ?? '';

$roleLabels = [
    'headmaster' => 'Headmaster',
    'dod'        => 'Director of Discipline',
    'dos'        => 'Director of Studies',
    'teacher'    => 'Teacher',
    'animation'  => 'Animation',
];
$roleLabel = $roleLabels[$role] ?? strtoupper($role);

$pendingCount = 0;
if ($isHead) {
    $r = $conn->query("SELECT COUNT(*) FROM users WHERE is_approved = 0");
    if ($r) $pendingCount = (int)$r->fetch_row()[0];
}

// [file, label, icon, headmaster-only?]
$nav = [
    ['section' => 'Main'],
    ['dashboard.php', 'Overview',        'fa-gauge-high',         false],
    ['students.php',  'Students',        'fa-user-graduate',      false],
    ['section' => 'Discipline'],
    ['conduct.php',   'Record Conduct',  'fa-gavel',              false],
    ['logs.php',      'Conduct Logs',    'fa-clock-rotate-left',  false],
    ['laws.php',      'School Laws',     'fa-scale-balanced',     false],
    ['section' => 'Administration'],
    ['users.php',     'Staff Approvals', 'fa-user-check',         true],
];

function initials(string $name): string {
    $parts = preg_split('/\s+/', trim($name));
    $out = '';
    foreach (array_slice($parts, 0, 2) as $p) $out .= mb_strtoupper(mb_substr($p, 0, 1));
    return $out ?: '?';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> · IMBONI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body class="app">

<aside class="sidebar" id="sidebar">
    <a href="dashboard.php" class="brand"><i class="fa-solid fa-graduation-cap"></i><span>IMBONI</span></a>

    <nav class="side-nav">
        <?php foreach ($nav as $item): ?>
            <?php if (isset($item['section'])): ?>
                <?php
                // hide "Administration" heading for non-headmasters
                if ($item['section'] === 'Administration' && !$isHead) continue;
                ?>
                <div class="nav-section"><?= htmlspecialchars($item['section']) ?></div>
            <?php else:
                [$file, $label, $icon, $headOnly] = $item;
                if ($headOnly && !$isHead) continue; ?>
                <a href="<?= $file ?>" class="nav-link <?= $current === $file ? 'active' : '' ?>">
                    <i class="fa-solid <?= $icon ?>"></i>
                    <span><?= htmlspecialchars($label) ?></span>
                    <?php if ($file === 'users.php' && $pendingCount > 0): ?>
                        <em class="nav-badge"><?= $pendingCount ?></em>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>

    <div class="side-foot">
        <a href="index.php" class="nav-link"><i class="fa-solid fa-house"></i><span>Back to Website</span></a>
        <a href="logout.php" class="nav-link danger"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </div>
</aside>
<div class="scrim" id="scrim"></div>

<div class="main">
    <header class="topbar">
        <button class="menu-btn" id="menuBtn" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
        <div class="page-heading">
            <h1><?= htmlspecialchars($pageTitle) ?></h1>
            <?php if ($pageSubtitle): ?><p><?= htmlspecialchars($pageSubtitle) ?></p><?php endif; ?>
        </div>
        <div class="user-chip">
            <div class="avatar"><?= htmlspecialchars(initials($userName)) ?></div>
            <div class="who">
                <strong><?= htmlspecialchars($userName) ?></strong>
                <small><?= htmlspecialchars($roleLabel) ?></small>
            </div>
        </div>
    </header>

    <main class="content">

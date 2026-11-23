<?php
$pageTitle    = 'Overview';
$pageSubtitle = 'School discipline at a glance';
require_once __DIR__ . '/partials/header.php';

function scalar(mysqli $c, string $sql) {
    $r = $c->query($sql);
    return $r ? $r->fetch_row()[0] : 0;
}

$totalStudents = (int)scalar($conn, "SELECT COUNT(*) FROM students");
$totalLaws     = (int)scalar($conn, "SELECT COUNT(*) FROM laws");
$totalLogs     = (int)scalar($conn, "SELECT COUNT(*) FROM conduct_logs");
$avgMarks      = (int)round((float)scalar($conn, "SELECT AVG(current_conduct_marks) FROM students"));
$logsToday     = (int)scalar($conn, "SELECT COUNT(*) FROM conduct_logs WHERE DATE(logged_at) = CURDATE()");
$marksLost     = (int)scalar($conn, "SELECT COALESCE(SUM(marks_deducted),0) FROM conduct_logs");

// Conduct bands
$good = (int)scalar($conn, "SELECT COUNT(*) FROM students WHERE current_conduct_marks >= 80");
$warn = (int)scalar($conn, "SELECT COUNT(*) FROM students WHERE current_conduct_marks BETWEEN 50 AND 79");
$crit = (int)scalar($conn, "SELECT COUNT(*) FROM students WHERE current_conduct_marks < 50");
$maxBand = max(1, $good, $warn, $crit);

// Recent activity
$recent = $conn->query(
    "SELECT cl.logged_at, cl.marks_deducted, s.name AS student, l.name AS law, u.name AS staff
     FROM conduct_logs cl
     JOIN students s ON s.id = cl.student_id
     JOIN laws l     ON l.id = cl.law_id
     JOIN users u    ON u.id = cl.user_id
     ORDER BY cl.logged_at DESC, cl.id DESC LIMIT 6"
);

// Students needing attention (lowest marks)
$atRisk = $conn->query("SELECT name, grade, current_conduct_marks FROM students ORDER BY current_conduct_marks ASC, name ASC LIMIT 5");

// Most common infractions
$topLaws = $conn->query(
    "SELECT l.name, COUNT(*) AS c FROM conduct_logs cl JOIN laws l ON l.id = cl.law_id
     GROUP BY l.id, l.name ORDER BY c DESC LIMIT 5"
);
$topMax = 1;
$topRows = [];
if ($topLaws) { while ($r = $topLaws->fetch_assoc()) { $topRows[] = $r; $topMax = max($topMax, (int)$r['c']); } }

function band(int $m): array {
    if ($m >= 80) return ['good', 'Good', ''];
    if ($m >= 50) return ['warn', 'Warning', 'warn'];
    return ['crit', 'Critical', 'crit'];
}
$hour = (int)date('G');
$greet = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
?>

<section class="card welcome">
    <div>
        <h2><?= $greet ?>, <?= htmlspecialchars(explode(' ', $userName)[0]) ?> 👋</h2>
        <p><?= $logsToday ?> conduct record<?= $logsToday === 1 ? '' : 's' ?> logged today<?= ($isHead && $pendingCount) ? " · {$pendingCount} staff awaiting approval" : '' ?>.</p>
    </div>
    <div class="actions">
        <a href="conduct.php" class="btn light"><i class="fa-solid fa-gavel"></i> Record Conduct</a>
        <?php if ($isHead && $pendingCount): ?>
            <a href="users.php" class="btn light"><i class="fa-solid fa-user-check"></i> Review Approvals</a>
        <?php endif; ?>
    </div>
</section>

<section class="grid stats">
    <div class="card stat"><div class="ico"><i class="fa-solid fa-user-graduate"></i></div><div><h3>Students</h3><div class="num"><?= $totalStudents ?></div></div></div>
    <div class="card stat"><div class="ico green"><i class="fa-solid fa-heart-pulse"></i></div><div><h3>Avg. Conduct</h3><div class="num"><?= $avgMarks ?><small class="muted" style="font-size:14px">/100</small></div></div></div>
    <div class="card stat"><div class="ico amber"><i class="fa-solid fa-clock-rotate-left"></i></div><div><h3>Total Records</h3><div class="num"><?= $totalLogs ?></div></div></div>
    <div class="card stat"><div class="ico red"><i class="fa-solid fa-arrow-trend-down"></i></div><div><h3>Marks Deducted</h3><div class="num"><?= $marksLost ?></div></div></div>
</section>

<section class="grid two">
    <div class="card">
        <div class="card-head"><h2>Recent Conduct Activity</h2><a class="link" href="logs.php">View all →</a></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Student</th><th>Infraction</th><th>Marks</th><th>By</th><th>When</th></tr></thead>
                <tbody>
                <?php if ($recent && $recent->num_rows): while ($r = $recent->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($r['student']) ?></strong></td>
                        <td><?= htmlspecialchars($r['law']) ?></td>
                        <td class="neg">-<?= (int)$r['marks_deducted'] ?></td>
                        <td class="muted"><?= htmlspecialchars($r['staff']) ?></td>
                        <td class="muted"><?= date('d M, H:i', strtotime($r['logged_at'])) ?></td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="5" class="empty">No conduct records yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h2>Conduct Health</h2><a class="link" href="students.php">Students →</a></div>
        <div class="hbar"><div class="row"><span>Good (80–100)</span><span><?= $good ?></span></div><div class="meter"><span style="width:<?= $good / $maxBand * 100 ?>%"></span></div></div>
        <div class="hbar"><div class="row"><span>Warning (50–79)</span><span><?= $warn ?></span></div><div class="meter"><span class="warn" style="width:<?= $warn / $maxBand * 100 ?>%"></span></div></div>
        <div class="hbar"><div class="row"><span>Critical (&lt;50)</span><span><?= $crit ?></span></div><div class="meter"><span class="crit" style="width:<?= $crit / $maxBand * 100 ?>%"></span></div></div>
    </div>
</section>

<section class="grid two-eq">
    <div class="card">
        <div class="card-head"><h2>Needs Attention</h2><span class="muted" style="font-size:13px">Lowest conduct marks</span></div>
        <div class="table-wrap">
            <table>
                <tbody>
                <?php if ($atRisk && $atRisk->num_rows): while ($s = $atRisk->fetch_assoc()): [$cls, $lbl, $mcls] = band((int)$s['current_conduct_marks']); ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($s['name']) ?></strong><br><span class="muted" style="font-size:12px"><?= htmlspecialchars($s['grade']) ?></span></td>
                        <td><div class="mark-cell"><div class="meter"><span class="<?= $mcls ?>" style="width:<?= max(0, min(100, (int)$s['current_conduct_marks'])) ?>%"></span></div><b><?= (int)$s['current_conduct_marks'] ?></b></div></td>
                        <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td class="empty">No students yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h2>Most Common Infractions</h2><a class="link" href="laws.php">School laws →</a></div>
        <?php if ($topRows): foreach ($topRows as $t): ?>
            <div class="hbar"><div class="row"><span><?= htmlspecialchars($t['name']) ?></span><span><?= (int)$t['c'] ?>×</span></div><div class="meter"><span style="width:<?= (int)$t['c'] / $topMax * 100 ?>%"></span></div></div>
        <?php endforeach; else: ?>
            <p class="empty">Nothing recorded yet.</p>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

<?php
$pageTitle    = 'Conduct Logs';
$pageSubtitle = 'Full history of every deduction';
require_once __DIR__ . '/partials/header.php';

$logs = $conn->query(
    "SELECT cl.id, cl.logged_at, cl.marks_deducted,
            s.name AS student, s.grade, l.name AS law, u.name AS staff
     FROM conduct_logs cl
     JOIN students s ON s.id = cl.student_id
     JOIN laws l     ON l.id = cl.law_id
     JOIN users u    ON u.id = cl.user_id
     ORDER BY cl.logged_at DESC, cl.id DESC"
);

$pageScript = <<<'JS'
<script>
document.getElementById('q').addEventListener('input', e => {
    const t = e.target.value.toLowerCase();
    document.querySelectorAll('#rows tr[data-s]').forEach(tr => tr.style.display = tr.dataset.s.includes(t) ? '' : 'none');
});
</script>
JS;
?>

<section class="card">
    <div class="toolbar"><input type="search" id="q" placeholder="Search by student, infraction or staff…"></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Date</th><th>Student</th><th>Infraction</th><th>Marks</th><th>Recorded by</th></tr></thead>
            <tbody id="rows">
            <?php if ($logs && $logs->num_rows): while ($r = $logs->fetch_assoc()): ?>
                <tr data-s="<?= htmlspecialchars(mb_strtolower($r['student'] . ' ' . $r['law'] . ' ' . $r['staff'])) ?>">
                    <td class="muted"><?= (int)$r['id'] ?></td>
                    <td class="muted"><?= date('d M Y, H:i', strtotime($r['logged_at'])) ?></td>
                    <td><strong><?= htmlspecialchars($r['student']) ?></strong><br><span class="muted" style="font-size:12px"><?= htmlspecialchars($r['grade']) ?></span></td>
                    <td><?= htmlspecialchars($r['law']) ?></td>
                    <td class="neg">−<?= (int)$r['marks_deducted'] ?></td>
                    <td><?= htmlspecialchars($r['staff']) ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="6" class="empty">No conduct records yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

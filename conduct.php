<?php
$pageTitle    = 'Record Conduct';
$pageSubtitle = 'Deduct conduct marks for an infraction';
require_once __DIR__ . '/partials/header.php';

$students = $conn->query("SELECT id, name, grade, current_conduct_marks FROM students ORDER BY name ASC");
$laws     = $conn->query("SELECT id, name, reduction_marks FROM laws ORDER BY name ASC");
$preselect = (int)($_GET['student'] ?? 0);

$recent = $conn->query(
    "SELECT cl.logged_at, cl.marks_deducted, s.name AS student, l.name AS law
     FROM conduct_logs cl JOIN students s ON s.id = cl.student_id JOIN laws l ON l.id = cl.law_id
     WHERE cl.user_id = " . (int)$_SESSION['user_id'] . "
     ORDER BY cl.logged_at DESC, cl.id DESC LIMIT 5"
);

$pageScript = <<<'JS'
<script>
const form = document.getElementById('reduce-form');
const sSel = form.student_id, lSel = form.law_id;
const prev = document.getElementById('preview');

function updatePreview() {
    const s = sSel.selectedOptions[0], l = lSel.selectedOptions[0];
    if (!sSel.value || !lSel.value) { prev.classList.remove('show'); return; }
    const cur = +s.dataset.marks, cut = +l.dataset.marks, after = cur - cut;
    document.getElementById('pv-before').textContent = cur;
    document.getElementById('pv-after').textContent  = after;
    document.getElementById('pv-note').textContent   = after < 0
        ? 'Not enough marks — this deduction will be refused.'
        : s.dataset.name + ' loses ' + cut + ' marks for “' + l.dataset.name + '”.';
    prev.classList.add('show');
}
sSel.addEventListener('change', updatePreview);
lSel.addEventListener('change', updatePreview);
updatePreview();

form.addEventListener('submit', async e => {
    e.preventDefault();
    if (!confirm('Confirm this deduction?')) return;
    const data = await api('reduce_marks', { student_id: sSel.value, law_id: lSel.value });
    showAlert('reduce-alert', data);
    if (data.status === 'success') setTimeout(() => location.href = 'conduct.php', 1400);
});
</script>
JS;
?>

<div class="grid two">
    <section class="card">
        <div class="card-head"><h2>New Deduction</h2></div>
        <div id="reduce-alert" class="alert"></div>
        <form id="reduce-form">
            <div class="form-grid">
                <div class="form-group">
                    <label>Student</label>
                    <select name="student_id" required>
                        <option value="">— Choose student —</option>
                        <?php if ($students) while ($s = $students->fetch_assoc()): ?>
                            <option value="<?= (int)$s['id'] ?>"
                                    data-marks="<?= (int)$s['current_conduct_marks'] ?>"
                                    data-name="<?= htmlspecialchars($s['name']) ?>"
                                    <?= $preselect === (int)$s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name']) ?> · <?= htmlspecialchars($s['grade']) ?> (<?= (int)$s['current_conduct_marks'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Infraction</label>
                    <select name="law_id" required>
                        <option value="">— Choose infraction —</option>
                        <?php if ($laws) while ($l = $laws->fetch_assoc()): ?>
                            <option value="<?= (int)$l['id'] ?>"
                                    data-marks="<?= (int)$l['reduction_marks'] ?>"
                                    data-name="<?= htmlspecialchars($l['name']) ?>">
                                <?= htmlspecialchars($l['name']) ?> (−<?= (int)$l['reduction_marks'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="preview" id="preview">
                <div class="flow"><span id="pv-before">0</span><i class="fa-solid fa-arrow-right"></i><span class="after" id="pv-after">0</span></div>
                <small id="pv-note"></small>
            </div>

            <div style="margin-top:20px"><button type="submit" class="btn danger"><i class="fa-solid fa-gavel"></i> Confirm Reduction</button></div>
        </form>
    </section>

    <section class="card">
        <div class="card-head"><h2>Your Recent Records</h2><a class="link" href="logs.php">All logs →</a></div>
        <?php if ($recent && $recent->num_rows): while ($r = $recent->fetch_assoc()): ?>
            <div style="padding:10px 0;border-bottom:1px solid #f0f3f8">
                <strong><?= htmlspecialchars($r['student']) ?></strong> <span class="neg">−<?= (int)$r['marks_deducted'] ?></span><br>
                <span class="muted" style="font-size:13px"><?= htmlspecialchars($r['law']) ?> · <?= date('d M, H:i', strtotime($r['logged_at'])) ?></span>
            </div>
        <?php endwhile; else: ?>
            <p class="empty">You haven't recorded anything yet.</p>
        <?php endif; ?>
    </section>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

<?php
$pageTitle    = 'School Laws';
$pageSubtitle = 'Infractions and the marks they cost';
require_once __DIR__ . '/partials/header.php';

$laws = $conn->query(
    "SELECT l.id, l.name, l.reduction_marks, COUNT(cl.id) AS used
     FROM laws l LEFT JOIN conduct_logs cl ON cl.law_id = l.id
     GROUP BY l.id, l.name, l.reduction_marks
     ORDER BY l.reduction_marks ASC, l.name ASC"
);

$pageScript = <<<'JS'
<script>
document.getElementById('law-form')?.addEventListener('submit', async e => {
    e.preventDefault();
    const f = e.target;
    const data = await api('add_law', { name: f.name.value, marks: f.marks.value });
    showAlert('law-alert', data);
    if (data.status === 'success') setTimeout(() => location.reload(), 900);
});
async function deleteLaw(id, name, used) {
    const warn = used > 0 ? '\n\nWARNING: ' + used + ' conduct log(s) using this law will also be deleted.' : '';
    if (!confirm('Delete "' + name + '"?' + warn)) return;
    const data = await api('delete_law', { id });
    if (data.status === 'success') location.reload(); else alert(data.message);
}
</script>
JS;
?>

<?php if ($isHead): ?>
<section class="card" style="margin-bottom:20px">
    <div class="card-head"><h2>Add a Law</h2></div>
    <div id="law-alert" class="alert"></div>
    <form id="law-form" class="form-grid">
        <div class="form-group"><label>Infraction name</label><input type="text" name="name" required placeholder="e.g. Using phone in class"></div>
        <div class="form-group"><label>Marks to deduct</label><input type="number" name="marks" required min="1" placeholder="e.g. 5"></div>
        <div class="form-group"><button type="submit" class="btn"><i class="fa-solid fa-plus"></i> Add Law</button></div>
    </form>
</section>
<?php endif; ?>

<section class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Infraction</th><th>Deduction</th><th>Times used</th><?php if ($isHead): ?><th></th><?php endif; ?></tr></thead>
            <tbody>
            <?php if ($laws && $laws->num_rows): while ($l = $laws->fetch_assoc()): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($l['name']) ?></strong></td>
                    <td class="neg">−<?= (int)$l['reduction_marks'] ?> marks</td>
                    <td class="muted"><?= (int)$l['used'] ?></td>
                    <?php if ($isHead): ?>
                        <td style="text-align:right"><button class="btn sm danger" onclick='deleteLaw(<?= (int)$l['id'] ?>, <?= json_encode($l['name'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= (int)$l['used'] ?>)'><i class="fa-solid fa-trash"></i></button></td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="4" class="empty">No laws defined yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

<?php
$pageTitle    = 'Students';
$pageSubtitle = 'All students and their current conduct marks';
require_once __DIR__ . '/partials/header.php';

$canAdd   = in_array($role, ['headmaster', 'dod', 'dos'], true);
$students = $conn->query("SELECT id, name, grade, current_conduct_marks FROM students ORDER BY name ASC");
$grades   = $conn->query("SELECT DISTINCT grade FROM students ORDER BY grade ASC");

function band(int $m): array {
    if ($m >= 80) return ['good', 'Good', ''];
    if ($m >= 50) return ['warn', 'Warning', 'warn'];
    return ['crit', 'Critical', 'crit'];
}

$pageScript = <<<'JS'
<script>
const q = document.getElementById('q'), g = document.getElementById('g');
function filterRows() {
    const term = q.value.toLowerCase(), grade = g.value;
    document.querySelectorAll('#rows tr[data-name]').forEach(tr => {
        const ok = tr.dataset.name.includes(term) && (!grade || tr.dataset.grade === grade);
        tr.style.display = ok ? '' : 'none';
    });
}
q.addEventListener('input', filterRows); g.addEventListener('change', filterRows);

document.getElementById('student-form')?.addEventListener('submit', async e => {
    e.preventDefault();
    const f = e.target;
    const data = await api('add_student', { name: f.name.value, grade: f.grade.value });
    showAlert('student-alert', data);
    if (data.status === 'success') setTimeout(() => location.reload(), 900);
});
</script>
JS;
?>

<?php if ($canAdd): ?>
<section class="card" style="margin-bottom:20px">
    <div class="card-head"><h2>Add a Student</h2></div>
    <div id="student-alert" class="alert"></div>
    <form id="student-form" class="form-grid">
        <div class="form-group"><label>Full name</label><input type="text" name="name" required placeholder="e.g. Aline Uwase"></div>
        <div class="form-group"><label>Grade</label><input type="text" name="grade" required placeholder="e.g. Grade 10"></div>
        <div class="form-group"><button type="submit" class="btn"><i class="fa-solid fa-plus"></i> Add Student</button></div>
    </form>
</section>
<?php endif; ?>

<section class="card">
    <div class="toolbar">
        <input type="search" id="q" placeholder="Search students…">
        <select id="g">
            <option value="">All grades</option>
            <?php if ($grades) while ($gr = $grades->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($gr['grade']) ?>"><?= htmlspecialchars($gr['grade']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Name</th><th>Grade</th><th>Conduct marks</th><th>Status</th><th></th></tr></thead>
            <tbody id="rows">
            <?php if ($students && $students->num_rows): $i = 0; while ($s = $students->fetch_assoc()): $i++;
                $m = (int)$s['current_conduct_marks']; [$cls, $lbl, $mcls] = band($m); ?>
                <tr data-name="<?= htmlspecialchars(mb_strtolower($s['name'])) ?>" data-grade="<?= htmlspecialchars($s['grade']) ?>">
                    <td class="muted"><?= $i ?></td>
                    <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                    <td><?= htmlspecialchars($s['grade']) ?></td>
                    <td><div class="mark-cell"><div class="meter"><span class="<?= $mcls ?>" style="width:<?= max(0, min(100, $m)) ?>%"></span></div><b><?= $m ?></b></div></td>
                    <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td>
                    <td style="text-align:right"><a class="btn sm ghost" href="conduct.php?student=<?= (int)$s['id'] ?>"><i class="fa-solid fa-gavel"></i> Record</a></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="6" class="empty">No students yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

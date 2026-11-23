<?php
$pageTitle    = 'Staff Approvals';
$pageSubtitle = 'Approve or reject new staff registrations';
require_once __DIR__ . '/partials/header.php';

if (!$isHead) { header('Location: dashboard.php'); exit; }

$pending  = $conn->query("SELECT id, name, email, role, created_at FROM users WHERE is_approved = 0 ORDER BY created_at DESC");
$approved = $conn->query("SELECT name, email, role, created_at FROM users WHERE is_approved = 1 ORDER BY name ASC");

$pageScript = <<<'JS'
<script>
async function act(action, id) {
    if (action === 'reject_user' && !confirm('Reject and delete this registration?')) return;
    const data = await api(action, { id });
    if (data.status === 'success') location.reload(); else alert(data.message);
}
</script>
JS;
?>

<section class="card" style="margin-bottom:20px">
    <div class="card-head"><h2>Pending Registrations <?= $pendingCount ? "($pendingCount)" : '' ?></h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Registered</th><th></th></tr></thead>
            <tbody>
            <?php if ($pending && $pending->num_rows): while ($u = $pending->fetch_assoc()): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                    <td class="muted"><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge role"><?= strtoupper(htmlspecialchars($u['role'])) ?></span></td>
                    <td class="muted"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td style="text-align:right;white-space:nowrap">
                        <button class="btn sm green" onclick="act('approve_user', <?= (int)$u['id'] ?>)"><i class="fa-solid fa-check"></i> Approve</button>
                        <button class="btn sm danger" onclick="act('reject_user', <?= (int)$u['id'] ?>)"><i class="fa-solid fa-xmark"></i></button>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="5" class="empty">🎉 No pending registrations.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <div class="card-head"><h2>Active Staff</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr></thead>
            <tbody>
            <?php if ($approved) while ($u = $approved->fetch_assoc()): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                    <td class="muted"><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge role"><?= strtoupper(htmlspecialchars($u['role'])) ?></span></td>
                    <td class="muted"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

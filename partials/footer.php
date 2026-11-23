    </main>
</div>

<script>
    // Sidebar toggle (mobile)
    const sidebar = document.getElementById('sidebar');
    const scrim   = document.getElementById('scrim');
    function toggleSidebar(open) {
        sidebar.classList.toggle('open', open);
        scrim.classList.toggle('show', open);
    }
    document.getElementById('menuBtn').addEventListener('click', () => toggleSidebar(!sidebar.classList.contains('open')));
    scrim.addEventListener('click', () => toggleSidebar(false));

    // Tiny helper: POST an action to api.php and return parsed JSON
    function api(action, data) {
        const fd = new FormData();
        fd.append('action', action);
        Object.entries(data || {}).forEach(([k, v]) => fd.append(k, v));
        return fetch('api.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .catch(err => ({ status: 'error', message: 'Network error: ' + err.message }));
    }

    function showAlert(id, data) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = data.message || '';
        el.className = 'alert ' + (data.status === 'success' ? 'alert-success' : 'alert-error');
        el.style.display = 'block';
    }
</script>
<?php if (!empty($pageScript)) echo $pageScript; ?>
</body>
</html>

<?php ob_start(); ?>

<div class="container admin-container">
    <div class="admin-header">
        <h1>👥 Lietotāju pārvaldība</h1>
        <p>Pārvaldīt lietotājus un viņu lomas</p>
    </div>

    <?php if (!empty($users)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vārds</th>
                        <th>E-pasts</th>
                        <th>Lomas</th>
                        <th>Produkti</th>
                        <th>Pasūtījumi</th>
                        <th>Statuss</th>
                        <th>Reģistrēts</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td>
                                <strong><?= e($user['full_name']) ?></strong>
                                <?php if ($user['phone']): ?>
                                    <br><small><?= e($user['phone']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= e($user['email']) ?></td>
                            <td>
                                <div class="role-badges">
                                    <?php if (!empty($user['roles'])): ?>
                                        <?php foreach (explode(', ', $user['roles']) as $role): ?>
                                            <span class="role-badge role-<?= e($role) ?>">
                                                <?= e($role) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="role-badge">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center"><?= $user['products_count'] ?></td>
                            <td class="text-center">
                                <?php if ($user['orders_as_seller'] > 0 || $user['orders_as_buyer'] > 0): ?>
                                    🛒 <?= $user['orders_as_buyer'] ?> | 
                                    🏪 <?= $user['orders_as_seller'] ?>
                                <?php else: ?>
                                    0
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span class="status-badge status-active">Aktīvs</span>
                                <?php else: ?>
                                    <span class="status-badge status-inactive">Neaktīvs</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= date('d.m.Y', strtotime($user['created_at'])) ?></small></td>
                            <td>
                                <button onclick="openRoleModal(<?= $user['id'] ?>, '<?= addslashes($user['full_name']) ?>', '<?= addslashes($user['roles'] ?? '') ?>')" 
                                        class="btn btn-sm btn-primary">
                                    ⚙️ Lomas
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-data">Nav lietotāju</div>
    <?php endif; ?>
</div>

<!-- Role Assignment Modal -->
<div id="roleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Piešķirt lomas</h2>
            <span class="modal-close" onclick="closeRoleModal()">&times;</span>
        </div>
        <form id="roleForm" method="POST" action="">
            <?= csrf_field() ?>
            <div class="modal-body">
                <p>Lietotājs: <strong id="userName"></strong></p>
                
                <div class="role-checkboxes">
                    <label class="checkbox-label">
                        <input type="checkbox" name="roles[]" value="admin">
                        <span>👨‍💼 Administrators</span>
                        <small>Pilnīga piekļuve visām funkcijām</small>
                    </label>
                    
                    <label class="checkbox-label">
                        <input type="checkbox" name="roles[]" value="seller">
                        <span>🏪 Pārdevējs/Ražotājs</span>
                        <small>Var pievienot produktus un pārdot</small>
                    </label>
                    
                    <label class="checkbox-label">
                        <input type="checkbox" name="roles[]" value="buyer">
                        <span>🛒 Pircējs</span>
                        <small>Var pirkt produktus</small>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeRoleModal()" class="btn btn-secondary">Atcelt</button>
                <button type="submit" class="btn btn-primary">Saglabāt lomas</button>
            </div>
        </form>
    </div>
</div>

<style>
.table-responsive {
    overflow-x: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.admin-table th,
.admin-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.admin-table tbody tr:hover {
    background: #f9f9f9;
}

.text-center {
    text-align: center !important;
}

.role-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.role-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.role-admin {
    background: #dc3545;
    color: white;
}

.role-seller {
    background: #28a745;
    color: white;
}

.role-buyer {
    background: #007bff;
    color: white;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    align-items: center;
    justify-content: center;
}

.modal.show {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e0e0e0;
}

.modal-header h2 {
    margin: 0;
}

.modal-close {
    font-size: 2rem;
    cursor: pointer;
    color: #999;
    line-height: 1;
}

.modal-close:hover {
    color: #333;
}

.modal-body {
    padding: 1.5rem;
}

.role-checkboxes {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.checkbox-label {
    display: flex;
    flex-direction: column;
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.checkbox-label:hover {
    border-color: #667eea;
    background: #f9f9ff;
}

.checkbox-label input {
    margin-right: 0.5rem;
}

.checkbox-label span {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.checkbox-label small {
    color: #666;
    margin-left: 1.5rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding: 1.5rem;
    border-top: 1px solid #e0e0e0;
}
</style>

<script>
function openRoleModal(userId, userName, currentRoles) {
    const modal = document.getElementById('roleModal');
    const form = document.getElementById('roleForm');
    const userNameSpan = document.getElementById('userName');
    
    // Set form action
    form.action = '/admin/users/' + userId + '/roles';
    
    // Set user name
    userNameSpan.textContent = userName;
    
    // Clear all checkboxes
    form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    
    // Check current roles
    const roles = currentRoles.split(', ');
    roles.forEach(role => {
        const checkbox = form.querySelector('input[value="' + role.trim() + '"]');
        if (checkbox) checkbox.checked = true;
    });
    
    // Show modal
    modal.classList.add('show');
}

function closeRoleModal() {
    document.getElementById('roleModal').classList.remove('show');
}

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('roleModal');
    if (event.target == modal) {
        closeRoleModal();
    }
}
</script>

<?php
$content = ob_get_clean();
$title = 'Lietotāju pārvaldība - ' . lang('admin.admin_panel') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>

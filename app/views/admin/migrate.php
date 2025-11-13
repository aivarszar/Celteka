<?php ob_start(); ?>

<div class="container admin-container">
    <div class="admin-header">
        <h1>🔄 Lietotāju lomu migrācija</h1>
        <p>Migrē esošo lietotāju lomas uz jauno vairāku lomu sistēmu</p>
    </div>

    <div class="migration-info">
        <h2>Par migrāciju</h2>
        <p>Šī migrācija pārnes lietotāju lomas no vecās <code>role_id</code> kolonnas uz jauno <code>user_role_assignments</code> tabulu, kas atbalsta vairākas lomas vienam lietotājam.</p>

        <h3>Ko migrācija dara:</h3>
        <ul>
            <li>Iegūst visus lietotājus ar <code>role_id</code> vērtību</li>
            <li>Konvertē <code>role_id</code> uz atbilstošo lomu (1=buyer, 2=seller, 3=admin)</li>
            <li>Pievieno ierakstu <code>user_role_assignments</code> tabulā</li>
            <li>Pārliecinās, ka pirmajam lietotājam ir admin loma</li>
            <li>Izlaiž lietotājus, kuriem loma jau ir piešķirta</li>
        </ul>

        <div class="migration-warning">
            <strong>⚠️ Svarīgi:</strong>
            <ul>
                <li>Šo migrāciju var palaist vairākas reizes - tā izlaidīs jau migrētos lietotājus</li>
                <li>Migrācija NEPĀRRAKSTA esošās lomas, tikai pievieno trūkstošās</li>
                <li>Migrācija neizdzēš <code>role_id</code> kolonnu (saglabā vecās vērtības drošībai)</li>
            </ul>
        </div>
    </div>

    <?php
    // Pārbaudīt pašreizējo stāvokli
    try {
        $usersWithRoleId = db()->fetchColumn("SELECT COUNT(*) FROM users WHERE role_id IS NOT NULL");
        $assignedRoles = db()->fetchColumn("SELECT COUNT(*) FROM user_role_assignments");
        $usersNeedingMigration = db()->fetchAll("
            SELECT u.id, u.full_name, u.email, u.role_id,
                   (SELECT COUNT(*) FROM user_role_assignments WHERE user_id = u.id) as assigned_count
            FROM users u
            WHERE u.role_id IS NOT NULL
            HAVING assigned_count = 0
        ");
    ?>

    <div class="migration-status">
        <h2>Pašreizējais stāvoklis</h2>
        <div class="status-grid">
            <div class="status-card">
                <div class="status-value"><?= $usersWithRoleId ?></div>
                <div class="status-label">Lietotāji ar role_id</div>
            </div>
            <div class="status-card">
                <div class="status-value"><?= $assignedRoles ?></div>
                <div class="status-label">Piešķirtās lomas (user_role_assignments)</div>
            </div>
            <div class="status-card <?= count($usersNeedingMigration) > 0 ? 'status-warning' : 'status-success' ?>">
                <div class="status-value"><?= count($usersNeedingMigration) ?></div>
                <div class="status-label">Lietotāji bez piešķirtām lomām</div>
            </div>
        </div>

        <?php if (!empty($usersNeedingMigration)): ?>
            <div class="users-list">
                <h3>Lietotāji, kuriem nepieciešama migrācija:</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vārds</th>
                            <th>E-pasts</th>
                            <th>role_id</th>
                            <th>Loma</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $roleMapping = [1 => 'buyer', 2 => 'seller', 3 => 'admin'];
                        foreach ($usersNeedingMigration as $user):
                        ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= e($user['full_name']) ?></td>
                                <td><?= e($user['email']) ?></td>
                                <td><?= $user['role_id'] ?></td>
                                <td>
                                    <span class="role-badge">
                                        <?= $roleMapping[$user['role_id']] ?? 'unknown' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <form method="POST" action="/admin/migrate/run" class="migration-form">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary btn-large">
                    🚀 Palaist migrāciju
                </button>
            </form>
        <?php else: ?>
            <div class="migration-complete">
                <p>✅ Visi lietotāji jau ir migrēti uz jauno lomu sistēmu!</p>
            </div>
        <?php endif; ?>
    </div>

    <?php
    } catch (Exception $e) {
        echo '<div class="error-message">Kļūda: ' . e($e->getMessage()) . '</div>';
    }
    ?>

    <?php
    // Parādīt migrācijas rezultātus, ja tādi ir
    $results = Session::get('migration_results');
    if ($results):
        Session::remove('migration_results');
    ?>
        <div class="migration-results">
            <h2>Migrācijas rezultāti</h2>

            <?php if (!empty($results['migrated'])): ?>
                <div class="results-section success">
                    <h3>✅ Veiksmīgi migrēti (<?= count($results['migrated']) ?>)</h3>
                    <ul>
                        <?php foreach ($results['migrated'] as $msg): ?>
                            <li><?= e($msg) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($results['skipped'])): ?>
                <div class="results-section info">
                    <h3>⊙ Izlaisti (<?= count($results['skipped']) ?>)</h3>
                    <ul>
                        <?php foreach ($results['skipped'] as $msg): ?>
                            <li><?= e($msg) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($results['errors'])): ?>
                <div class="results-section error">
                    <h3>✗ Kļūdas (<?= count($results['errors']) ?>)</h3>
                    <ul>
                        <?php foreach ($results['errors'] as $msg): ?>
                            <li><?= e($msg) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.migration-info {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.migration-info h2 {
    margin-top: 0;
    color: #333;
}

.migration-info h3 {
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    color: #555;
}

.migration-info code {
    background: #f5f5f5;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-family: monospace;
}

.migration-warning {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 1rem;
    margin-top: 1rem;
    border-radius: 4px;
}

.migration-status {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin: 1.5rem 0;
}

.status-card {
    background: #f9f9f9;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    border: 2px solid #e0e0e0;
}

.status-card.status-success {
    background: #d4edda;
    border-color: #28a745;
}

.status-card.status-warning {
    background: #fff3cd;
    border-color: #ffc107;
}

.status-value {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
}

.status-label {
    font-size: 0.875rem;
    color: #666;
    margin-top: 0.5rem;
}

.users-list {
    margin: 2rem 0;
}

.users-list table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.users-list th,
.users-list td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.users-list th {
    background: #f5f5f5;
    font-weight: 600;
}

.role-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    background: #667eea;
    color: white;
}

.migration-form {
    text-align: center;
    margin: 2rem 0;
}

.btn-large {
    font-size: 1.25rem;
    padding: 1rem 2rem;
}

.migration-complete {
    background: #d4edda;
    border: 2px solid #28a745;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    margin: 2rem 0;
}

.migration-complete p {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #155724;
}

.migration-results {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.results-section {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border-radius: 8px;
}

.results-section.success {
    background: #d4edda;
    border-left: 4px solid #28a745;
}

.results-section.info {
    background: #e7f3ff;
    border-left: 4px solid #2196f3;
}

.results-section.error {
    background: #f8d7da;
    border-left: 4px solid #dc3545;
}

.results-section h3 {
    margin-top: 0;
}

.results-section ul {
    margin: 0.5rem 0 0 0;
    padding-left: 1.5rem;
}

.error-message {
    background: #f8d7da;
    border: 2px solid #dc3545;
    padding: 1rem;
    border-radius: 8px;
    color: #721c24;
}
</style>

<?php
$content = ob_get_clean();
$title = 'Lomu migrācija - ' . lang('admin.admin_panel') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>

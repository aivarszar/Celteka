<?php ob_start(); ?>

<div class="container admin-container">
    <div class="admin-header">
        <h1>📧 Saglabātie e-pasti</h1>
        <p>E-pasti, kas tika saglabāti failos (development/fallback režīms)</p>
    </div>

    <?php if (!empty($emails)): ?>
        <div class="emails-list">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Datums</th>
                        <th>Faila nosaukums</th>
                        <th>Izmērs</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emails as $email): ?>
                        <tr>
                            <td><?= e($email['date']) ?></td>
                            <td>
                                <code style="font-size: 0.875rem;"><?= e($email['filename']) ?></code>
                            </td>
                            <td><?= number_format($email['size'] / 1024, 2) ?> KB</td>
                            <td>
                                <a href="/admin/emails/view/<?= e($email['filename']) ?>" class="btn btn-sm btn-primary">
                                    👁️ Skatīt
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="info-box">
            <h3>ℹ️ Par saglabātajiem e-pastiem</h3>
            <ul>
                <li>E-pasti tiek saglabāti <code>storage/emails/</code> direktorijā</li>
                <li>Faili tiek izveidoti .eml formātā (var atvērt ar e-pasta klientu)</li>
                <li>Šī funkcija darbojas kā fallback, ja PHP mail() nedarbojas</li>
                <li>Production režīmā ieteicams konfigurēt SMTP serveri</li>
            </ul>
        </div>
    <?php else: ?>
        <div class="no-data">
            <p>📭 Nav saglabātu e-pastu</p>
            <p style="margin-top: 1rem;">E-pasti tiks saglabāti failos, ja:</p>
            <ul style="text-align: left; max-width: 400px; margin: 1rem auto;">
                <li>PHP mail() funkcija nav pieejama</li>
                <li>E-pasta sūtīšana neveicas</li>
                <li>Konfigurācijā iestatīts file driver</li>
            </ul>
        </div>
    <?php endif; ?>
</div>

<style>
.emails-list {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    overflow: hidden;
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

.info-box {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
    padding: 1.5rem;
    border-radius: 8px;
}

.info-box h3 {
    margin-top: 0;
    color: #1976d2;
}

.info-box ul {
    margin: 0;
    padding-left: 1.5rem;
}

.info-box li {
    margin-bottom: 0.5rem;
}

.info-box code {
    background: white;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-size: 0.875rem;
}

.no-data {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.no-data p {
    font-size: 1.125rem;
    color: #666;
}
</style>

<?php
$content = ob_get_clean();
$title = 'E-pasti - Admin panelis - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>

<?php ob_start(); ?>

<div class="container import-container">
    <div class="page-header">
        <h1>📥 Importēt produktu no ss.lv</h1>
        <p>Viegli importējiet produktus no ss.lv sludinājumiem</p>
    </div>

    <div class="import-content">
        <div class="import-main">
            <div class="card">
                <div class="card-header">
                    <h2>Sludinājuma importēšana</h2>
                </div>
                <div class="card-body">
                    <form action="/import/from-url" method="post">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="url">SS.LV sludinājuma URL:</label>
                            <input type="url"
                                   id="url"
                                   name="url"
                                   class="form-control"
                                   placeholder="https://www.ss.lv/msg/..."
                                   required
                                   pattern="https?://(www\.)?ss\.(lv|com)/.*">
                            <small class="form-text">Ievadiet pilnu ss.lv sludinājuma adresi</small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg">
                                🔍 Importēt sludinājumu
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Instrukcijas -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3>📋 Kā importēt?</h3>
                </div>
                <div class="card-body">
                    <ol class="instructions-list">
                        <li>
                            <strong>Atveriet ss.lv</strong> un atrodiet sludinājumu, kuru vēlaties importēt
                        </li>
                        <li>
                            <strong>Nokopējiet sludinājuma URL</strong> no pārlūka adreses joslas<br>
                            <span class="example">Piemērs: https://www.ss.lv/msg/lv/electronics/computers/12345.html</span>
                        </li>
                        <li>
                            <strong>Ielīmējiet URL</strong> augstāk esošajā laukā
                        </li>
                        <li>
                            <strong>Nospiediet "Importēt"</strong> - sistēma automātiski nolasīs:
                            <ul>
                                <li>Produkta nosaukumu</li>
                                <li>Cenu</li>
                                <li>Aprakstu</li>
                                <li>Attēlus</li>
                            </ul>
                        </li>
                        <li>
                            <strong>Pārbaudiet un rediģējiet</strong> importētos datus priekšskatījumā
                        </li>
                        <li>
                            <strong>Apstipriniet</strong> un produkts tiks pievienots jūsu katalogam
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Sidebar ar piemēriem -->
        <div class="import-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>ℹ️ Svarīgi</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p><strong>Atbalstītie avoti:</strong></p>
                        <ul>
                            <li>✅ ss.lv sludinājumi</li>
                            <li>✅ ss.com sludinājumi</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <p><strong>⚠️ Piezīmes:</strong></p>
                        <ul>
                            <li>Importējiet tikai savus sludinājumus vai ar atļauju</li>
                            <li>Pārbaudiet visus datus pirms publicēšanas</li>
                            <li>Attēli tiks saglabāti kā URL saites</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3>📊 Statistika</h3>
                </div>
                <div class="card-body">
                    <?php
                    $userId = Session::getUserId();
                    $importedCount = db()->fetchColumn(
                        "SELECT COUNT(*) FROM product_meta WHERE user_id = :user_id AND meta_key = 'source_url'",
                        ['user_id' => $userId]
                    ) ?: 0;
                    ?>
                    <div class="stat">
                        <div class="stat-number"><?= $importedCount ?></div>
                        <div class="stat-label">Importēti produkti</div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3>🔗 Ātrās saites</h3>
                </div>
                <div class="card-body">
                    <a href="/seller/products" class="btn btn-outline btn-block">Mani produkti</a>
                    <a href="/seller/products/create" class="btn btn-outline btn-block mt-2">Manuāli pievienot</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.import-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.page-header {
    margin-bottom: 2rem;
    text-align: center;
}

.page-header h1 {
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #666;
    font-size: 1.125rem;
}

.import-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
}

.import-main {
    min-width: 0;
}

.card-header h2, .card-header h3 {
    margin: 0;
    font-size: 1.25rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
}

.form-control:focus {
    outline: none;
    border-color: #2196F3;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

.form-text {
    display: block;
    margin-top: 0.5rem;
    color: #666;
    font-size: 0.875rem;
}

.form-actions {
    margin-top: 2rem;
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.125rem;
}

.instructions-list {
    line-height: 1.8;
    padding-left: 1.5rem;
}

.instructions-list li {
    margin-bottom: 1rem;
}

.instructions-list ul {
    margin-top: 0.5rem;
}

.example {
    display: inline-block;
    margin-top: 0.5rem;
    padding: 0.5rem;
    background: #f5f5f5;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.875rem;
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert-info {
    background: #e3f2fd;
    border: 1px solid #90caf9;
    color: #0d47a1;
}

.alert-warning {
    background: #fff3cd;
    border: 1px solid #ffc107;
    color: #856404;
}

.alert p {
    margin: 0 0 0.5rem 0;
}

.alert ul {
    margin: 0.5rem 0 0 0;
    padding-left: 1.5rem;
}

.alert li {
    margin: 0.25rem 0;
}

.stat {
    text-align: center;
    padding: 1.5rem;
    background: #f5f5f5;
    border-radius: 8px;
}

.stat-number {
    font-size: 3rem;
    font-weight: bold;
    color: #2196F3;
}

.stat-label {
    color: #666;
    margin-top: 0.5rem;
}

.btn-block {
    width: 100%;
    display: block;
    text-align: center;
}

.import-sidebar {
    position: sticky;
    top: 2rem;
    height: fit-content;
}

@media (max-width: 1200px) {
    .import-content {
        grid-template-columns: 1fr;
    }

    .import-sidebar {
        position: static;
    }
}
</style>

<?php
$content = ob_get_clean();
$title = 'Importēt produktu - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>

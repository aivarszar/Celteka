<?php ob_start(); ?>

<div class="container admin-container">
    <div class="admin-header">
        <h1>📧 E-pasta satura skatīšana</h1>
        <p><?= e($filename) ?></p>
        <a href="/admin/emails" class="btn btn-secondary">← Atpakaļ uz sarakstu</a>
    </div>

    <div class="email-viewer">
        <!-- Headers -->
        <div class="email-headers">
            <h2>Headers</h2>
            <table>
                <?php foreach ($headers as $key => $value): ?>
                    <tr>
                        <td class="header-key"><?= e($key) ?>:</td>
                        <td class="header-value"><?= e($value) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- Body -->
        <div class="email-body">
            <h2>Saturs</h2>
            <div class="body-preview">
                <?php
                // Pārbaudīt, vai ir HTML
                if (strpos($body, '<html>') !== false || strpos($body, '<!DOCTYPE') !== false) {
                    // HTML saturs - renderēt iframe
                    echo '<iframe srcdoc="' . htmlspecialchars($body) . '" frameborder="0"></iframe>';
                } else {
                    // Plain text
                    echo '<pre>' . e($body) . '</pre>';
                }
                ?>
            </div>
        </div>

        <!-- Raw Content -->
        <div class="email-raw">
            <h2>Raw Content</h2>
            <details>
                <summary>Rādīt neapstrādātu saturu</summary>
                <pre class="raw-content"><?= e(file_get_contents($filepath)) ?></pre>
            </details>
        </div>
    </div>
</div>

<style>
.email-viewer {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.email-headers {
    border-bottom: 2px solid #e0e0e0;
    padding: 2rem;
}

.email-headers h2 {
    margin-top: 0;
    margin-bottom: 1rem;
    color: #333;
}

.email-headers table {
    width: 100%;
    border-collapse: collapse;
}

.email-headers tr {
    border-bottom: 1px solid #f0f0f0;
}

.header-key {
    padding: 0.75rem;
    font-weight: 600;
    color: #666;
    width: 150px;
    vertical-align: top;
}

.header-value {
    padding: 0.75rem;
    word-break: break-word;
}

.email-body {
    padding: 2rem;
    border-bottom: 2px solid #e0e0e0;
}

.email-body h2 {
    margin-top: 0;
    margin-bottom: 1rem;
    color: #333;
}

.body-preview {
    background: #f9f9f9;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    min-height: 300px;
}

.body-preview iframe {
    width: 100%;
    min-height: 500px;
    border: none;
    background: white;
}

.body-preview pre {
    margin: 0;
    white-space: pre-wrap;
    word-wrap: break-word;
    font-family: monospace;
    font-size: 0.875rem;
}

.email-raw {
    padding: 2rem;
}

.email-raw h2 {
    margin-top: 0;
    margin-bottom: 1rem;
    color: #333;
}

.email-raw details {
    background: #f9f9f9;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

.email-raw summary {
    cursor: pointer;
    font-weight: 600;
    color: #667eea;
    padding: 0.5rem;
}

.email-raw summary:hover {
    color: #764ba2;
}

.raw-content {
    margin: 1rem 0 0 0;
    padding: 1rem;
    background: #2d2d2d;
    color: #f8f8f2;
    border-radius: 4px;
    overflow-x: auto;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    line-height: 1.5;
}
</style>

<?php
$content = ob_get_clean();
$title = 'E-pasta skatīšana - Admin panelis - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>

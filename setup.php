<?php
/**
 * STANDALONE Instalācijas Vednis
 * Vienkāršs fails bez atkarībām - darbojas bez .htaccess
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konstantes
define('MIN_PHP_VERSION', '7.4.0');
define('REQUIRED_EXTENSIONS', ['pdo', 'pdo_mysql', 'mysqli', 'json', 'mbstring', 'curl']);
define('BASE_DIR', __DIR__);

// Palīgfunkcijas
function checkPhpVersion() {
    return version_compare(PHP_VERSION, MIN_PHP_VERSION, '>=');
}

function checkExtensions() {
    $missing = [];
    foreach (REQUIRED_EXTENSIONS as $ext) {
        if (!extension_loaded($ext)) {
            $missing[] = $ext;
        }
    }
    return $missing;
}

function checkWritePermissions() {
    $dirs = [BASE_DIR . '/app/config', BASE_DIR . '/assets/images'];
    $notWritable = [];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (!is_writable($dir)) {
            $notWritable[] = $dir;
        }
    }
    return $notWritable;
}

function testDatabaseConnection($host, $dbname, $username, $password) {
    try {
        $dsn = "mysql:host={$host};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $stmt = $pdo->query("SHOW DATABASES LIKE '{$dbname}'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("CREATE DATABASE `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }

        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function installDatabase($host, $dbname, $username, $password, $cleanInstall = true) {
    try {
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // Ja tīra instalācija, iztīrīt esošās tabulas
        if ($cleanInstall) {
            $tables = ['reviews', 'order_items', 'orders', 'product_images', 'product_meta', 'products',
                       'categories', 'locations', 'user_meta', 'users', 'user_roles', 'delivery_methods', 'settings'];

            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            foreach ($tables as $table) {
                $pdo->exec("DROP TABLE IF EXISTS {$table}");
            }
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        }

        // Izpildīt schema.sql
        if (file_exists(BASE_DIR . '/database/schema.sql')) {
            $schema = file_get_contents(BASE_DIR . '/database/schema.sql');
            $statements = explode(';', $schema);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
        }

        // Izpildīt locations_lv.sql
        if (file_exists(BASE_DIR . '/database/locations_lv.sql')) {
            $locations = file_get_contents(BASE_DIR . '/database/locations_lv.sql');
            $statements = explode(';', $locations);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
        }

        // Izpildīt categories.sql
        if (file_exists(BASE_DIR . '/database/categories.sql')) {
            $categories = file_get_contents(BASE_DIR . '/database/categories.sql');
            $statements = explode(';', $categories);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
        }

        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function createConfigFile($host, $dbname, $username, $password) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $hostName = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $appUrl = $protocol . '://' . $hostName;

    $config = "<?php\n";
    $config .= "/**\n";
    $config .= " * Marketplace Platform - Konfigurācijas Fails\n";
    $config .= " * Automātiski ģenerēts instalācijas laikā\n";
    $config .= " */\n\n";
    $config .= "return [\n";
    $config .= "    // Datubāzes iestatījumi\n";
    $config .= "    'database' => [\n";
    $config .= "        'host' => '{$host}',\n";
    $config .= "        'dbname' => '{$dbname}',\n";
    $config .= "        'username' => '{$username}',\n";
    $config .= "        'password' => '{$password}',\n";
    $config .= "        'charset' => 'utf8mb4',\n";
    $config .= "    ],\n\n";
    $config .= "    // Aplikācijas iestatījumi\n";
    $config .= "    'app' => [\n";
    $config .= "        'name' => 'Vietējais Tirgus',\n";
    $config .= "        'url' => '{$appUrl}',\n";
    $config .= "        'timezone' => 'Europe/Riga',\n";
    $config .= "        'locale' => 'lv',\n";
    $config .= "        'debug' => false,\n";
    $config .= "    ],\n\n";
    $config .= "    // Sesijas iestatījumi\n";
    $config .= "    'session' => [\n";
    $config .= "        'name' => 'marketplace_session',\n";
    $config .= "        'lifetime' => 86400,\n";
    $config .= "        'secure' => false,\n";
    $config .= "        'httponly' => true,\n";
    $config .= "    ],\n\n";
    $config .= "    // Augšupielāžu iestatījumi\n";
    $config .= "    'upload' => [\n";
    $config .= "        'max_size' => 5242880,\n";
    $config .= "        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],\n";
    $config .= "        'path' => '/assets/images/uploads/',\n";
    $config .= "    ],\n";
    $config .= "];\n";

    $configFile = BASE_DIR . '/app/config/config.php';
    return file_put_contents($configFile, $config);
}

// Apstrādāt formu
$step = $_GET['step'] ?? 'check';
$errors = [];
$warnings = [];
$success = [];

// Pārbaudīt vai jau instalēts
$configFile = BASE_DIR . '/app/config/config.php';
$alreadyInstalled = file_exists($configFile);

if ($step === 'check' && !$alreadyInstalled) {
    if (!checkPhpVersion()) {
        $errors[] = "PHP versija ir pārāk veca. Nepieciešama " . MIN_PHP_VERSION . "+. Pašreizējā: " . PHP_VERSION;
    } else {
        $success[] = "PHP versija: " . PHP_VERSION . " ✓";
    }

    $missingExt = checkExtensions();
    if (!empty($missingExt)) {
        $errors[] = "Trūkst PHP paplašinājumi: " . implode(', ', $missingExt);
    } else {
        $success[] = "Visi PHP paplašinājumi instalēti ✓";
    }

    $notWritable = checkWritePermissions();
    if (!empty($notWritable)) {
        $warnings[] = "Nav rakstīšanas tiesību: " . implode(', ', $notWritable);
    } else {
        $success[] = "Rakstīšanas tiesības OK ✓";
    }

} elseif ($step === 'database' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = trim($_POST['db_host'] ?? 'localhost');
    $dbname = trim($_POST['db_name'] ?? '');
    $username = trim($_POST['db_user'] ?? '');
    $password = $_POST['db_pass'] ?? '';

    if (empty($dbname) || empty($username)) {
        $errors[] = "Aizpildiet visus obligātos laukus!";
    } else {
        $testResult = testDatabaseConnection($host, $dbname, $username, $password);

        if ($testResult['success']) {
            $installResult = installDatabase($host, $dbname, $username, $password);

            if ($installResult['success']) {
                if (createConfigFile($host, $dbname, $username, $password)) {
                    $_SESSION['install_success'] = true;
                    header('Location: setup.php?step=complete');
                    exit;
                } else {
                    $errors[] = "Neizdevās izveidot config failu!";
                }
            } else {
                $errors[] = "Datubāzes kļūda: " . $installResult['error'];
            }
        } else {
            $errors[] = "Savienojuma kļūda: " . $testResult['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalācija - Vietējais Tirgus</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            max-width: 700px;
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 28px; margin-bottom: 10px; }
        .content { padding: 40px; }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid;
        }
        .alert-error { background: #fee; border-color: #c33; color: #c33; }
        .alert-warning { background: #fffbea; border-color: #f5a623; color: #8b6914; }
        .alert-success { background: #eeffee; border-color: #4caf50; color: #2e7d32; }
        .alert-info { background: #e3f2fd; border-color: #2196f3; color: #0d47a1; }
        .alert ul { margin-left: 20px; margin-top: 10px; list-style: none; }
        .alert li { margin-bottom: 5px; }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-2px); }
        .text-center { text-align: center; }
        .progress {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        .progress::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #ddd;
            z-index: 0;
        }
        .progress-step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .progress-step .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 2px solid #ddd;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .progress-step.active .circle {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }
        .progress-step.completed .circle {
            background: #4caf50;
            border-color: #4caf50;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏪 Vietējais Tirgus</h1>
            <p>Standalone Instalācijas Vednis</p>
        </div>

        <div class="content">
            <?php if ($alreadyInstalled && $step !== 'complete'): ?>
                <div class="alert alert-warning">
                    ⚠️ Sistēma jau ir instalēta!
                </div>
                <div class="text-center">
                    <a href="public/index.php" class="btn">Doties uz platformu</a>
                </div>
            <?php else: ?>
                <div class="progress">
                    <div class="progress-step <?= $step === 'check' ? 'active' : 'completed' ?>">
                        <div class="circle">1</div>
                        <p>Pārbaude</p>
                    </div>
                    <div class="progress-step <?= $step === 'database' ? 'active' : ($step === 'complete' ? 'completed' : '') ?>">
                        <div class="circle">2</div>
                        <p>Datubāze</p>
                    </div>
                    <div class="progress-step <?= $step === 'complete' ? 'active' : '' ?>">
                        <div class="circle">3</div>
                        <p>Pabeigts</p>
                    </div>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <strong>❌ Kļūdas:</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($warnings)): ?>
                    <div class="alert alert-warning">
                        <strong>⚠️ Brīdinājumi:</strong>
                        <ul>
                            <?php foreach ($warnings as $warning): ?>
                                <li><?= htmlspecialchars($warning) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success) && $step === 'check'): ?>
                    <div class="alert alert-success">
                        <strong>✓ Servera pārbaude veiksmīga:</strong>
                        <ul>
                            <?php foreach ($success as $msg): ?>
                                <li><?= htmlspecialchars($msg) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($step === 'check'): ?>
                    <div class="text-center">
                        <a href="setup.php?step=database" class="btn" <?= !empty($errors) ? 'style="display:none"' : '' ?>>
                            Turpināt →
                        </a>
                        <?php if (!empty($errors)): ?>
                            <p style="color: #c33; margin-top: 20px;">
                                Novērsiet kļūdas pirms turpināt.
                            </p>
                        <?php endif; ?>
                    </div>

                <?php elseif ($step === 'database'): ?>
                    <form method="post" action="setup.php?step=database">
                        <div class="form-group">
                            <label>Datubāzes serveris *</label>
                            <input type="text" name="db_host" value="localhost" required>
                        </div>

                        <div class="form-group">
                            <label>Datubāzes nosaukums *</label>
                            <input type="text" name="db_name" placeholder="marketplace" required>
                        </div>

                        <div class="form-group">
                            <label>Lietotājvārds *</label>
                            <input type="text" name="db_user" placeholder="root" required>
                        </div>

                        <div class="form-group">
                            <label>Parole</label>
                            <input type="password" name="db_pass">
                        </div>

                        <div class="alert alert-info">
                            ℹ️ Ja datubāze neeksistē, tā tiks izveidota automātiski.
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn">Instalēt</button>
                        </div>
                    </form>

                <?php elseif ($step === 'complete'): ?>
                    <div class="alert alert-success">
                        <strong>🎉 Instalācija pabeigta!</strong>
                        <p style="margin-top: 10px;">Platforma ir gatava lietošanai.</p>
                    </div>

                    <div class="alert alert-warning" style="margin-top: 20px;">
                        <strong>⚠️ SVARĪGI - Drošības pasākumi:</strong>
                        <ul style="margin-left: 20px; margin-top: 10px;">
                            <li>Izdzēsiet setup.php failu no servera!</li>
                            <li>Izdzēsiet test-root.php, install/test.php, public/test.php</li>
                            <li>Iestatiet debug => false production vidē</li>
                        </ul>
                    </div>

                    <div class="text-center" style="margin-top: 20px;">
                        <a href="public/index.php" class="btn">Doties uz platformu →</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

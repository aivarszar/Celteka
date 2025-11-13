<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? lang('app.name') ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <?= $extraCss ?? '' ?>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <!-- Logo / Home Icon -->
                <div class="logo">
                    <a href="/" title="<?= lang('nav.home') ?>">
                        <span style="font-size: 1.5rem;">🏠</span>
                        <span class="logo-text"><?= lang('app.name') ?></span>
                    </a>
                </div>

                <?php
                // Noteikt aktīvo lapu
                $currentPath = $_SERVER['REQUEST_URI'] ?? '/';
                $currentPath = strtok($currentPath, '?'); // Noņemt query string
                $currentPath = rtrim($currentPath, '/'); // Noņemt trailing slash

                function isActive($path) {
                    global $currentPath;
                    // Eksaktais match sākumlapai
                    if ($path === '/' && $currentPath === '') {
                        return 'active';
                    }
                    // Ignore home page for other paths
                    if ($path === '/') {
                        return '';
                    }
                    // Remove trailing slash from path
                    $path = rtrim($path, '/');
                    // Check if current path starts with the given path
                    return (strpos($currentPath, $path) === 0) ? 'active' : '';
                }
                ?>

                <!-- Main Navigation -->
                <ul class="nav-menu" id="navMenu">
                    <li><a href="/products" class="<?= isActive('/products') ?>"><?= lang('nav.products') ?></a></li>

                    <?php if (Session::isLoggedIn()): ?>
                        <?php if (AuthHelper::isSeller()): ?>
                            <li><a href="/seller/products" class="<?= isActive('/seller/products') ?>"><?= lang('nav.my_products') ?></a></li>
                            <li><a href="/seller/orders" class="<?= isActive('/seller/orders') ?>"><?= lang('nav.my_orders') ?></a></li>
                        <?php else: ?>
                            <li><a href="/orders" class="<?= isActive('/orders') ?>"><?= lang('nav.my_orders') ?></a></li>
                        <?php endif; ?>
                        <li><a href="/profile" class="<?= isActive('/profile') ?>"><?= lang('nav.profile') ?></a></li>
                        <?php if (AuthHelper::isAdmin()): ?>
                            <li><a href="/admin" class="<?= isActive('/admin') ?>"><?= lang('nav.dashboard') ?></a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a href="/login" class="<?= isActive('/login') ?>"><?= lang('nav.login') ?></a></li>
                        <li><a href="/register" class="btn btn-primary btn-sm <?= isActive('/register') ?>"><?= lang('nav.register') ?></a></li>
                    <?php endif; ?>
                </ul>

                <!-- Right Side: Language Selector & Logout -->
                <div class="navbar-right">
                    <!-- Language Selector Dropdown -->
                    <div class="language-dropdown">
                        <?php
                        $currentLocale = app()->getLang()->getLocale();
                        $languages = [
                            'lv' => 'Latviešu',
                            'en' => 'English',
                            'ru' => 'Русский',
                            'lt' => 'Lietuvių',
                            'ee' => 'Eesti'
                        ];
                        $languageFlags = [
                            'lv' => '🇱🇻',
                            'en' => '🇬🇧',
                            'ru' => '🇷🇺',
                            'lt' => '🇱🇹',
                            'ee' => '🇪🇪'
                        ];
                        ?>
                        <button class="lang-dropdown-btn" onclick="toggleLangDropdown()">
                            <span><?= $languageFlags[$currentLocale] ?? '🌐' ?></span>
                            <span><?= strtoupper($currentLocale) ?></span>
                            <span>▼</span>
                        </button>
                        <div class="lang-dropdown-menu" id="langDropdown">
                            <?php foreach ($languages as $code => $name): ?>
                                <a href="/lang/<?= $code ?>" class="lang-dropdown-item <?= $code === $currentLocale ? 'active' : '' ?>">
                                    <span><?= $languageFlags[$code] ?></span>
                                    <span><?= $name ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Logout Icon -->
                    <?php if (Session::isLoggedIn()): ?>
                        <a href="/logout" class="logout-btn" title="<?= lang('nav.logout') ?>">
                            <span style="font-size: 1.3rem;">🚪</span>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="mobile-toggle" onclick="toggleMenu()">☰</div>
            </nav>
        </div>
    </header>

    <style>
    .navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 0;
    }

    .logo a {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        font-size: 1.2rem;
        font-weight: bold;
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .language-dropdown {
        position: relative;
    }

    .lang-dropdown-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--white);
        border: 1px solid var(--gray);
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .lang-dropdown-btn:hover {
        background: var(--light-gray);
        border-color: var(--primary);
    }

    .lang-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 0.5rem;
        background: var(--white);
        border: 1px solid var(--gray);
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        min-width: 180px;
        z-index: 1000;
    }

    .lang-dropdown-menu.show {
        display: block;
    }

    .lang-dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        text-decoration: none;
        color: var(--text);
        transition: background 0.2s;
    }

    .lang-dropdown-item:hover {
        background: var(--light-gray);
    }

    .lang-dropdown-item.active {
        background: var(--primary);
        color: var(--white);
        font-weight: 600;
    }

    .logout-btn {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        text-decoration: none;
        transition: transform 0.2s;
    }

    .logout-btn:hover {
        transform: scale(1.1);
    }

    @media (max-width: 768px) {
        .logo-text {
            display: none;
        }
        .navbar-right {
            gap: 0.5rem;
        }
    }
    </style>

    <script>
    function toggleLangDropdown() {
        const dropdown = document.getElementById('langDropdown');
        dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.language-dropdown')) {
            const dropdown = document.getElementById('langDropdown');
            if (dropdown) dropdown.classList.remove('show');
        }
    });
    </script>

    <!-- Main Content -->
    <main>
        <?php
        // Flash ziņojumi
        if ($success = Session::flash('success')):
        ?>
            <div class="container mt-3">
                <div class="alert alert-success"><?= e($success) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($error = Session::flash('error')): ?>
            <div class="container mt-3">
                <div class="alert alert-error"><?= e($error) ?></div>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?= lang('app.name') ?></h3>
                    <p><?= lang('app.tagline') ?></p>
                </div>

                <div class="footer-section">
                    <h3>Saites</h3>
                    <ul>
                        <li><a href="/"><?= lang('nav.home') ?></a></li>
                        <li><a href="/products"><?= lang('nav.products') ?></a></li>
                        <li><a href="/about"><?= lang('nav.about') ?></a></li>
                        <li><a href="/contact"><?= lang('nav.contact') ?></a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Palīdzība</h3>
                    <ul>
                        <li><a href="#">Lietošanas noteikumi</a></li>
                        <li><a href="#">Privātuma politika</a></li>
                        <li><a href="#">BUJ</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Kontakti</h3>
                    <p>E-pasts: info@vietejaistirgus.lv</p>
                    <p>Tālrunis: +371 2000 0000</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= lang('app.name') ?>. Visas tiesības aizsargātas.</p>
            </div>
        </div>
    </footer>

    <script src="<?= asset('js/main.js') ?>"></script>
    <?= $extraJs ?? '' ?>
</body>
</html>

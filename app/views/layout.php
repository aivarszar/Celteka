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
                <div class="logo">
                    <a href="/"><?= lang('app.name') ?></a>
                </div>

                <!-- Language Selector -->
                <div class="language-selector">
                    <?php
                    $currentLocale = app()->getLang()->getLocale();
                    $languages = ['lv' => 'LV', 'en' => 'EN', 'ru' => 'RU', 'lt' => 'LT', 'ee' => 'EE'];
                    ?>
                    <?php foreach ($languages as $code => $label): ?>
                        <a href="/lang/<?= $code ?>" class="lang-btn <?= $code === $currentLocale ? 'active' : '' ?>" title="<?= app()->getLang()->getLanguageName($code) ?>">
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <ul class="nav-menu" id="navMenu">
                    <li><a href="/"><?= lang('nav.home') ?></a></li>
                    <li><a href="/products"><?= lang('nav.products') ?></a></li>

                    <?php if (Session::isLoggedIn()): ?>
                        <?php if (AuthHelper::isSeller()): ?>
                            <li><a href="/seller/products"><?= lang('nav.my_products') ?></a></li>
                            <li><a href="/seller/orders"><?= lang('nav.my_orders') ?></a></li>
                        <?php else: ?>
                            <li><a href="/orders"><?= lang('nav.my_orders') ?></a></li>
                        <?php endif; ?>
                        <li><a href="/profile"><?= lang('nav.profile') ?></a></li>
                        <?php if (AuthHelper::isAdmin()): ?>
                            <li><a href="/admin"><?= lang('nav.dashboard') ?></a></li>
                        <?php endif; ?>
                        <li><a href="/logout"><?= lang('nav.logout') ?></a></li>
                    <?php else: ?>
                        <li><a href="/login"><?= lang('nav.login') ?></a></li>
                        <li><a href="/register" class="btn btn-primary btn-sm"><?= lang('nav.register') ?></a></li>
                    <?php endif; ?>
                </ul>

                <div class="mobile-toggle" onclick="toggleMenu()">☰</div>
            </nav>
        </div>
    </header>

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

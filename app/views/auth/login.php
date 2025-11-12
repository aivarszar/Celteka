<?php ob_start(); ?>

<section class="container mt-4 mb-4">
    <div class="card" style="max-width: 500px; margin: 0 auto;">
        <div class="card-body">
            <h2 class="text-center"><?= lang('auth.login') ?></h2>

            <form action="/login" method="post" class="mt-3">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label"><?= lang('auth.email') ?> *</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= lang('auth.password') ?> *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-group d-flex justify-between align-center">
                    <label>
                        <input type="checkbox" name="remember"> <?= lang('auth.remember_me') ?>
                    </label>
                    <a href="/forgot-password"><?= lang('auth.forgot_password') ?></a>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <?= lang('auth.login') ?>
                </button>
            </form>

            <div class="text-center mt-3">
                <p><?= lang('auth.no_account') ?> <a href="/register"><?= lang('auth.register') ?></a></p>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$title = lang('auth.login') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>

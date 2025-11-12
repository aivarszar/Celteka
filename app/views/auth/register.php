<?php ob_start(); ?>

<section class="container mt-4 mb-4">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-body">
            <h2 class="text-center"><?= lang('auth.register') ?></h2>

            <form action="/register" method="post" class="mt-3">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label"><?= lang('auth.full_name') ?> *</label>
                    <input type="text" name="full_name" class="form-control" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= lang('auth.email') ?> *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= lang('auth.phone') ?></label>
                    <input type="tel" name="phone" class="form-control" placeholder="+371 20000000">
                </div>

                <div class="form-group">
                    <label class="form-label"><?= lang('auth.password') ?> *</label>
                    <input type="password" name="password" class="form-control" minlength="6" required>
                    <small>Vismaz 6 simboli</small>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= lang('auth.password_confirm') ?> *</label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= lang('auth.role') ?> *</label>
                    <select name="role" class="form-control" required>
                        <option value="buyer"><?= lang('auth.role_buyer') ?></option>
                        <option value="seller"><?= lang('auth.role_seller') ?></option>
                    </select>
                    <small>Izvēlieties savu galveno lomu platformā</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <?= lang('auth.register') ?>
                </button>
            </form>

            <div class="text-center mt-3">
                <p><?= lang('auth.have_account') ?> <a href="/login"><?= lang('auth.login') ?></a></p>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$title = lang('auth.register') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>

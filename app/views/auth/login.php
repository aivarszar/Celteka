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
                    <div style="position: relative;">
                        <input type="password" name="password" id="loginPassword" class="form-control" required style="padding-right: 40px;">
                        <button type="button" onclick="togglePassword('loginPassword', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2rem;" title="Rādīt/slēpt paroli">
                            👁️
                        </button>
                    </div>
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

<script>
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        button.innerHTML = '🙈';
        button.title = 'Slēpt paroli';
    } else {
        input.type = 'password';
        button.innerHTML = '👁️';
        button.title = 'Rādīt paroli';
    }
}
</script>

<?php
$content = ob_get_clean();
$title = lang('auth.login') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>

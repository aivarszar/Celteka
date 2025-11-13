<?php ob_start(); ?>

<section class="container mt-4 mb-4">
    <div class="card" style="max-width: 500px; margin: 0 auto;">
        <div class="card-body">
            <h2 class="text-center">Izveidot jaunu paroli</h2>
            <p class="text-center" style="color: var(--gray); margin-top: 1rem;">
                Lūdzu ievadiet jauno paroli un apstipriniet to.
            </p>

            <?php if (Session::has('errors')): ?>
                <div class="alert alert-danger mt-3">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        <?php foreach (Session::get('errors') as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/reset-password" method="post" class="mt-3">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= e($token) ?>">

                <div class="form-group">
                    <label class="form-label">Jaunā parole *</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="resetPassword" class="form-control" minlength="6" required autofocus style="padding-right: 40px;">
                        <button type="button" onclick="togglePassword('resetPassword', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2rem;" title="Rādīt/slēpt paroli">
                            👁️
                        </button>
                    </div>
                    <small>Vismaz 6 simboli</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Apstiprināt jauno paroli *</label>
                    <div style="position: relative;">
                        <input type="password" name="password_confirm" id="resetPasswordConfirm" class="form-control" required style="padding-right: 40px;">
                        <button type="button" onclick="togglePassword('resetPasswordConfirm', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2rem;" title="Rādīt/slēpt paroli">
                            👁️
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Atjaunot paroli
                </button>
            </form>

            <div class="text-center mt-3">
                <p>
                    <a href="/login">← Atpakaļ uz ielogošanos</a>
                </p>
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
$title = 'Atjaunot paroli - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>

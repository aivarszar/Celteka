<?php ob_start(); ?>

<section class="container mt-4 mb-4">
    <div class="card" style="max-width: 500px; margin: 0 auto;">
        <div class="card-body">
            <h2 class="text-center">Atjaunot paroli</h2>
            <p class="text-center" style="color: var(--gray); margin-top: 1rem;">
                Ievadiet savu e-pasta adresi un mēs nosūtīsim jums saiti paroles atjaunošanai.
            </p>

            <form action="/forgot-password" method="post" class="mt-3">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label"><?= lang('auth.email') ?> *</label>
                    <input type="email" name="email" class="form-control" required autofocus placeholder="jusu.epasts@example.com">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Nosūtīt atjaunošanas saiti
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

<?php
$content = ob_get_clean();
$title = 'Atjaunot paroli - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>

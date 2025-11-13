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
                    <div style="position: relative;">
                        <input type="password" name="password" id="registerPassword" class="form-control" minlength="6" required style="padding-right: 40px;">
                        <button type="button" onclick="togglePassword('registerPassword', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2rem;" title="Rādīt/slēpt paroli">
                            👁️
                        </button>
                    </div>
                    <small>Vismaz 6 simboli</small>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= lang('auth.password_confirm') ?> *</label>
                    <div style="position: relative;">
                        <input type="password" name="password_confirm" id="registerPasswordConfirm" class="form-control" required style="padding-right: 40px;">
                        <button type="button" onclick="togglePassword('registerPasswordConfirm', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2rem;" title="Rādīt/slēpt paroli">
                            👁️
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= lang('auth.roles') ?> *</label>
                    <div class="role-checkboxes" style="background: #f9f9f9; padding: 1rem; border-radius: 8px;">
                        <div class="form-check" style="margin-bottom: 0.75rem;">
                            <input type="checkbox" name="roles[]" value="buyer" id="role_buyer" class="form-check-input" checked>
                            <label class="form-check-label" for="role_buyer" style="font-weight: 500;">
                                🛒 <?= lang('auth.role_buyer') ?>
                            </label>
                            <small class="d-block" style="margin-left: 1.5rem; color: #666;">
                                Es vēlos iegādāties produktus un pakalpojumus
                            </small>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="roles[]" value="seller" id="role_seller" class="form-check-input">
                            <label class="form-check-label" for="role_seller" style="font-weight: 500;">
                                🏪 <?= lang('auth.role_seller') ?>
                            </label>
                            <small class="d-block" style="margin-left: 1.5rem; color: #666;">
                                Es vēlos pārdot savus produktus un pakalpojumus
                            </small>
                        </div>
                    </div>
                    <small style="color: #d9534f; display: none;" id="role_error">Lūdzu, izvēlieties vismaz vienu lomu</small>
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

// Validēt, ka vismaz viena loma ir izvēlēta
document.querySelector('form').addEventListener('submit', function(e) {
    const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
    const isAnyChecked = Array.from(roleCheckboxes).some(cb => cb.checked);
    const errorMsg = document.getElementById('role_error');

    if (!isAnyChecked) {
        e.preventDefault();
        errorMsg.style.display = 'block';
        roleCheckboxes[0].focus();
    } else {
        errorMsg.style.display = 'none';
    }
});

// Slēpt kļūdas ziņojumu, kad lietotājs atzīmē checkbox
document.querySelectorAll('input[name="roles[]"]').forEach(cb => {
    cb.addEventListener('change', function() {
        const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
        const isAnyChecked = Array.from(roleCheckboxes).some(cb => cb.checked);
        const errorMsg = document.getElementById('role_error');

        if (isAnyChecked) {
            errorMsg.style.display = 'none';
        }
    });
});
</script>

<?php
$content = ob_get_clean();
$title = lang('auth.register') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>

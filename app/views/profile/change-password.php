<!DOCTYPE html>
<html lang="<?= htmlspecialchars($config['app']['locale']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Lang::get('change_password') ?> - <?= htmlspecialchars($config['app']['name']) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php require_once ROOT_DIR . '/app/views/layout.php'; ?>

    <div class="container change-password-container">
        <div class="form-header">
            <h1><?= Lang::get('change_password') ?></h1>
            <a href="/profile" class="btn btn-secondary"><?= Lang::get('back_to_profile') ?></a>
        </div>

        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form action="/profile/update-password" method="POST" class="password-form">
                <?= csrf_field() ?>
                <div class="security-notice">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div>
                        <strong><?= Lang::get('security_notice') ?></strong>
                        <p><?= Lang::get('password_security_tip') ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="current_password"><?= Lang::get('current_password') ?> <span class="required">*</span></label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <div class="form-group">
                    <label for="new_password"><?= Lang::get('new_password') ?> <span class="required">*</span></label>
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        required
                        minlength="6"
                        autocomplete="new-password"
                    >
                    <small class="form-hint"><?= Lang::get('password_min_6_chars') ?></small>
                </div>

                <div class="form-group">
                    <label for="confirm_password"><?= Lang::get('confirm_password') ?> <span class="required">*</span></label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        required
                        minlength="6"
                        autocomplete="new-password"
                    >
                </div>

                <div class="password-strength" id="passwordStrength" style="display: none;">
                    <div class="strength-label"><?= Lang::get('password_strength') ?>:</div>
                    <div class="strength-meter">
                        <div class="strength-meter-fill" id="strengthMeterFill"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?= Lang::get('change_password') ?></button>
                    <a href="/profile" class="btn btn-outline"><?= Lang::get('cancel') ?></a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .change-password-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .security-notice {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            border-radius: 4px;
            margin-bottom: 2rem;
        }

        .security-notice svg {
            flex-shrink: 0;
            color: #2196F3;
        }

        .security-notice strong {
            display: block;
            color: #1976D2;
            margin-bottom: 0.25rem;
        }

        .security-notice p {
            margin: 0;
            color: #555;
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2196F3;
        }

        .form-hint {
            display: block;
            margin-top: 0.25rem;
            color: #666;
            font-size: 0.875rem;
        }

        .required {
            color: #f44336;
        }

        .password-strength {
            margin-bottom: 1.5rem;
        }

        .strength-label {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .strength-meter {
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .strength-meter-fill {
            height: 100%;
            width: 0%;
            transition: width 0.3s, background-color 0.3s;
        }

        .strength-text {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .strength-weak { background-color: #f44336; }
        .strength-fair { background-color: #ff9800; }
        .strength-good { background-color: #4CAF50; }
        .strength-strong { background-color: #2196F3; }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e0e0e0;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef5350;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #66bb6a;
        }

        .alert ul {
            margin: 0;
            padding-left: 1.5rem;
        }

        .alert li {
            margin-bottom: 0.25rem;
        }

        @media (max-width: 768px) {
            .form-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
            }
        }
    </style>

    <script>
        // Password strength checker
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const strengthContainer = document.getElementById('passwordStrength');
        const strengthFill = document.getElementById('strengthMeterFill');
        const strengthText = document.getElementById('strengthText');

        newPasswordInput.addEventListener('input', function() {
            const password = this.value;

            if (password.length === 0) {
                strengthContainer.style.display = 'none';
                return;
            }

            strengthContainer.style.display = 'block';

            const strength = calculatePasswordStrength(password);
            updateStrengthMeter(strength);
        });

        function calculatePasswordStrength(password) {
            let strength = 0;

            // Length
            if (password.length >= 6) strength += 25;
            if (password.length >= 10) strength += 25;

            // Contains lowercase
            if (/[a-z]/.test(password)) strength += 15;

            // Contains uppercase
            if (/[A-Z]/.test(password)) strength += 15;

            // Contains numbers
            if (/\d/.test(password)) strength += 10;

            // Contains special characters
            if (/[^a-zA-Z\d]/.test(password)) strength += 10;

            return strength;
        }

        function updateStrengthMeter(strength) {
            strengthFill.style.width = strength + '%';

            // Remove all strength classes
            strengthFill.className = 'strength-meter-fill';

            if (strength < 40) {
                strengthFill.classList.add('strength-weak');
                strengthText.textContent = '<?= Lang::get('weak') ?>';
                strengthText.style.color = '#f44336';
            } else if (strength < 60) {
                strengthFill.classList.add('strength-fair');
                strengthText.textContent = '<?= Lang::get('fair') ?>';
                strengthText.style.color = '#ff9800';
            } else if (strength < 80) {
                strengthFill.classList.add('strength-good');
                strengthText.textContent = '<?= Lang::get('good') ?>';
                strengthText.style.color = '#4CAF50';
            } else {
                strengthFill.classList.add('strength-strong');
                strengthText.textContent = '<?= Lang::get('strong') ?>';
                strengthText.style.color = '#2196F3';
            }
        }

        // Password match validation
        const form = document.querySelector('.password-form');
        form.addEventListener('submit', function(e) {
            if (newPasswordInput.value !== confirmPasswordInput.value) {
                e.preventDefault();
                alert('<?= Lang::get('passwords_dont_match') ?>');
                confirmPasswordInput.focus();
            }
        });
    </script>
</body>
</html>

<?php
ob_start();
?>

    <div class="container edit-profile-container">
        <div class="form-header">
            <h1><?= lang('profile.edit_profile') ?></h1>
            <a href="/profile" class="btn btn-secondary"><?= lang('profile.back_to_profile') ?></a>
        </div>

        <?php if (Session::has('errors')): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach (Session::get('errors') as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form action="/profile/update" method="POST" class="profile-form">
                <?= csrf_field() ?>
                <div class="form-section">
                    <h2><?= lang('profile.personal_information') ?></h2>

                    <div class="form-group">
                        <label for="name"><?= lang('common.name') ?> <span class="required">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?= e(Session::getOldInput('name', $user['full_name'] ?? '')) ?>"
                            required
                            maxlength="100"
                        >
                    </div>

                    <div class="form-group">
                        <label for="email"><?= lang('common.email') ?> <span class="required">*</span></label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= e(Session::getOldInput('email', $user['email'] ?? '')) ?>"
                            required
                            maxlength="100"
                        >
                    </div>

                    <div class="form-group">
                        <label for="phone"><?= lang('common.phone') ?></label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="<?= e(Session::getOldInput('phone', $user['phone'] ?? '')) ?>"
                            maxlength="20"
                            placeholder="+371 20000000"
                        >
                    </div>
                </div>

                <div class="form-section">
                    <h2>Lietotāja lomas</h2>
                    <?php
                    // Iegūt lietotāja pašreizējās lomas
                    $userRoles = AuthHelper::getUserRoles();
                    ?>
                    <div class="role-checkboxes" style="background: #f9f9f9; padding: 1rem; border-radius: 8px;">
                        <div class="form-check" style="margin-bottom: 0.75rem;">
                            <input type="checkbox" name="roles[]" value="buyer" id="role_buyer" class="form-check-input" <?= in_array('buyer', $userRoles) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="role_buyer" style="font-weight: 500;">
                                🛒 <?= lang('auth.role_buyer') ?>
                            </label>
                            <small class="d-block" style="margin-left: 1.5rem; color: #666;">
                                Es vēlos iegādāties produktus un pakalpojumus
                            </small>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="roles[]" value="seller" id="role_seller" class="form-check-input" <?= in_array('seller', $userRoles) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="role_seller" style="font-weight: 500;">
                                🏪 <?= lang('auth.role_seller') ?>
                            </label>
                            <small class="d-block" style="margin-left: 1.5rem; color: #666;">
                                Es vēlos pārdot savus produktus un pakalpojumus
                            </small>
                        </div>
                        <?php if (AuthHelper::isAdmin()): ?>
                        <div class="form-check" style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #ddd;">
                            <input type="checkbox" disabled checked style="opacity: 0.5;">
                            <label style="font-weight: 500; opacity: 0.7;">
                                👑 Administrators
                            </label>
                            <small class="d-block" style="margin-left: 1.5rem; color: #666;">
                                Admin loma nav maināma
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                    <small style="color: #d9534f; display: none;" id="role_error">Lūdzu, izvēlieties vismaz vienu lomu</small>
                </div>

                <div class="form-section">
                    <h2><?= lang('profile.address_information') ?></h2>

                    <div class="form-group">
                        <label for="address"><?= lang('profile.address') ?></label>
                        <input
                            type="text"
                            id="address"
                            name="address"
                            value="<?= e(Session::getOldInput('address', $user['address'] ?? '')) ?>"
                            maxlength="200"
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city"><?= lang('profile.city') ?></label>
                            <input
                                type="text"
                                id="city"
                                name="city"
                                value="<?= e(Session::getOldInput('city', $user['city'] ?? '')) ?>"
                                maxlength="100"
                            >
                        </div>

                        <div class="form-group">
                            <label for="postal_code"><?= lang('profile.postal_code') ?></label>
                            <input
                                type="text"
                                id="postal_code"
                                name="postal_code"
                                value="<?= e(Session::getOldInput('postal_code', $user['postal_code'] ?? '')) ?>"
                                maxlength="20"
                            >
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?= lang('profile.save_changes') ?></button>
                    <a href="/profile" class="btn btn-outline"><?= lang('common.cancel') ?></a>
                </div>
            </form>
        </div>


    <style>
        .edit-profile-container {
            max-width: 800px;
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

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section:last-of-type {
            margin-bottom: 0;
        }

        .form-section h2 {
            margin-bottom: 1.5rem;
            color: #333;
            font-size: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e0e0e0;
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

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2196F3;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .required {
            color: #f44336;
        }

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

            .form-row {
                grid-template-columns: 1fr;
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
    // Validēt, ka vismaz viena loma ir izvēlēta
    document.querySelector('.profile-form').addEventListener('submit', function(e) {
        const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
        const isAnyChecked = Array.from(roleCheckboxes).some(cb => cb.checked && !cb.disabled);
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
            const isAnyChecked = Array.from(roleCheckboxes).some(cb => cb.checked && !cb.disabled);
            const errorMsg = document.getElementById('role_error');

            if (isAnyChecked) {
                errorMsg.style.display = 'none';
            }
        });
    });
    </script>

<?php
$content = ob_get_clean();
$title = lang('edit_profile') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>

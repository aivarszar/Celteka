<!DOCTYPE html>
<html lang="<?= htmlspecialchars($config['app']['locale']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Lang::get('edit_profile') ?> - <?= htmlspecialchars($config['app']['name']) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php require_once ROOT_DIR . '/app/views/layout.php'; ?>

    <div class="container edit-profile-container">
        <div class="form-header">
            <h1><?= Lang::get('edit_profile') ?></h1>
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

        <div class="form-container">
            <form action="/profile/update" method="POST" class="profile-form">
                <div class="form-section">
                    <h2><?= Lang::get('personal_information') ?></h2>

                    <div class="form-group">
                        <label for="name"><?= Lang::get('name') ?> <span class="required">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?= htmlspecialchars($_SESSION['old_input']['name'] ?? $user['name']) ?>"
                            required
                            maxlength="100"
                        >
                    </div>

                    <div class="form-group">
                        <label for="email"><?= Lang::get('email') ?> <span class="required">*</span></label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($_SESSION['old_input']['email'] ?? $user['email']) ?>"
                            required
                            maxlength="100"
                        >
                    </div>

                    <div class="form-group">
                        <label for="phone"><?= Lang::get('phone') ?></label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="<?= htmlspecialchars($_SESSION['old_input']['phone'] ?? $user['phone'] ?? '') ?>"
                            maxlength="20"
                        >
                    </div>
                </div>

                <div class="form-section">
                    <h2><?= Lang::get('address_information') ?></h2>

                    <div class="form-group">
                        <label for="address"><?= Lang::get('address') ?></label>
                        <input
                            type="text"
                            id="address"
                            name="address"
                            value="<?= htmlspecialchars($_SESSION['old_input']['address'] ?? $user['address'] ?? '') ?>"
                            maxlength="200"
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city"><?= Lang::get('city') ?></label>
                            <input
                                type="text"
                                id="city"
                                name="city"
                                value="<?= htmlspecialchars($_SESSION['old_input']['city'] ?? $user['city'] ?? '') ?>"
                                maxlength="100"
                            >
                        </div>

                        <div class="form-group">
                            <label for="postal_code"><?= Lang::get('postal_code') ?></label>
                            <input
                                type="text"
                                id="postal_code"
                                name="postal_code"
                                value="<?= htmlspecialchars($_SESSION['old_input']['postal_code'] ?? $user['postal_code'] ?? '') ?>"
                                maxlength="20"
                            >
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?= Lang::get('save_changes') ?></button>
                    <a href="/profile" class="btn btn-outline"><?= Lang::get('cancel') ?></a>
                </div>
            </form>
        </div>
    </div>

    <?php unset($_SESSION['old_input']); ?>

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
</body>
</html>

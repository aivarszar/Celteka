<?php
/**
 * ProfileController - Lietotāja profila pārvaldība
 * Lietotājs var skatīt un rediģēt savu profilu
 */

class ProfileController {
    private $db;
    private $userModel;

    public function __construct() {
        $this->db = db();
        require_once ROOT_DIR . '/app/models/User.php';
        $this->userModel = new User();
    }

    /**
     * Parāda lietotāja profilu
     */
    public function index() {
        // Pārbaudīt vai lietotājs ir autorizēts
        if (!AuthHelper::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $userId = AuthHelper::getUserId();
        $user = $this->userModel->findById($userId);

        if (!$user) {
            header('Location: /');
            exit;
        }

        // Ielādēt user_meta laukus
        $userMeta = $this->userModel->getMeta($userId);
        $user['address'] = $userMeta['address'] ?? '';
        $user['city'] = $userMeta['city'] ?? '';
        $user['postal_code'] = $userMeta['postal_code'] ?? '';

        // Iegūt lietotāja statistiku
        $stats = $this->getUserStats($userId, $user['role']);

        // Iegūt lietotāja atsauksmes
        require_once ROOT_DIR . '/app/controllers/ReviewController.php';
        $reviewController = new ReviewController();
        $reviews = $reviewController->getUserReviews($userId);

        view('profile/index', [
            'user' => $user,
            'stats' => $stats,
            'reviews' => $reviews,
            'config' => config()
        ]);
    }

    /**
     * Parāda profila rediģēšanas formu
     */
    public function edit() {
        // Pārbaudīt vai lietotājs ir autorizēts
        if (!AuthHelper::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $userId = AuthHelper::getUserId();
        $user = $this->userModel->findById($userId);

        if (!$user) {
            header('Location: /');
            exit;
        }

        // Ielādēt user_meta laukus
        $userMeta = $this->userModel->getMeta($userId);
        $user['address'] = $userMeta['address'] ?? '';
        $user['city'] = $userMeta['city'] ?? '';
        $user['postal_code'] = $userMeta['postal_code'] ?? '';

        view('profile/edit', [
            'user' => $user,
            'config' => config()
        ]);
    }

    /**
     * Atjaunina lietotāja profilu
     */
    public function update() {
        error_log("=== ProfileController::update() START ===");

        try {
            // Pārbaudīt vai lietotājs ir autorizēts
            if (!AuthHelper::isLoggedIn()) {
                error_log("ProfileController::update() - User not logged in");
                header('Location: /login');
                exit;
            }
            error_log("ProfileController::update() - User is logged in");

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                error_log("ProfileController::update() - Not POST request");
                header('Location: /profile');
                exit;
            }
            error_log("ProfileController::update() - POST request confirmed");

            // CSRF verifikācija
            error_log("ProfileController::update() - About to verify CSRF");
            error_log("ProfileController::update() - POST data: " . json_encode($_POST));
            error_log("ProfileController::update() - SESSION data: " . json_encode($_SESSION ?? []));

            if (!isset($_POST['csrf_token'])) {
                error_log("ProfileController::update() - CSRF token missing from POST");
                Session::flash('error', 'CSRF token missing');
                header('Location: /profile/edit');
                exit;
            }
            error_log("ProfileController::update() - CSRF token in POST: " . $_POST['csrf_token']);

            RequestHelper::verifyCsrf();
            error_log("ProfileController::update() - CSRF verified successfully");
        } catch (Exception $e) {
            error_log("ProfileController::update() - FATAL ERROR: " . $e->getMessage());
            error_log("ProfileController::update() - Error trace: " . $e->getTraceAsString());
            Session::flash('error', 'System error: ' . $e->getMessage());
            header('Location: /profile/edit');
            exit;
        }

        $userId = AuthHelper::getUserId();
        error_log("ProfileController::update() - User ID: " . $userId);

        try {
            $user = $this->userModel->findById($userId);
            error_log("ProfileController::update() - User found: " . json_encode($user));
        } catch (Exception $e) {
            error_log("ProfileController::update() - Error finding user: " . $e->getMessage());
            throw $e;
        }

        if (!$user) {
            error_log("ProfileController::update() - User not found in database");
            Session::flash('error', lang('messages.user_not_found'));
            header('Location: /');
            exit;
        }

        // Validēt datus
        $errors = [];
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $postal_code = trim($_POST['postal_code'] ?? '');

        error_log("ProfileController::update() - Input data: name=$name, email=$email, phone=$phone");

        // Validācija
        if (empty($name)) {
            $errors[] = lang('messages.name_required');
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = lang('messages.email_invalid');
        }

        // Pārbaudīt vai e-pasts jau eksistē (izņemot pašreizējo lietotāju)
        if ($email !== $user['email']) {
            try {
                $existingUser = $this->userModel->findByEmail($email);
                if ($existingUser) {
                    $errors[] = lang('messages.email_exists');
                }
            } catch (Exception $e) {
                error_log("ProfileController::update() - Error checking email: " . $e->getMessage());
            }
        }

        if (!empty($errors)) {
            error_log("ProfileController::update() - Validation errors: " . json_encode($errors));
            Session::flash('errors', $errors);
            // Saglabāt tikai nepieciešamos laukus, nevis visu $_POST
            Session::set('old_input', [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'city' => $city,
                'postal_code' => $postal_code
            ]);
            header('Location: /profile/edit');
            exit;
        }

        error_log("ProfileController::update() - Validation passed, updating user");

        // Atjaunot profilu - users tabulas lauki
        $userData = [
            'full_name' => $name,
            'email' => $email,
            'phone' => $phone ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            error_log("ProfileController::update() - Updating user data: " . json_encode($userData));
            $result = $this->userModel->update($userId, $userData);
            error_log("ProfileController::update() - User update result: " . ($result ? 'success' : 'failed'));
        } catch (Exception $e) {
            error_log("ProfileController::update() - Error updating user: " . $e->getMessage());
            error_log("ProfileController::update() - Stack trace: " . $e->getTraceAsString());
            Session::flash('error', 'Database error: ' . $e->getMessage());
            header('Location: /profile/edit');
            exit;
        }

        // Saglabāt papildus laukus user_meta tabulā
        if ($result) {
            try {
                error_log("ProfileController::update() - Updating user meta");
                if (!empty($address)) {
                    $this->userModel->setMeta($userId, 'address', $address);
                    error_log("ProfileController::update() - Address meta saved");
                }
                if (!empty($city)) {
                    $this->userModel->setMeta($userId, 'city', $city);
                    error_log("ProfileController::update() - City meta saved");
                }
                if (!empty($postal_code)) {
                    $this->userModel->setMeta($userId, 'postal_code', $postal_code);
                    error_log("ProfileController::update() - Postal code meta saved");
                }
            } catch (Exception $e) {
                error_log("ProfileController::update() - Error updating meta: " . $e->getMessage());
            }
        }

        if ($result) {
            // Atjaunot sesijas datus - izmantojam pilnus datus no DB
            try {
                error_log("ProfileController::update() - Refreshing session data");
                $updatedUser = $this->userModel->findById($userId);
                if ($updatedUser) {
                    Session::setUser($updatedUser);
                    error_log("ProfileController::update() - Session refreshed");
                }
            } catch (Exception $e) {
                error_log("ProfileController::update() - Error refreshing session: " . $e->getMessage());
            }

            Session::flash('success', lang('messages.profile_updated'));
            error_log("ProfileController::update() - SUCCESS - Redirecting to profile");
        } else {
            Session::flash('error', lang('messages.profile_update_failed'));
            error_log("ProfileController::update() - FAILED - Redirecting to edit");
        }

        header('Location: /profile');
        error_log("=== ProfileController::update() END ===");
        exit;
    }

    /**
     * Parāda paroles maiņas formu
     */
    public function changePassword() {
        // Pārbaudīt vai lietotājs ir autorizēts
        if (!AuthHelper::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        view('profile/change-password', [
            'config' => config()
        ]);
    }

    /**
     * Atjaunina lietotāja paroli
     */
    public function updatePassword() {
        // Pārbaudīt vai lietotājs ir autorizēts
        if (!AuthHelper::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit;
        }

        // CSRF verifikācija
        RequestHelper::verifyCsrf();

        $userId = AuthHelper::getUserId();
        $user = $this->userModel->findById($userId);

        if (!$user) {
            Session::flash('error', lang('messages.user_not_found'));
            header('Location: /');
            exit;
        }

        $errors = [];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validācija
        if (empty($currentPassword)) {
            $errors[] = lang('messages.current_password_required');
        } elseif (!password_verify($currentPassword, $user['password_hash'])) {
            $errors[] = lang('messages.current_password_incorrect');
        }

        if (empty($newPassword)) {
            $errors[] = lang('messages.new_password_required');
        } elseif (strlen($newPassword) < 6) {
            $errors[] = lang('messages.password_min_length');
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = lang('messages.passwords_dont_match');
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            header('Location: /profile/change-password');
            exit;
        }

        // Atjaunot paroli
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $this->userModel->update($userId, [
            'password_hash' => $hashedPassword,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($result) {
            Session::flash('success', lang('messages.password_changed'));
            header('Location: /profile');
        } else {
            Session::flash('error', lang('messages.password_change_failed'));
            header('Location: /profile/change-password');
        }
        exit;
    }

    /**
     * Iegūt lietotāja statistiku
     */
    private function getUserStats($userId, $role) {
        $stats = [
            'orders_count' => 0,
            'products_count' => 0,
            'reviews_count' => 0,
        ];

        try {
            if ($role === 'buyer') {
                // Pircēja statistika
                $stats['orders_count'] = $this->db->fetchColumn(
                    "SELECT COUNT(*) FROM orders WHERE buyer_id = ?",
                    [$userId]
                );

                $stats['reviews_count'] = $this->db->fetchColumn(
                    "SELECT COUNT(*) FROM reviews WHERE reviewer_id = ?",
                    [$userId]
                );
            } elseif ($role === 'seller') {
                // Pārdevēja statistika
                $stats['products_count'] = $this->db->fetchColumn(
                    "SELECT COUNT(*) FROM products WHERE seller_id = ?",
                    [$userId]
                );

                $stats['orders_count'] = $this->db->fetchColumn(
                    "SELECT COUNT(*) FROM orders WHERE seller_id = ?",
                    [$userId]
                );

                $stats['reviews_count'] = $this->db->fetchColumn(
                    "SELECT COUNT(*) FROM reviews WHERE reviewed_id = ?",
                    [$userId]
                );
            }
        } catch (Exception $e) {
            error_log("Error fetching user stats: " . $e->getMessage());
        }

        return $stats;
    }
}

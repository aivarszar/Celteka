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

        // Iegūt lietotāja statistiku
        $stats = $this->getUserStats($userId, $user['role']);

        view('profile/index', [
            'user' => $user,
            'stats' => $stats,
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

        view('profile/edit', [
            'user' => $user,
            'config' => config()
        ]);
    }

    /**
     * Atjaunina lietotāja profilu
     */
    public function update() {
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
            Session::flash('error', Lang::get('user_not_found'));
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

        // Validācija
        if (empty($name)) {
            $errors[] = Lang::get('name_required');
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = Lang::get('email_invalid');
        }

        // Pārbaudīt vai e-pasts jau eksistē (izņemot pašreizējo lietotāju)
        if ($email !== $user['email']) {
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser) {
                $errors[] = Lang::get('email_exists');
            }
        }

        if (!empty($errors)) {
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

        // Atjaunot profilu
        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone ?: null,
            'address' => $address ?: null,
            'city' => $city ?: null,
            'postal_code' => $postal_code ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $result = $this->userModel->update($userId, $data);

        if ($result) {
            // Atjaunot sesijas datus
            $userData = Session::getUser();
            $userData['name'] = $name;
            $userData['email'] = $email;
            Session::setUser($userData);

            Session::flash('success', Lang::get('profile_updated'));
        } else {
            Session::flash('error', Lang::get('profile_update_failed'));
        }

        header('Location: /profile');
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
            Session::flash('error', Lang::get('user_not_found'));
            header('Location: /');
            exit;
        }

        $errors = [];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validācija
        if (empty($currentPassword)) {
            $errors[] = Lang::get('current_password_required');
        } elseif (!password_verify($currentPassword, $user['password_hash'])) {
            $errors[] = Lang::get('current_password_incorrect');
        }

        if (empty($newPassword)) {
            $errors[] = Lang::get('new_password_required');
        } elseif (strlen($newPassword) < 6) {
            $errors[] = Lang::get('password_min_length');
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = Lang::get('passwords_dont_match');
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
            Session::flash('success', Lang::get('password_changed'));
            header('Location: /profile');
        } else {
            Session::flash('error', Lang::get('password_change_failed'));
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

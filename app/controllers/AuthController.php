<?php
/**
 * AuthController
 * Autentifikācijas un reģistrācijas loģika
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLogin() {
        // Ja jau ielogojies, pāradresēt uz profilu
        if (Session::isLoggedIn()) {
            redirect('/profile');
        }

        view('auth/login');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }

        // Validācija
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            Session::flash('error', lang('messages.validation_error'));
            redirect('/login');
        }

        // Meklēt lietotāju
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            Session::flash('error', lang('messages.invalid_credentials'));
            redirect('/login');
        }

        // Pārbaudīt paroli
        if (!$this->userModel->verifyPassword($user, $password)) {
            Session::flash('error', lang('messages.invalid_credentials'));
            redirect('/login');
        }

        // Pārbaudīt vai konts aktīvs
        if (!$user['is_active']) {
            Session::flash('error', 'Jūsu konts ir deaktivizēts');
            redirect('/login');
        }

        // Ielogot lietotāju
        Session::setUser($user);
        Session::flash('success', lang('messages.login_success'));

        // Pāradresēt atkarībā no lomas
        if ($user['role_name'] === 'admin') {
            redirect('/admin');
        } elseif ($user['role_name'] === 'seller') {
            redirect('/seller/products');
        } else {
            redirect('/');
        }
    }

    public function showRegister() {
        // Ja jau ielogojies, pāradresēt uz profilu
        if (Session::isLoggedIn()) {
            redirect('/profile');
        }

        view('auth/register');
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/register');
        }

        // Validācija
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $role = $_POST['role'] ?? 'buyer';

        $errors = [];

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Nederīga e-pasta adrese';
        }

        if ($this->userModel->emailExists($email)) {
            $errors[] = 'Šī e-pasta adrese jau ir reģistrēta';
        }

        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Parolei jābūt vismaz 6 simbolus garai';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Paroles nesakrīt';
        }

        if (empty($fullName)) {
            $errors[] = 'Vārds ir obligāts';
        }

        if (!in_array($role, ['buyer', 'seller'])) {
            $errors[] = 'Nederīga loma';
        }

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            redirect('/register');
        }

        // Izveidot lietotāju
        $roleId = $this->userModel->getRoleId($role);

        $userId = $this->userModel->create([
            'email' => $email,
            'password' => $password,
            'full_name' => $fullName,
            'phone' => $phone,
            'role_id' => $roleId,
            'is_active' => true,
        ]);

        Session::flash('success', lang('messages.register_success'));
        redirect('/login');
    }

    public function logout() {
        Session::logout();
        Session::flash('success', lang('messages.logout_success'));
        redirect('/');
    }
}

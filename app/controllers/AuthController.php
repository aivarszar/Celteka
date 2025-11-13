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

    /**
     * Parāda paroles atjaunošanas pieprasījuma formu
     */
    public function showForgotPassword() {
        // Ja jau ielogojies, pāradresēt uz profilu
        if (Session::isLoggedIn()) {
            redirect('/profile');
        }

        view('auth/forgot-password');
    }

    /**
     * Apstrādā paroles atjaunošanas pieprasījumu
     */
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/forgot-password');
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Lūdzu ievadiet derīgu e-pasta adresi');
            redirect('/forgot-password');
        }

        // Pārbaudīt vai lietotājs eksistē
        $user = $this->userModel->findByEmail($email);

        // Drošības apsvērumu dēļ vienmēr parādām veiksmes ziņojumu,
        // pat ja e-pasts neeksistē (lai nepalīdzētu uzbrucējiem noteikt derīgus e-pastus)
        Session::flash('success', 'Ja šī e-pasta adrese ir reģistrēta, uz to tiks nosūtītas paroles atjaunošanas instrukcijas.');

        if ($user) {
            // Ģenerēt atjaunošanas token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Saglabāt token datubāzē
            $db = db();
            $db->insert('password_resets', [
                'email' => $email,
                'token' => hash('sha256', $token),
                'expires_at' => $expiry,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // TODO: Nosūtīt e-pastu ar atjaunošanas saiti
            // Pagaidām tikai logojam
            error_log("Password reset token for $email: $token (expires at $expiry)");
            error_log("Reset URL: " . ($_SERVER['REQUEST_SCHEME'] ?? 'http') . "://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "/reset-password?token=$token");
        }

        redirect('/forgot-password');
    }

    /**
     * Parāda paroles atjaunošanas formu ar token
     */
    public function showResetPassword() {
        // Ja jau ielogojies, pāradresēt uz profilu
        if (Session::isLoggedIn()) {
            redirect('/profile');
        }

        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            Session::flash('error', 'Nederīga vai tukša atjaunošanas saite');
            redirect('/login');
        }

        // Pārbaudīt vai token ir derīgs
        $db = db();
        $reset = $db->fetch(
            "SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1",
            ['token' => hash('sha256', $token)]
        );

        if (!$reset) {
            Session::flash('error', 'Atjaunošanas saite ir nederīga vai tās derīguma termiņš ir beidzies');
            redirect('/forgot-password');
        }

        view('auth/reset-password', ['token' => $token]);
    }

    /**
     * Apstrādā jaunu paroli
     */
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/forgot-password');
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validācija
        $errors = [];

        if (empty($token)) {
            Session::flash('error', 'Nederīgs atjaunošanas tokens');
            redirect('/forgot-password');
        }

        if (empty($password)) {
            $errors[] = 'Parole ir obligāta';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Parolei jābūt vismaz 6 simboliem';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Paroles nesakrīt';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            redirect('/reset-password?token=' . urlencode($token));
        }

        // Pārbaudīt token
        $db = db();
        $reset = $db->fetch(
            "SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1",
            ['token' => hash('sha256', $token)]
        );

        if (!$reset) {
            Session::flash('error', 'Atjaunošanas saite ir nederīga vai tās derīguma termiņš ir beidzies');
            redirect('/forgot-password');
        }

        // Atjaunot paroli
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user = $this->userModel->findByEmail($reset['email']);

        if ($user) {
            $this->userModel->update($user['id'], [
                'password_hash' => $hashedPassword,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Dzēst izmantoto token
            $db->delete('password_resets', 'token = :token', ['token' => hash('sha256', $token)]);

            Session::flash('success', 'Parole veiksmīgi atjaunota! Tagad varat ielogoties ar jauno paroli.');
            redirect('/login');
        } else {
            Session::flash('error', 'Lietotājs nav atrasts');
            redirect('/forgot-password');
        }
    }
}

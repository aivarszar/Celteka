<?php
/**
 * PageController - Statisko lapu pārvaldība
 * Par mums, Kontakti, u.c. statiskās lapas
 */

class PageController {
    /**
     * Par mums lapa
     */
    public function about() {
        view('pages/about', [
            'config' => config()
        ]);
    }

    /**
     * Kontaktu lapa
     */
    public function contact() {
        view('pages/contact', [
            'config' => config()
        ]);
    }

    /**
     * Apstrādā kontaktu formas iesniegšanu
     */
    public function submitContact() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /contact');
            exit;
        }

        // CSRF verifikācija
        RequestHelper::verifyCsrf();

        // Validēt datus
        $errors = [];
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name)) {
            $errors[] = lang('messages.name_required');
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = lang('messages.email_invalid');
        }

        if (empty($subject)) {
            $errors[] = 'Tēma ir obligāta';
        }

        if (empty($message)) {
            $errors[] = 'Ziņojums ir obligāts';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::set('old_input', [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message
            ]);
            header('Location: /contact');
            exit;
        }

        // Šeit varētu nosūtīt e-pastu vai saglabāt datubāzē
        // Pagaidām tikai parādīsim veiksmes ziņojumu
        Session::flash('success', 'Paldies! Jūsu ziņojums ir saņemts. Mēs ar Jums sazināsimies tuvākajā laikā.');
        header('Location: /contact');
        exit;
    }
}

<?php
/**
 * PageController - Statisko lapu pārvaldība
 * Par mums, Kontakti, u.c. statiskās lapas
 */

require_once __DIR__ . '/../helpers/RequestHelper.php';

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
        error_log("=== PageController::submitContact() START ===");

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                error_log("PageController::submitContact() - Not POST request");
                header('Location: /contact');
                exit;
            }
            error_log("PageController::submitContact() - POST request confirmed");

            // CSRF verifikācija
            try {
                error_log("PageController::submitContact() - Verifying CSRF");
                RequestHelper::verifyCsrf();
                error_log("PageController::submitContact() - CSRF verified");
            } catch (Exception $e) {
                error_log("PageController::submitContact() - CSRF error: " . $e->getMessage());
                Session::flash('error', 'CSRF verification failed');
                header('Location: /contact');
                exit;
            }

            // Validēt datus
            $errors = [];
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');

            error_log("PageController::submitContact() - Input: name=$name, email=$email, subject=$subject");

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
                error_log("PageController::submitContact() - Validation errors: " . json_encode($errors));
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
            error_log("PageController::submitContact() - Success, redirecting");
            Session::flash('success', 'Paldies! Jūsu ziņojums ir saņemts. Mēs ar Jums sazināsimies tuvākajā laikā.');
            header('Location: /contact');
            exit;

        } catch (Exception $e) {
            error_log("PageController::submitContact() - FATAL ERROR: " . $e->getMessage());
            error_log("PageController::submitContact() - Error trace: " . $e->getTraceAsString());
            Session::flash('error', 'System error: ' . $e->getMessage());
            header('Location: /contact');
            exit;
        }
    }
}

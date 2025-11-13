<?php
/**
 * EmailHelper
 * E-pasta sūtīšanas palīgfunkcijas ar fallback uz file-based sistēmu
 */

class EmailHelper {
    private static $emailsDir = __DIR__ . '/../../storage/emails';

    /**
     * Nosūta e-pastu
     *
     * @param string $to Saņēmēja e-pasta adrese
     * @param string $subject E-pasta temats
     * @param string $message E-pasta saturs (HTML vai text)
     * @param array $options Papildus opcijas (from, from_name, is_html)
     * @return bool Vai e-pasts tika veiksmīgi nosūtīts
     */
    public static function send($to, $subject, $message, $options = []) {
        $from = $options['from'] ?? config('mail.from_address', 'noreply@celteka.lv');
        $fromName = $options['from_name'] ?? config('mail.from_name', 'Celteka');
        $isHtml = $options['is_html'] ?? true;

        // Izveidot headers
        $headers = [];
        $headers[] = 'From: ' . $fromName . ' <' . $from . '>';
        $headers[] = 'Reply-To: ' . $from;
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        $headers[] = 'MIME-Version: 1.0';

        if ($isHtml) {
            $headers[] = 'Content-type: text/html; charset=UTF-8';
        } else {
            $headers[] = 'Content-type: text/plain; charset=UTF-8';
        }

        $headersString = implode("\r\n", $headers);

        // Mēģināt sūtīt ar PHP mail()
        $mailSent = false;
        if (function_exists('mail') && config('mail.driver', 'file') === 'mail') {
            try {
                $mailSent = mail($to, $subject, $message, $headersString);
                if ($mailSent) {
                    error_log("Email sent successfully to: $to");
                    return true;
                }
            } catch (Exception $e) {
                error_log("Email sending failed via mail(): " . $e->getMessage());
            }
        }

        // Fallback uz file-based sistēmu
        return self::saveToFile($to, $subject, $message, $headersString);
    }

    /**
     * Saglabā e-pastu kā failu (development/fallback režīms)
     *
     * @param string $to Saņēmēja adrese
     * @param string $subject Temats
     * @param string $message Saturs
     * @param string $headers Headers
     * @return bool
     */
    private static function saveToFile($to, $subject, $message, $headers) {
        try {
            // Izveidot emails direktoriju, ja neeksistē
            if (!is_dir(self::$emailsDir)) {
                mkdir(self::$emailsDir, 0755, true);
            }

            // Izveidot faila nosaukumu ar timestamp
            $timestamp = date('Y-m-d_H-i-s');
            $safeSubject = preg_replace('/[^a-zA-Z0-9_-]/', '_', $subject);
            $safeSubject = substr($safeSubject, 0, 50);
            $filename = $timestamp . '_' . $safeSubject . '_' . substr(md5($to . $timestamp), 0, 8) . '.eml';
            $filepath = self::$emailsDir . '/' . $filename;

            // Izveidot .eml format email
            $emailContent = "To: $to\r\n";
            $emailContent .= "Subject: $subject\r\n";
            $emailContent .= "$headers\r\n";
            $emailContent .= "Date: " . date('r') . "\r\n";
            $emailContent .= "\r\n";
            $emailContent .= $message;

            // Saglabāt failu
            $result = file_put_contents($filepath, $emailContent);

            if ($result !== false) {
                error_log("Email saved to file: $filepath");
                error_log("Email details - To: $to, Subject: $subject");
                return true;
            } else {
                error_log("Failed to save email to file: $filepath");
                return false;
            }
        } catch (Exception $e) {
            error_log("Error saving email to file: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Nosūta paroles atjaunošanas e-pastu
     *
     * @param string $email Saņēmēja e-pasts
     * @param string $token Atjaunošanas tokens
     * @param string $userName Lietotāja vārds
     * @return bool
     */
    public static function sendPasswordReset($email, $token, $userName = '') {
        $appName = config('app.name', 'Celteka');
        $resetUrl = self::getBaseUrl() . '/reset-password?token=' . $token;

        $subject = 'Paroles atjaunošana - ' . $appName;

        // HTML versija
        $message = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white !important; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Paroles atjaunošana</h1>
        </div>
        <div class="content">
            ' . ($userName ? '<p>Sveiki, ' . htmlspecialchars($userName) . '!</p>' : '<p>Sveiki!</p>') . '
            <p>Mēs saņēmām pieprasījumu atjaunot Jūsu paroli <strong>' . $appName . '</strong> platformā.</p>

            <p>Lai atjaunotu savu paroli, noklikšķiniet uz zemāk esošās pogas:</p>

            <center>
                <a href="' . $resetUrl . '" class="button">Atjaunot paroli</a>
            </center>

            <p>Vai kopējiet un ielīmējiet šo saiti savā pārlūkprogrammā:</p>
            <p style="word-break: break-all; background: white; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <a href="' . $resetUrl . '">' . $resetUrl . '</a>
            </p>

            <div class="warning">
                <strong>⚠️ Svarīgi:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>Šī saite ir derīga tikai <strong>1 stundu</strong></li>
                    <li>Ja Jūs nepieprasījāt paroles atjaunošanu, ignorējiet šo e-pastu</li>
                    <li>Nekad nedodiet šo saiti citiem cilvēkiem</li>
                </ul>
            </div>
        </div>
        <div class="footer">
            <p>Šis ir automātisks e-pasts no ' . $appName . '</p>
            <p>Lūdzu, neatbildiet uz šo e-pastu</p>
        </div>
    </div>
</body>
</html>';

        return self::send($email, $subject, $message, [
            'is_html' => true
        ]);
    }

    /**
     * Iegūt bāzes URL
     *
     * @return string
     */
    private static function getBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host;
    }

    /**
     * Iegūt visus saglabātos e-pastus (development režīmam)
     *
     * @param int $limit Maksimālais skaits
     * @return array
     */
    public static function getStoredEmails($limit = 50) {
        if (!is_dir(self::$emailsDir)) {
            return [];
        }

        $files = glob(self::$emailsDir . '/*.eml');

        // Kārtot pēc izmaiņu laika (jaunākie pirmie)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $files = array_slice($files, 0, $limit);

        $emails = [];
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $emails[] = [
                'filename' => basename($file),
                'filepath' => $file,
                'date' => date('Y-m-d H:i:s', filemtime($file)),
                'size' => filesize($file),
                'content' => $content
            ];
        }

        return $emails;
    }
}

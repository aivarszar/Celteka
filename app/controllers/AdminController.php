<?php
/**
 * AdminController
 * Administratora paneļa funkcionalitāte
 */

require_once __DIR__ . '/../helpers/RequestHelper.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';

class AdminController {
    private $db;

    public function __construct() {
        $this->db = db();

        // Pārbaudīt admin tiesības
        if (!AuthHelper::isAdmin()) {
            Session::flash('error', 'Piekļuve liegta. Nepieciešamas administratora tiesības.');
            redirect('/');
        }
    }

    /**
     * Administratora paneļa galvenā lapa - Dashboard
     */
    public function index() {
        // Iegūt platformas statistiku
        $stats = $this->getPlatformStats();

        view('admin/dashboard', [
            'stats' => $stats,
            'config' => config()
        ]);
    }

    /**
     * Lietotāju pārvaldība
     */
    public function users() {
        // Iegūt visus lietotājus ar viņu lomām
        $users = $this->db->fetchAll("
            SELECT u.*, 
                   GROUP_CONCAT(ura.role ORDER BY ura.role SEPARATOR ', ') as roles,
                   COUNT(DISTINCT o_buy.id) as orders_as_buyer,
                   COUNT(DISTINCT o_sell.id) as orders_as_seller,
                   COUNT(DISTINCT p.id) as products_count
            FROM users u
            LEFT JOIN user_role_assignments ura ON u.id = ura.user_id
            LEFT JOIN orders o_buy ON u.id = o_buy.buyer_id
            LEFT JOIN orders o_sell ON u.id = o_sell.seller_id
            LEFT JOIN products p ON u.id = p.seller_id
            GROUP BY u.id
            ORDER BY u.created_at DESC
        ");

        view('admin/users', [
            'users' => $users,
            'config' => config()
        ]);
    }

    /**
     * Lietotāja lomu pārvaldība
     */
    public function updateUserRoles($userId) {
        error_log("=== AdminController::updateUserRoles START ===");
        error_log("User ID parameter: " . var_export($userId, true));
        error_log("POST data: " . json_encode($_POST));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Not POST request, redirecting");
            redirect('/admin/users');
        }

        try {
            RequestHelper::verifyCsrf();
            error_log("CSRF verified");
        } catch (Exception $e) {
            error_log("CSRF verification failed: " . $e->getMessage());
            Session::flash('error', 'CSRF verifikācijas kļūda');
            redirect('/admin/users');
        }

        $roles = $_POST['roles'] ?? [];
        $availableRoles = ['admin', 'seller', 'buyer'];

        error_log("Roles to assign: " . json_encode($roles));

        // Validēt user ID
        if (empty($userId) || !is_numeric($userId)) {
            error_log("Invalid user ID: " . var_export($userId, true));
            Session::flash('error', 'Nederīgs lietotāja ID');
            redirect('/admin/users');
        }

        // Validēt lomas
        foreach ($roles as $role) {
            if (!in_array($role, $availableRoles)) {
                error_log("Invalid role: " . $role);
                Session::flash('error', 'Nederīga loma: ' . $role);
                redirect('/admin/users');
            }
        }

        try {
            // Pārbaudīt, vai lietotājs eksistē
            $userExists = $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE id = ?", [$userId]);
            if (!$userExists) {
                error_log("User not found: " . $userId);
                Session::flash('error', 'Lietotājs nav atrasts');
                redirect('/admin/users');
            }

            error_log("User exists, proceeding with role update");

            // Dzēst visas esošās lomas
            $this->db->query("DELETE FROM user_role_assignments WHERE user_id = ?", [$userId]);
            error_log("Deleted existing roles for user " . $userId);

            // Pievienot jaunās lomas
            $currentUserId = Session::getUserId();
            error_log("Current admin user ID: " . $currentUserId);

            foreach ($roles as $role) {
                $this->db->insert('user_role_assignments', [
                    'user_id' => $userId,
                    'role' => $role,
                    'assigned_by' => $currentUserId,
                    'assigned_at' => date('Y-m-d H:i:s')
                ]);
                error_log("Inserted role: " . $role . " for user " . $userId);
            }

            Session::flash('success', 'Lietotāja lomas veiksmīgi atjauninātas');
            error_log("=== AdminController::updateUserRoles SUCCESS ===");
        } catch (Exception $e) {
            error_log("Error updating user roles: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Session::flash('error', 'Kļūda atjauninot lomas: ' . $e->getMessage());
        }

        redirect('/admin/users');
    }

    /**
     * Produktu pārvaldība
     */
    public function products() {
        $products = $this->db->fetchAll("
            SELECT p.*, u.full_name as seller_name, c.name as category_name
            FROM products p
            LEFT JOIN users u ON p.seller_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.created_at DESC
            LIMIT 100
        ");

        view('admin/products', [
            'products' => $products,
            'config' => config()
        ]);
    }

    /**
     * Pasūtījumu pārvaldība
     */
    public function orders() {
        $orders = $this->db->fetchAll("
            SELECT o.*, 
                   b.full_name as buyer_name,
                   s.full_name as seller_name,
                   COUNT(oi.id) as items_count
            FROM orders o
            LEFT JOIN users b ON o.buyer_id = b.id
            LEFT JOIN users s ON o.seller_id = s.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.created_at DESC
            LIMIT 100
        ");

        view('admin/orders', [
            'orders' => $orders,
            'config' => config()
        ]);
    }

    /**
     * Atsauksmju pārvaldība
     */
    public function reviews() {
        $reviews = $this->db->fetchAll("
            SELECT r.*, 
                   reviewer.full_name as reviewer_name,
                   reviewed.full_name as reviewed_name,
                   o.order_number
            FROM reviews r
            LEFT JOIN users reviewer ON r.reviewer_id = reviewer.id
            LEFT JOIN users reviewed ON r.reviewed_id = reviewed.id
            LEFT JOIN orders o ON r.order_id = o.id
            ORDER BY r.created_at DESC
            LIMIT 100
        ");

        view('admin/reviews', [
            'reviews' => $reviews,
            'config' => config()
        ]);
    }

    /**
     * Iegūst platformas statistiku
     */
    private function getPlatformStats() {
        $stats = [];

        try {
            $stats['total_users'] = $this->db->fetchColumn("SELECT COUNT(*) FROM users");
            $stats['total_products'] = $this->db->fetchColumn("SELECT COUNT(*) FROM products WHERE is_active = 1");
            $stats['total_orders'] = $this->db->fetchColumn("SELECT COUNT(*) FROM orders");
            $stats['total_reviews'] = $this->db->fetchColumn("SELECT COUNT(*) FROM reviews");
            
            $stats['pending_orders'] = $this->db->fetchColumn("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
            $stats['completed_orders'] = $this->db->fetchColumn("SELECT COUNT(*) FROM orders WHERE status = 'completed'");
            
            $stats['total_revenue'] = $this->db->fetchColumn("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'") ?? 0;
            
            $stats['admin_count'] = $this->db->fetchColumn("SELECT COUNT(DISTINCT user_id) FROM user_role_assignments WHERE role = 'admin'");
            $stats['seller_count'] = $this->db->fetchColumn("SELECT COUNT(DISTINCT user_id) FROM user_role_assignments WHERE role = 'seller'");
            $stats['buyer_count'] = $this->db->fetchColumn("SELECT COUNT(DISTINCT user_id) FROM user_role_assignments WHERE role = 'buyer'");

            // Pēdējie reģistrētie lietotāji
            $stats['recent_users'] = $this->db->fetchAll("SELECT id, full_name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");

            // Pēdējie pasūtījumi
            $stats['recent_orders'] = $this->db->fetchAll("
                SELECT o.id, o.order_number, o.total_amount, o.status, o.created_at,
                       b.full_name as buyer_name
                FROM orders o
                LEFT JOIN users b ON o.buyer_id = b.id
                ORDER BY o.created_at DESC
                LIMIT 5
            ");

        } catch (Exception $e) {
            error_log("Error fetching admin stats: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Migrācijas lapa - lietotāju lomu migrācija
     */
    public function migrate() {
        view('admin/migrate', [
            'config' => config()
        ]);
    }

    /**
     * Izpilda lietotāju lomu migrāciju
     */
    public function runMigration() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/migrate');
        }

        RequestHelper::verifyCsrf();

        $results = [
            'migrated' => [],
            'skipped' => [],
            'errors' => []
        ];

        try {
            // Iegūt visus lietotājus ar role_id
            $users = $this->db->fetchAll("
                SELECT id, full_name, email, role_id
                FROM users
                WHERE role_id IS NOT NULL
                ORDER BY id
            ");

            if (empty($users)) {
                Session::flash('info', 'Nav lietotāju ar role_id. Migrācija nav nepieciešama.');
                redirect('/admin/migrate');
            }

            // Lomu kartējums
            $roleMapping = [
                1 => 'buyer',
                2 => 'seller',
                3 => 'admin'
            ];

            foreach ($users as $user) {
                $userId = $user['id'];
                $roleId = $user['role_id'];
                $role = $roleMapping[$roleId] ?? null;

                if (!$role) {
                    $results['errors'][] = "Lietotājs ID {$userId} ({$user['email']}) - nezināms role_id: {$roleId}";
                    continue;
                }

                // Pārbaudīt, vai loma jau eksistē
                $exists = $this->db->fetchColumn(
                    "SELECT COUNT(*) FROM user_role_assignments WHERE user_id = ? AND role = ?",
                    [$userId, $role]
                );

                if ($exists > 0) {
                    $results['skipped'][] = "Lietotājs {$user['email']} - loma '{$role}' jau eksistē";
                    continue;
                }

                // Pievienot lomu
                try {
                    $this->db->insert('user_role_assignments', [
                        'user_id' => $userId,
                        'role' => $role,
                        'assigned_at' => date('Y-m-d H:i:s')
                    ]);

                    $results['migrated'][] = "Lietotājs {$user['email']} → loma '{$role}'";
                } catch (Exception $e) {
                    $results['errors'][] = "Lietotājs {$user['email']} - " . $e->getMessage();
                }
            }

            // Pārbaudīt pirmā lietotāja admin lomu
            $firstUser = $this->db->fetchOne("SELECT id, full_name, email FROM users ORDER BY id LIMIT 1");
            if ($firstUser) {
                $hasAdmin = $this->db->fetchColumn(
                    "SELECT COUNT(*) FROM user_role_assignments WHERE user_id = ? AND role = 'admin'",
                    [$firstUser['id']]
                );

                if ($hasAdmin == 0) {
                    $this->db->insert('user_role_assignments', [
                        'user_id' => $firstUser['id'],
                        'role' => 'admin',
                        'assigned_at' => date('Y-m-d H:i:s')
                    ]);
                    $results['migrated'][] = "Pirmajam lietotājam {$firstUser['email']} piešķirta admin loma";
                }
            }

            // Saglabāt rezultātus sesijā
            Session::set('migration_results', $results);
            Session::flash('success', 'Migrācija pabeigta!');

        } catch (Exception $e) {
            error_log("Migration error: " . $e->getMessage());
            Session::flash('error', 'Migrācijas kļūda: ' . $e->getMessage());
        }

        redirect('/admin/migrate');
    }

    /**
     * E-pastu skatīšanas lapa (development režīmam)
     */
    public function emails() {
        require_once __DIR__ . '/../helpers/EmailHelper.php';

        $emails = EmailHelper::getStoredEmails(100);

        view('admin/emails', [
            'emails' => $emails,
            'config' => config()
        ]);
    }

    /**
     * Konkrēta e-pasta skatīšana
     */
    public function viewEmail($filename) {
        require_once __DIR__ . '/../helpers/EmailHelper.php';

        $emailsDir = __DIR__ . '/../../storage/emails';
        $filepath = $emailsDir . '/' . basename($filename);

        if (!file_exists($filepath)) {
            Session::flash('error', 'E-pasts nav atrasts');
            redirect('/admin/emails');
        }

        $content = file_get_contents($filepath);

        // Parsēt e-pasta saturu
        $headers = [];
        $body = '';
        $lines = explode("\r\n", $content);
        $bodyStarted = false;

        foreach ($lines as $line) {
            if (!$bodyStarted) {
                if (trim($line) === '') {
                    $bodyStarted = true;
                    continue;
                }
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $headers[trim($key)] = trim($value);
                }
            } else {
                $body .= $line . "\n";
            }
        }

        view('admin/view-email', [
            'filename' => basename($filename),
            'filepath' => $filepath,
            'headers' => $headers,
            'body' => $body,
            'config' => config()
        ]);
    }
}

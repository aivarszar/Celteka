<?php
/**
 * ApiController
 * API endpoints datu iegūšanai (locations, categories, utt.)
 */

class ApiController {
    private $db;

    public function __construct() {
        $this->db = db();
        header('Content-Type: application/json');
    }

    /**
     * Iegūt lokācijas
     */
    public function locations($type = null) {
        try {
            $sql = "SELECT id, parent_id, name, type, code
                    FROM locations
                    WHERE is_active = 1";

            $params = [];

            if ($type) {
                $sql .= " AND type = :type";
                $params['type'] = $type;
            }

            $sql .= " ORDER BY name ASC";

            $locations = $this->db->fetchAll($sql, $params);

            echo json_encode([
                'success' => true,
                'data' => $locations
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Kļūda iegūstot lokācijas'
            ]);
        }
    }

    /**
     * Iegūt kategorijas
     */
    public function categories() {
        try {
            $sql = "SELECT id, parent_id, name, slug, description, icon, sort_order
                    FROM categories
                    WHERE is_active = 1
                    ORDER BY sort_order ASC, name ASC";

            $categories = $this->db->fetchAll($sql, []);

            // Strukturēt kā koku
            $categoriesTree = $this->buildTree($categories);

            echo json_encode([
                'success' => true,
                'data' => $categoriesTree
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Kļūda iegūstot kategorijas'
            ]);
        }
    }

    /**
     * Uzbūvēt kategoriju koku
     */
    private function buildTree($elements, $parentId = null) {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);

                if ($children) {
                    $element['children'] = $children;
                }

                $branch[] = $element;
            }
        }

        return $branch;
    }

    /**
     * Meklēt produktus
     */
    public function searchProducts() {
        try {
            $query = $_GET['q'] ?? '';
            $limit = min(intval($_GET['limit'] ?? 10), 50);

            if (empty($query)) {
                echo json_encode([
                    'success' => true,
                    'data' => []
                ]);
                return;
            }

            $sql = "SELECT id, title, slug, price,
                           (SELECT image_path FROM product_images WHERE product_id = products.id AND is_primary = 1 LIMIT 1) as image
                    FROM products
                    WHERE is_active = 1 AND (title LIKE :query OR description LIKE :query)
                    ORDER BY title ASC
                    LIMIT " . $limit;

            $products = $this->db->fetchAll($sql, [
                'query' => '%' . $query . '%'
            ]);

            echo json_encode([
                'success' => true,
                'data' => $products
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Kļūda meklējot produktus'
            ]);
        }
    }
}

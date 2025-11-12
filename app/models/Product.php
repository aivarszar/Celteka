<?php
/**
 * Product Model
 * Produktu un pakalpojumu pārvaldība
 */

class Product {
    private $db;

    public function __construct() {
        $this->db = db();
    }

    public function create($data) {
        // Ģenerēt slug
        if (!isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        }

        return $this->db->insert('products', $data);
    }

    public function findById($id) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                       u.full_name as seller_name, u.email as seller_email,
                       l.name as location_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.seller_id = u.id
                LEFT JOIN locations l ON p.location_id = l.id
                WHERE p.id = :id";

        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function findBySlug($slug) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                       u.full_name as seller_name, u.email as seller_email, u.id as seller_id,
                       l.name as location_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.seller_id = u.id
                LEFT JOIN locations l ON p.location_id = l.id
                WHERE p.slug = :slug";

        return $this->db->fetch($sql, ['slug' => $slug]);
    }

    public function getAll($filters = [], $limit = 20, $offset = 0) {
        $where = ['p.is_active = 1'];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = :category_id';
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['location_id'])) {
            $where[] = 'p.location_id = :location_id';
            $params['location_id'] = $filters['location_id'];
        }

        if (!empty($filters['type'])) {
            $where[] = 'p.type = :type';
            $params['type'] = $filters['type'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(p.title LIKE :search OR p.description LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['seller_id'])) {
            $where[] = 'p.seller_id = :seller_id';
            $params['seller_id'] = $filters['seller_id'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT p.*, c.name as category_name, u.full_name as seller_name,
                       l.name as location_name,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.seller_id = u.id
                LEFT JOIN locations l ON p.location_id = l.id
                WHERE {$whereClause}
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset";

        $params['limit'] = $limit;
        $params['offset'] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    public function update($id, $data) {
        // Atjaunināt slug, ja mainās nosaukums
        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['title'], $id);
        }

        return $this->db->update('products', $data, 'id = :id', ['id' => $id]);
    }

    public function delete($id) {
        return $this->db->delete('products', 'id = :id', ['id' => $id]);
    }

    public function getImages($productId) {
        $sql = "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY is_primary DESC, sort_order ASC";
        return $this->db->fetchAll($sql, ['product_id' => $productId]);
    }

    public function addImage($productId, $imagePath, $isPrimary = false) {
        if ($isPrimary) {
            // Noņemt primary flag no citiem attēliem
            $this->db->update('product_images', ['is_primary' => 0], 'product_id = :id', ['id' => $productId]);
        }

        return $this->db->insert('product_images', [
            'product_id' => $productId,
            'image_path' => $imagePath,
            'is_primary' => $isPrimary ? 1 : 0,
        ]);
    }

    public function deleteImage($imageId) {
        return $this->db->delete('product_images', 'id = :id', ['id' => $imageId]);
    }

    // Meta datu pārvaldība
    public function getMeta($productId, $key = null) {
        if ($key === null) {
            $sql = "SELECT meta_key, meta_value FROM product_meta WHERE product_id = :product_id";
            $results = $this->db->fetchAll($sql, ['product_id' => $productId]);

            $meta = [];
            foreach ($results as $row) {
                $meta[$row['meta_key']] = $row['meta_value'];
            }
            return $meta;
        } else {
            $sql = "SELECT meta_value FROM product_meta WHERE product_id = :product_id AND meta_key = :key";
            return $this->db->fetchColumn($sql, ['product_id' => $productId, 'key' => $key]);
        }
    }

    public function setMeta($productId, $key, $value) {
        $existing = $this->db->fetch(
            "SELECT id FROM product_meta WHERE product_id = :product_id AND meta_key = :key",
            ['product_id' => $productId, 'key' => $key]
        );

        if ($existing) {
            return $this->db->update(
                'product_meta',
                ['meta_value' => $value],
                'product_id = :product_id AND meta_key = :key',
                ['product_id' => $productId, 'key' => $key]
            );
        } else {
            return $this->db->insert('product_meta', [
                'product_id' => $productId,
                'meta_key' => $key,
                'meta_value' => $value
            ]);
        }
    }

    public function incrementViews($id) {
        $sql = "UPDATE products SET views_count = views_count + 1 WHERE id = :id";
        return $this->db->query($sql, ['id' => $id]);
    }

    public function getCount($filters = []) {
        $where = ['is_active = 1'];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = 'category_id = :category_id';
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(title LIKE :search OR description LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT COUNT(*) FROM products WHERE {$whereClause}";

        return $this->db->fetchColumn($sql, $params);
    }

    private function generateSlug($title, $excludeId = null) {
        // Pārveidot latviešu burtus
        $slug = $this->transliterate($title);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        // Pārbaudīt vai slug jau eksistē
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function transliterate($text) {
        $latvian = ['ā', 'č', 'ē', 'ģ', 'ī', 'ķ', 'ļ', 'ņ', 'š', 'ū', 'ž',
                    'Ā', 'Č', 'Ē', 'Ģ', 'Ī', 'Ķ', 'Ļ', 'Ņ', 'Š', 'Ū', 'Ž'];
        $latin = ['a', 'c', 'e', 'g', 'i', 'k', 'l', 'n', 's', 'u', 'z',
                  'A', 'C', 'E', 'G', 'I', 'K', 'L', 'N', 'S', 'U', 'Z'];

        return str_replace($latvian, $latin, $text);
    }

    private function slugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM products WHERE slug = :slug";
        $params = ['slug' => $slug];

        if ($excludeId !== null) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        return $this->db->fetchColumn($sql, $params) > 0;
    }
}

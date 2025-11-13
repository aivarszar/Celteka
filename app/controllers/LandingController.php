<?php
/**
 * LandingController
 * Tiešās saites (landing pages) funkcionalitāte
 * Piemēram, konkrētam kopbraukšanas reisam vai pakalpojumam
 */

require_once __DIR__ . '/../models/Product.php';

class LandingController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    /**
     * Parādīt landing page
     */
    public function show($slug) {
        // Meklēt produktu pēc īpaša landing slug
        // Landing slug formāts: l-{product_id}-{random}

        // Ekstrahēt product ID no slug
        if (preg_match('/^l-(\d+)/', $slug, $matches)) {
            $productId = $matches[1];
        } else {
            // Mēģināt atrast pēc meta key
            $productId = $this->findProductByLandingSlug($slug);
        }

        if (!$productId) {
            http_response_code(404);
            view('errors/404', ['config' => config()]);
            return;
        }

        $product = $this->productModel->findById($productId);

        if (!$product || !$product['is_active']) {
            http_response_code(404);
            view('errors/404', ['config' => config()]);
            return;
        }

        // Iegūt produkta attēlus
        $images = $this->productModel->getImages($productId);

        // Iegūt produkta meta datus
        $meta = $this->productModel->getMeta($productId);

        // Palielināt skatījumu skaitu
        $this->productModel->incrementViews($productId);

        // Attēlot landing page
        view('landing/product', [
            'product' => $product,
            'images' => $images,
            'meta' => $meta,
            'config' => config()
        ]);
    }

    /**
     * Atrast produktu pēc landing slug
     */
    private function findProductByLandingSlug($slug) {
        $result = db()->fetch(
            "SELECT product_id FROM product_meta WHERE meta_key = 'landing_slug' AND meta_value = :slug",
            ['slug' => $slug]
        );

        return $result ? $result['product_id'] : null;
    }

    /**
     * Ģenerēt landing slug produktam
     */
    public static function generateLandingSlug($productId) {
        $randomPart = bin2hex(random_bytes(4));
        return 'l-' . $productId . '-' . $randomPart;
    }
}

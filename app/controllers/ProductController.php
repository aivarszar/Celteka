<?php
/**
 * ProductController
 * Produktu skatīšana un meklēšana
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Review.php';

class ProductController {
    private $productModel;
    private $reviewModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->reviewModel = new Review();
    }

    public function index() {
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'category_id' => $_GET['category'] ?? null,
            'location_id' => $_GET['location'] ?? null,
            'type' => $_GET['type'] ?? null,
        ];

        $products = $this->productModel->getAll($filters, $perPage, $offset);
        $totalCount = $this->productModel->getCount($filters);
        $totalPages = ceil($totalCount / $perPage);

        view('products/index', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'filters' => $filters,
        ]);
    }

    public function show($slug) {
        $product = $this->productModel->findBySlug($slug);

        if (!$product) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        // Palielināt skatījumu skaitu
        $this->productModel->incrementViews($product['id']);

        // Iegūt attēlus
        $images = $this->productModel->getImages($product['id']);

        // Iegūt meta datus (maršruts, laiks, kapacitāte)
        $meta = $this->productModel->getMeta($product['id']);

        // Iegūt pieteikšanās informāciju
        $hasBooked = false;
        $bookingStats = null;
        if (Session::isLoggedIn()) {
            require_once __DIR__ . '/../models/Booking.php';
            $bookingModel = new Booking();
            $hasBooked = $bookingModel->hasUserBooked($product['id'], Session::getUserId());
            $bookingStats = $bookingModel->getProductStats($product['id']);
        }

        // Iegūt pārdevēja vērtējumu
        $sellerRating = $this->reviewModel->getAverageRating($product['seller_id'], 'buyer_to_seller');
        $sellerReviewCount = $this->reviewModel->getRatingCount($product['seller_id'], 'buyer_to_seller');

        view('products/show', [
            'product' => $product,
            'images' => $images,
            'meta' => $meta,
            'hasBooked' => $hasBooked,
            'bookingStats' => $bookingStats,
            'sellerRating' => $sellerRating,
            'sellerReviewCount' => $sellerReviewCount,
        ]);
    }

    public function search() {
        $query = trim($_GET['q'] ?? '');
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        if (empty($query)) {
            redirect('/products');
        }

        $filters = ['search' => $query];
        $products = $this->productModel->getAll($filters, $perPage, $offset);
        $totalCount = $this->productModel->getCount($filters);
        $totalPages = ceil($totalCount / $perPage);

        view('products/search', [
            'query' => $query,
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
        ]);
    }

    public function category($slug) {
        // TODO: Implementēt kategorijas skatīšanu
        $this->index();
    }
}

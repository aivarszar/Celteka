<?php
/**
 * HomeController
 * Sākumlapas un galveno lapu kontrolieris
 */

require_once __DIR__ . '/../models/Product.php';

class HomeController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function index() {
        // Iegūt jaunākos produktus
        $featuredProducts = $this->productModel->getAll(['is_featured' => true], 8);
        $latestProducts = $this->productModel->getAll([], 12);

        view('home', [
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts,
        ]);
    }
}

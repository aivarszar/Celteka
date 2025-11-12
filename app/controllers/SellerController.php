<?php
/**
 * SellerController
 * Pārdevēja/Ražotāja funkcionalitāte
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';

class SellerController {
    private $productModel;
    private $orderModel;

    public function __construct() {
        // Pārbaudīt autentifikāciju
        AuthHelper::requireLogin();
        AuthHelper::requireRole(['seller', 'admin']);

        $this->productModel = new Product();
        $this->orderModel = new Order();
    }

    public function products() {
        $userId = Session::getUserId();
        $products = $this->productModel->getAll(['seller_id' => $userId], 100);

        view('seller/products', [
            'products' => $products,
        ]);
    }

    public function createProduct() {
        view('seller/product-form', [
            'product' => null,
            'action' => 'create',
        ]);
    }

    public function storeProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/seller/products');
        }

        $userId = Session::getUserId();

        // Validācija
        $errors = $this->validateProduct($_POST);

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            redirect('/seller/products/create');
        }

        // Izveidot produktu
        $productData = [
            'seller_id' => $userId,
            'category_id' => intval($_POST['category_id']),
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'price' => floatval($_POST['price']),
            'type' => $_POST['type'] ?? 'product',
            'stock_quantity' => intval($_POST['stock_quantity'] ?? 0),
            'location_id' => !empty($_POST['location_id']) ? intval($_POST['location_id']) : null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        $productId = $this->productModel->create($productData);

        // Apstrādāt attēlu augšupielādi (vienkāršota versija)
        // TODO: Pilnvērtīga attēlu augšupielāde

        Session::flash('success', 'Produkts pievienots veiksmīgi!');
        redirect('/seller/products');
    }

    public function editProduct($id) {
        $product = $this->productModel->findById($id);

        if (!$product || $product['seller_id'] != Session::getUserId()) {
            Session::flash('error', 'Produkts nav atrasts vai jums nav tiesību to rediģēt');
            redirect('/seller/products');
        }

        $images = $this->productModel->getImages($id);

        view('seller/product-form', [
            'product' => $product,
            'images' => $images,
            'action' => 'edit',
        ]);
    }

    public function updateProduct($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/seller/products');
        }

        $product = $this->productModel->findById($id);

        if (!$product || $product['seller_id'] != Session::getUserId()) {
            Session::flash('error', 'Produkts nav atrasts vai jums nav tiesību to rediģēt');
            redirect('/seller/products');
        }

        // Validācija
        $errors = $this->validateProduct($_POST);

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            redirect('/seller/products/' . $id . '/edit');
        }

        // Atjaunināt produktu
        $productData = [
            'category_id' => intval($_POST['category_id']),
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'price' => floatval($_POST['price']),
            'type' => $_POST['type'] ?? 'product',
            'stock_quantity' => intval($_POST['stock_quantity'] ?? 0),
            'location_id' => !empty($_POST['location_id']) ? intval($_POST['location_id']) : null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        $this->productModel->update($id, $productData);

        Session::flash('success', 'Produkts atjaunināts veiksmīgi!');
        redirect('/seller/products');
    }

    public function deleteProduct($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/seller/products');
        }

        $product = $this->productModel->findById($id);

        if (!$product || $product['seller_id'] != Session::getUserId()) {
            Session::flash('error', 'Produkts nav atrasts vai jums nav tiesību to dzēst');
            redirect('/seller/products');
        }

        $this->productModel->delete($id);

        Session::flash('success', 'Produkts dzēsts veiksmīgi!');
        redirect('/seller/products');
    }

    public function orders() {
        $userId = Session::getUserId();
        $orders = $this->orderModel->getUserOrders($userId, 'seller');

        view('seller/orders', [
            'orders' => $orders,
        ]);
    }

    private function validateProduct($data) {
        $errors = [];

        if (empty($data['title'])) {
            $errors[] = 'Nosaukums ir obligāts';
        }

        if (empty($data['description'])) {
            $errors[] = 'Apraksts ir obligāts';
        }

        if (empty($data['price']) || floatval($data['price']) <= 0) {
            $errors[] = 'Cena ir obligāta un jābūt lielākai par 0';
        }

        if (empty($data['category_id'])) {
            $errors[] = 'Kategorija ir obligāta';
        }

        return $errors;
    }
}

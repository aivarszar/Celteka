<?php
/**
 * CartController
 * Iepirkumu groza un checkout funkcionalitāte
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/RequestHelper.php';

class CartController {
    private $productModel;
    private $orderModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->orderModel = new Order();
    }

    /**
     * Parādīt grozu
     */
    public function index() {
        $cart = Session::get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $this->productModel->findById($productId);

            if ($product && $product['is_active']) {
                $subtotal = $product['price'] * $quantity;
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
        }

        view('cart/index', [
            'cartItems' => $cartItems,
            'total' => $total,
            'config' => config()
        ]);
    }

    /**
     * Pievienot produktu grozam
     */
    public function add($productId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/products');
        }

        // CSRF verifikācija
        RequestHelper::verifyCsrf();

        $product = $this->productModel->findById($productId);

        if (!$product || !$product['is_active']) {
            Session::flash('error', 'Produkts nav atrasts vai nav pieejams');
            redirect('/products');
        }

        $quantity = intval($_POST['quantity'] ?? 1);

        if ($quantity < 1) {
            $quantity = 1;
        }

        // Pārbaudīt krājumus
        if ($product['type'] === 'product' && $product['stock_quantity'] < $quantity) {
            Session::flash('error', 'Nepietiekams daudzums noliktavā');
            redirect('/product/' . $product['slug']);
        }

        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        // Pārbaudīt kopējo daudzumu pret krājumiem
        if ($product['type'] === 'product' && $cart[$productId] > $product['stock_quantity']) {
            $cart[$productId] = $product['stock_quantity'];
            Session::flash('warning', 'Daudzums pielāgots pieejamam krājumam');
        }

        Session::set('cart', $cart);
        Session::flash('success', 'Produkts pievienots grozam!');

        redirect('/cart');
    }

    /**
     * Atjaunināt produkta daudzumu grozā
     */
    public function update($productId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/cart');
        }

        // CSRF verifikācija
        RequestHelper::verifyCsrf();

        $quantity = intval($_POST['quantity'] ?? 0);
        $cart = Session::get('cart', []);

        if ($quantity < 1) {
            // Noņemt no groza
            unset($cart[$productId]);
        } else {
            // Pārbaudīt krājumus
            $product = $this->productModel->findById($productId);

            if ($product && $product['type'] === 'product' && $quantity > $product['stock_quantity']) {
                $quantity = $product['stock_quantity'];
                Session::flash('warning', 'Daudzums pielāgots pieejamam krājumam');
            }

            $cart[$productId] = $quantity;
        }

        Session::set('cart', $cart);
        Session::flash('success', 'Grozs atjaunināts');
        redirect('/cart');
    }

    /**
     * Noņemt produktu no groza
     */
    public function remove($productId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/cart');
        }

        // CSRF verifikācija
        RequestHelper::verifyCsrf();

        $cart = Session::get('cart', []);
        unset($cart[$productId]);
        Session::set('cart', $cart);

        Session::flash('success', 'Produkts noņemts no groza');
        redirect('/cart');
    }

    /**
     * Checkout forma
     */
    public function checkout() {
        AuthHelper::requireLogin();

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            Session::flash('error', 'Jūsu grozs ir tukšs');
            redirect('/products');
        }

        // Ielādēt produktus
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $this->productModel->findById($productId);

            if ($product && $product['is_active']) {
                $subtotal = $product['price'] * $quantity;
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
        }

        if (empty($cartItems)) {
            Session::flash('error', 'Grozā nav derīgu produktu');
            redirect('/cart');
        }

        // Ielādēt piegādes metodes
        $deliveryMethods = db()->fetchAll("SELECT * FROM delivery_methods WHERE is_active = 1", []);

        view('cart/checkout', [
            'cartItems' => $cartItems,
            'total' => $total,
            'deliveryMethods' => $deliveryMethods,
            'config' => config()
        ]);
    }

    /**
     * Izveidot pasūtījumu
     */
    public function placeOrder() {
        AuthHelper::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/checkout');
        }

        // CSRF verifikācija
        RequestHelper::verifyCsrf();

        $userId = Session::getUserId();
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            Session::flash('error', 'Jūsu grozs ir tukšs');
            redirect('/products');
        }

        // Validācija
        $errors = [];
        $deliveryMethodId = intval($_POST['delivery_method_id'] ?? 0);
        $deliveryAddress = trim($_POST['delivery_address'] ?? '');
        $deliveryNotes = trim($_POST['delivery_notes'] ?? '');
        $paymentMethod = $_POST['payment_method'] ?? 'cash';

        if (!$deliveryMethodId) {
            $errors[] = 'Izvēlieties piegādes metodi';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            redirect('/checkout');
        }

        // Grupēt pasūtījumus pēc pārdevējiem
        $ordersBySeller = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $this->productModel->findById($productId);

            if (!$product || !$product['is_active']) {
                continue;
            }

            // Pārbaudīt krājumus
            if ($product['type'] === 'product' && $product['stock_quantity'] < $quantity) {
                Session::flash('error', 'Produkts "' . $product['title'] . '" nav pieejams pietiekamā daudzumā');
                redirect('/cart');
            }

            $sellerId = $product['seller_id'];

            if (!isset($ordersBySeller[$sellerId])) {
                $ordersBySeller[$sellerId] = [
                    'items' => [],
                    'total' => 0
                ];
            }

            $subtotal = $product['price'] * $quantity;
            $ordersBySeller[$sellerId]['items'][] = [
                'product_id' => $productId,
                'product_title' => $product['title'],
                'quantity' => $quantity,
                'price' => $product['price'],
                'subtotal' => $subtotal
            ];
            $ordersBySeller[$sellerId]['total'] += $subtotal;
            $total += $subtotal;
        }

        // Izveidot pasūtījumus katram pārdevējam
        $createdOrders = [];

        try {
            foreach ($ordersBySeller as $sellerId => $orderData) {
                // Izveidot pasūtījumu
                $orderDataToInsert = [
                    'buyer_id' => $userId,
                    'seller_id' => $sellerId,
                    'total_amount' => $orderData['total'],
                    'delivery_method_id' => $deliveryMethodId,
                    'delivery_address' => $deliveryAddress,
                    'delivery_notes' => $deliveryNotes,
                    'status' => 'pending',
                    'payment_method' => $paymentMethod,
                    'payment_status' => 'pending',
                ];

                $orderId = $this->orderModel->create($orderDataToInsert);

                // Pievienot pasūtījuma pozīcijas
                foreach ($orderData['items'] as $item) {
                    $this->orderModel->addItem($orderId, $item);

                    // Samazināt krājumus
                    $product = $this->productModel->findById($item['product_id']);
                    if ($product && $product['type'] === 'product') {
                        $newStock = max(0, $product['stock_quantity'] - $item['quantity']);
                        $this->productModel->update($item['product_id'], [
                            'stock_quantity' => $newStock
                        ]);
                    }
                }

                $createdOrders[] = $orderId;
            }

            // Notīrīt grozu
            Session::set('cart', []);

            Session::flash('success', 'Pasūtījums veiksmīgi izveidots! Pārdevējs sazināsies ar jums.');

            // Ja viens pasūtījums, pāradresēt uz to
            if (count($createdOrders) === 1) {
                redirect('/order/' . $createdOrders[0]);
            } else {
                redirect('/orders');
            }

        } catch (Exception $e) {
            error_log("Error creating order: " . $e->getMessage());
            Session::flash('error', 'Kļūda veidojot pasūtījumu. Lūdzu, mēģiniet vēlreiz.');
            redirect('/checkout');
        }
    }
}

<?php
/**
 * BookingController
 * Pieteikšanās/rezervāciju pārvaldība
 */

require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/RequestHelper.php';

class BookingController {
    private $bookingModel;
    private $productModel;

    public function __construct() {
        AuthHelper::requireLogin();
        $this->bookingModel = new Booking();
        $this->productModel = new Product();
    }

    /**
     * Pieteikties produktam
     */
    public function book($productId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/products/' . $productId);
        }

        RequestHelper::verifyCsrf();

        $userId = Session::getUserId();
        $product = $this->productModel->findById($productId);

        if (!$product) {
            Session::flash('error', 'Produkts nav atrasts');
            redirect('/products');
        }

        // Pārbaudīt vai lietotājs mēģina pieteikties savam produktam
        if ($product['seller_id'] == $userId) {
            Session::flash('error', 'Jūs nevarat pieteikties savam produktam');
            redirect('/products/' . $productId);
        }

        // Pārbaudīt vai jau ir pieteicies
        if ($this->bookingModel->hasUserBooked($productId, $userId)) {
            Session::flash('error', 'Jūs jau esat pieteicies šim produktam');
            redirect('/products/' . $productId);
        }

        // Pārbaudīt kapacitāti (ja ir ierobežota)
        $meta = $this->productModel->getMeta($productId);
        if (isset($meta['capacity'])) {
            $capacity = intval($meta['capacity']);
            $currentBookings = $this->bookingModel->countConfirmedBookings($productId);

            if ($currentBookings >= $capacity) {
                Session::flash('error', 'Vietas ir aizņemtas. Kapacitāte: ' . $capacity);
                redirect('/products/' . $productId);
            }
        }

        $quantity = intval($_POST['quantity'] ?? 1);
        $notes = trim($_POST['notes'] ?? '');

        // Izveidot pieteikumu
        $bookingData = [
            'product_id' => $productId,
            'buyer_id' => $userId,
            'seller_id' => $product['seller_id'],
            'quantity' => $quantity,
            'notes' => $notes,
            'status' => 'pending'
        ];

        // Ja ir pakalpojums ar datumu/laiku, pievienot
        if (!empty($meta['service_date'])) {
            $bookingData['booking_date'] = $meta['service_date'];
        }
        if (!empty($meta['service_time'])) {
            $bookingData['booking_time'] = $meta['service_time'];
        }

        try {
            $bookingId = $this->bookingModel->create($bookingData);

            Session::flash('success', 'Pieteikšanās veiksmīga! Pārdevējs sazināsies ar jums.');
            redirect('/bookings');

        } catch (Exception $e) {
            error_log("Booking error: " . $e->getMessage());
            Session::flash('error', 'Kļūda pieteikšanās laikā');
            redirect('/products/' . $productId);
        }
    }

    /**
     * Lietotāja pieteikšanās saraksts
     */
    public function index() {
        $userId = Session::getUserId();
        $userRoles = Session::getUserRoles();

        // Pircēja un pārdevēja pieteikšanās
        $buyerBookings = $this->bookingModel->getByUser($userId, 'buyer');
        $sellerBookings = [];

        if (in_array('seller', $userRoles) || in_array('admin', $userRoles)) {
            $sellerBookings = $this->bookingModel->getByUser($userId, 'seller');
        }

        view('bookings/index', [
            'buyerBookings' => $buyerBookings,
            'sellerBookings' => $sellerBookings,
        ]);
    }

    /**
     * Pieteikuma detaļas
     */
    public function show($id) {
        $userId = Session::getUserId();
        $booking = $this->bookingModel->findById($id);

        if (!$booking) {
            Session::flash('error', 'Pieteikums nav atrasts');
            redirect('/bookings');
        }

        // Pārbaudīt tiesības
        if ($booking['buyer_id'] != $userId && $booking['seller_id'] != $userId) {
            Session::flash('error', 'Nav tiesību skatīt šo pieteikumu');
            redirect('/bookings');
        }

        view('bookings/show', [
            'booking' => $booking,
        ]);
    }

    /**
     * Produkta pieteikumu saraksts (pārdevējam)
     */
    public function productBookings($productId) {
        $userId = Session::getUserId();
        $product = $this->productModel->findById($productId);

        if (!$product) {
            Session::flash('error', 'Produkts nav atrasts');
            redirect('/seller/products');
        }

        // Pārbaudīt vai lietotājs ir produkta īpašnieks
        if ($product['seller_id'] != $userId) {
            Session::flash('error', 'Nav tiesību skatīt šo informāciju');
            redirect('/seller/products');
        }

        $bookings = $this->bookingModel->getByProduct($productId);
        $stats = $this->bookingModel->getProductStats($productId);
        $meta = $this->productModel->getMeta($productId);

        view('bookings/product-list', [
            'product' => $product,
            'bookings' => $bookings,
            'stats' => $stats,
            'meta' => $meta,
        ]);
    }

    /**
     * Apstiprināt pieteikumu (pārdevējs)
     */
    public function confirm($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/bookings');
        }

        RequestHelper::verifyCsrf();

        $userId = Session::getUserId();
        $booking = $this->bookingModel->findById($id);

        if (!$booking) {
            Session::flash('error', 'Pieteikums nav atrasts');
            redirect('/bookings');
        }

        // Pārbaudīt vai lietotājs ir pārdevējs
        if ($booking['seller_id'] != $userId) {
            Session::flash('error', 'Nav tiesību mainīt šo pieteikumu');
            redirect('/bookings');
        }

        $this->bookingModel->confirm($id);

        Session::flash('success', 'Pieteikums apstiprināts');
        redirect('/bookings/product/' . $booking['product_id']);
    }

    /**
     * Atcelt pieteikumu
     */
    public function cancel($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/bookings');
        }

        RequestHelper::verifyCsrf();

        $userId = Session::getUserId();
        $booking = $this->bookingModel->findById($id);

        if (!$booking) {
            Session::flash('error', 'Pieteikums nav atrasts');
            redirect('/bookings');
        }

        // Pārbaudīt tiesības (pircējs vai pārdevējs var atcelt)
        if ($booking['buyer_id'] != $userId && $booking['seller_id'] != $userId) {
            Session::flash('error', 'Nav tiesību atcelt šo pieteikumu');
            redirect('/bookings');
        }

        $this->bookingModel->cancel($id);

        Session::flash('success', 'Pieteikums atcelts');

        // Novirzīt atpakaļ uz atbilstošo lapu
        if ($booking['seller_id'] == $userId) {
            redirect('/bookings/product/' . $booking['product_id']);
        } else {
            redirect('/bookings');
        }
    }

    /**
     * Pabeigt pieteikumu (pārdevējs)
     */
    public function complete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/bookings');
        }

        RequestHelper::verifyCsrf();

        $userId = Session::getUserId();
        $booking = $this->bookingModel->findById($id);

        if (!$booking) {
            Session::flash('error', 'Pieteikums nav atrasts');
            redirect('/bookings');
        }

        // Pārbaudīt vai lietotājs ir pārdevējs
        if ($booking['seller_id'] != $userId) {
            Session::flash('error', 'Nav tiesību mainīt šo pieteikumu');
            redirect('/bookings');
        }

        $this->bookingModel->complete($id);

        Session::flash('success', 'Pieteikums pabeigts');
        redirect('/bookings/product/' . $booking['product_id']);
    }
}

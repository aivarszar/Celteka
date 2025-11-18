<?php
/**
 * ImportController
 * Datu importēšana no ārējiem avotiem (piemēram, ss.lv)
 */

require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/RequestHelper.php';
require_once __DIR__ . '/../models/Product.php';

class ImportController {
    private $productModel;

    public function __construct() {
        // Tikai autorizētiem lietotājiem
        AuthHelper::requireLogin();
        AuthHelper::requireRole(['seller', 'admin']);

        $this->productModel = new Product();
    }

    /**
     * Importēšanas forma
     */
    public function index() {
        view('import/index', [
            'config' => config()
        ]);
    }

    /**
     * Importēt no ss.lv URL
     */
    public function fromUrl() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/import');
        }

        // CSRF verifikācija
        RequestHelper::verifyCsrf();

        $url = trim($_POST['url'] ?? '');

        if (empty($url)) {
            Session::flash('error', 'URL ir obligāts');
            redirect('/import');
        }

        // Pārbaudīt vai URL ir no ss.lv
        if (!preg_match('/^https?:\/\/(www\.)?ss\.(lv|com)/', $url)) {
            Session::flash('error', 'Atbalstīts tikai ss.lv domēns');
            redirect('/import');
        }

        try {
            // Iegūt HTML saturu
            $html = $this->fetchUrl($url);

            if (!$html) {
                Session::flash('error', 'Neizdevās ielādēt sludinājumu');
                redirect('/import');
            }

            // Parsēt ss.lv sludinājumu
            $productData = $this->parseSsLv($html, $url);

            if (!$productData) {
                Session::flash('error', 'Neizdevās nolasīt sludinājuma datus');
                redirect('/import');
            }

            // Saglabāt sesijā priekšskatījumam
            Session::set('import_preview', $productData);

            redirect('/import/preview');

        } catch (Exception $e) {
            error_log("Import error: " . $e->getMessage());
            Session::flash('error', 'Kļūda importējot: ' . $e->getMessage());
            redirect('/import');
        }
    }

    /**
     * Priekšskatījums pirms importēšanas
     */
    public function preview() {
        $productData = Session::get('import_preview');

        if (!$productData) {
            Session::flash('error', 'Nav datu priekšskatījumam');
            redirect('/import');
        }

        view('import/preview', [
            'product' => $productData,
            'config' => config()
        ]);
    }

    /**
     * Apstiprināt un saglabāt importēto produktu
     */
    public function confirm() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/import');
        }

        // CSRF verifikācija
        RequestHelper::verifyCsrf();

        $productData = Session::get('import_preview');

        if (!$productData) {
            Session::flash('error', 'Nav datu importēšanai');
            redirect('/import');
        }

        $userId = Session::getUserId();

        // Pievienot seller_id
        $productData['seller_id'] = $userId;

        // Ļaut rediģēt laukus no formas
        $productData['title'] = trim($_POST['title'] ?? $productData['title']);
        $productData['description'] = trim($_POST['description'] ?? $productData['description']);
        $productData['price'] = floatval($_POST['price'] ?? $productData['price']);
        $productData['category_id'] = intval($_POST['category_id'] ?? 1);
        $productData['is_active'] = isset($_POST['is_active']) ? 1 : 0;

        try {
            $productId = $this->productModel->create($productData);

            // Saglabāt attēlus, ja ir
            if (!empty($productData['images'])) {
                foreach ($productData['images'] as $index => $imageUrl) {
                    $this->productModel->addImage($productId, $imageUrl, $index === 0);
                }
            }

            // Saglabāt avota URL kā meta
            $this->productModel->setMeta($productId, 'source_url', $productData['source_url'] ?? '');

            // Notīrīt sesiju
            Session::remove('import_preview');

            Session::flash('success', 'Produkts importēts veiksmīgi!');
            redirect('/seller/products');

        } catch (Exception $e) {
            error_log("Import save error: " . $e->getMessage());
            Session::flash('error', 'Kļūda saglabājot produktu: ' . $e->getMessage());
            redirect('/import/preview');
        }
    }

    /**
     * Iegūt URL saturu
     */
    private function fetchUrl($url) {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0 (compatible; LocalMarketplace/1.0)\r\n",
                'timeout' => 10
            ]
        ]);

        return @file_get_contents($url, false, $context);
    }

    /**
     * Parsēt ss.lv HTML
     */
    private function parseSsLv($html, $url) {
        // Izmantot DOMDocument lai parsētu HTML
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);

        $data = [
            'source_url' => $url,
            'images' => []
        ];

        // Nosaukums (h1 vai h2 ar class "headtitle")
        $titleNodes = $xpath->query("//h1[@class='headtitle'] | //h2[@id='tdo_31']");
        if ($titleNodes->length > 0) {
            $data['title'] = trim($titleNodes->item(0)->textContent);
        }

        // Cena (td ar id "tdo_8")
        $priceNodes = $xpath->query("//td[@id='tdo_8']");
        if ($priceNodes->length > 0) {
            $priceText = trim($priceNodes->item(0)->textContent);
            // Ekstrakt tikai ciparus
            preg_match('/[\d\s]+/', $priceText, $matches);
            if (!empty($matches)) {
                $data['price'] = floatval(str_replace(' ', '', $matches[0]));
            }
        }

        // Apraksts (div ar id "msg_div_msg")
        $descNodes = $xpath->query("//div[@id='msg_div_msg']");
        if ($descNodes->length > 0) {
            $data['description'] = trim($descNodes->item(0)->textContent);
        }

        // Attēli (a ar class "pic" iekš href)
        $imageNodes = $xpath->query("//a[contains(@class, 'pic')]");
        foreach ($imageNodes as $imageNode) {
            $href = $imageNode->getAttribute('href');
            if (!empty($href) && filter_var($href, FILTER_VALIDATE_URL)) {
                $data['images'][] = $href;
            }
        }

        // Ja nav attēlu, meklēt img ar id "bigpic"
        if (empty($data['images'])) {
            $bigpicNodes = $xpath->query("//img[@id='bigpic']");
            if ($bigpicNodes->length > 0) {
                $src = $bigpicNodes->item(0)->getAttribute('src');
                if (!empty($src)) {
                    // Ja relatīvs URL, pievienot domēnu
                    if (!filter_var($src, FILTER_VALIDATE_URL)) {
                        $src = 'https://www.ss.lv' . $src;
                    }
                    $data['images'][] = $src;
                }
            }
        }

        // Defaults
        if (empty($data['title'])) {
            $data['title'] = 'Importēts produkts';
        }

        if (!isset($data['price'])) {
            $data['price'] = 0;
        }

        if (empty($data['description'])) {
            $data['description'] = 'Importēts no ' . $url;
        }

        $data['type'] = 'product';
        $data['stock_quantity'] = 1;
        $data['is_active'] = 1;

        return $data;
    }

    /**
     * Atcelt importēšanu
     */
    public function cancel() {
        Session::remove('import_preview');
        redirect('/import');
    }
}

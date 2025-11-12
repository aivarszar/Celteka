# 🏪 Vietējais Tirgus - Local Marketplace Platform

Universāla, eleganta un responsīva web platforma, kas nodrošina elastīgu tiešās tirdzniecības un pakalpojumu sniegšanas vidi starp vietējiem ražotājiem/pakalpojumu sniedzējiem un pircējiem/klientiem.

## 📋 Satura Rādītājs

- [Īpašības](#-īpašības)
- [Tehnoloģijas](#-tehnoloģijas)
- [Prasības](#-prasības)
- [Instalācija](#-instalācija)
- [Konfigurācija](#-konfigurācija)
- [Lietošana](#-lietošana)
- [Projekta Struktūra](#-projekta-struktūra)
- [Drošība](#-drošība)
- [Turpmākā Attīstība](#-turpmākā-attīstība)

## ✨ Īpašības

### 🛠️ Automātiska Instalācija
- **Servera Pārbaude**: Automātiski pārbauda PHP versiju un nepieciešamās paplašinājumus
- **Datubāzes Iestatīšana**: Vienkārša datubāzes konfigurācija ar automātisku tabulu izveidi
- **Kļūdu Ziņošana**: Skaidri un saprotami kļūdu ziņojumi

### 👤 Lietotāju Pārvaldība
- **Trīs Galvenās Lomas**: Pircējs, Ražotājs/Pārdevējs, Administrators
- **Dinamiskais Profils**: Profila dati tiek dinamiski pievienoti un saglabāti
- **Divpusēja Autentifikācija**: Drošs login/register process

### 🛒 Produkti un Pakalpojumi
- **Plaša Klāsta Atbalsts**: Standarta preces, pakalpojumi un unikāli pakalpojumi
- **Kategoriju Sistēma**: Elastīga produktu kategoriju hierarhija
- **Attēlu Pārvaldība**: Vairāku attēlu augšupielāde produktiem
- **Dinamiskā Meta**: Pielāgoti lauki katram produktam

### 📦 Pasūtījumu Sistēma
- **Kombinētā Piegāde**:
  - Tiešā paņemšana
  - Ražotāja piegāde
  - Centralizēta maršrutēta piegāde
- **Pasūtījumu Statusi**: Pilns dzīves cikls no izveidošanas līdz pabeigšanai
- **Maksājumu Norādes**: Informācija par tiešo norēķinu ar pārdevēju

### ⭐ Divpusējā Atsauksmju Sistēma
- Klienti vērtē ražotājus/pārdevējus
- Ražotāji/pārdevēji vērtē klientus
- Vērtējumu aprēķini un statistika

### 🌍 Lokalizācija
- **Latvijas Teritoriālais Iedalījums**: Reģioni un pilsētas
- **Paplašināma**: Viegli pievienot jaunas valstis/reģionus
- **Atdalīta Tulkošana**: Visi UI teksti ārējos failos
- **Vairāku Valodu Atbalsts**: Gatavs paplašināšanai

### 🎨 Moderns Dizains
- **Responsīvs**: Optimizēts visām ierīcēm (desktop, tablet, mobile)
- **Elegants**: Mūsdienīgs gradient dizains
- **Ātrs**: Optimizēts ielādes ātrums
- **Piekļūstams**: W3C standartu ievērošana

## 🔧 Tehnoloģijas

- **Backend**: PHP 7.4+ (bez framework, tīrs PHP)
- **Database**: MySQL 5.7+ / MariaDB 10.2+
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Architecture**: MVC Pattern
- **Security**:
  - PDO Prepared Statements
  - CSRF Protection
  - Password Hashing (bcrypt)
  - Session Management
  - XSS Protection

## 📋 Prasības

### Minimālās Prasības
- **PHP**: 7.4 vai jaunāks
- **MySQL**: 5.7+ vai MariaDB 10.2+
- **Apache**: 2.4+ ar mod_rewrite
- **PHP Paplašinājumi**:
  - PDO
  - pdo_mysql
  - mysqli
  - json
  - mbstring
  - curl

### Ieteicamās Prasības
- **PHP**: 8.0+
- **MySQL**: 8.0+
- **SSL Certificate**: HTTPS savienojumam
- **Memory**: 128MB+
- **Disk Space**: 100MB+

## 🚀 Instalācija

### 1. Lejupielādēt Projektu

```bash
git clone https://github.com/yourusername/marketplace.git
cd marketplace
```

### 2. Iestatīt Tiesības

```bash
chmod -R 755 .
chmod -R 775 app/config
chmod -R 775 assets/images
```

### 3. Apache Konfigurācija

**Option A: Root Domain**

Iestatīt Apache virtual host:

```apache
<VirtualHost *:80>
    ServerName marketplace.local
    DocumentRoot /path/to/marketplace/public

    <Directory /path/to/marketplace/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/marketplace-error.log
    CustomLog ${APACHE_LOG_DIR}/marketplace-access.log combined
</VirtualHost>
```

**Option B: Subdirectory**

Vienkārši pārkopējiet projektu uz jūsu web servera direktoriju.

### 4. Palaist Instalācijas Vedni

Atveriet pārlūkprogrammā:
```
http://jūsu-domēns/install/
```

Instalācijas vednis:
1. ✅ Pārbaudīs servera vidi
2. 📝 Ievadīsiet datubāzes parametrus
3. 🎉 Automātiski izveidosies tabulas un pamata dati

### 5. Drošība Pēc Instalācijas

**SVARĪGI!** Pēc instalācijas:

```bash
# 1. Izdzēsiet vai pārsauciet install direktoriju
rm -rf install/
# vai
mv install/ install.bak/

# 2. Iestatiet production mode
# Rediģējiet app/config/config.php:
'debug' => false,

# 3. Iestatiet drošas sesiju cookies
'secure' => true,  // ja izmantojat HTTPS
```

## ⚙️ Konfigurācija

### Datubāzes Konfigurācija

Fails: `app/config/config.php`

```php
'database' => [
    'host' => 'localhost',
    'dbname' => 'marketplace',
    'username' => 'root',
    'password' => 'your_password',
    'charset' => 'utf8mb4',
],
```

### Aplikācijas Iestatījumi

```php
'app' => [
    'name' => 'Vietējais Tirgus',
    'url' => 'http://marketplace.local',
    'timezone' => 'Europe/Riga',
    'locale' => 'lv',
    'debug' => false,  // PRODUCTION: false
],
```

### Augšupielāžu Iestatījumi

```php
'upload' => [
    'max_size' => 5242880,  // 5MB
    'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    'path' => '/assets/images/uploads/',
],
```

## 📚 Lietošana

### Lietotāju Lomas

#### 1. **Pircējs** (Buyer)
- Pārlūkot produktus un pakalpojumus
- Veikt pasūtījumus
- Atstāt atsauksmes par pārdevējiem
- Pārvaldīt savu profilu

#### 2. **Ražotājs/Pārdevējs** (Seller)
- Pievienot un rediģēt produktus/pakalpojumus
- Pārvaldīt pasūtījumus
- Atstāt atsauksmes par pircējiem
- Skatīt statistiku

#### 3. **Administrators** (Admin)
- Pārvaldīt visus lietotājus
- Moderēt saturu
- Pārvaldīt kategorijas
- Skatīt sistēmas statistiku

### Pirmais Administrators

Pēc instalācijas izveidojiet administratora kontu tieši datubāzē:

```sql
-- 1. Izveidot lietotāju
INSERT INTO users (email, password_hash, full_name, role_id, is_active)
VALUES (
    'admin@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: "password"
    'Admin',
    3,
    1
);
```

### API Endpoints

Platformai ir vairāki API endpoints:

```
GET  /api/locations/{type}  - Iegūt lokācijas pēc tipa
GET  /api/categories        - Iegūt visas kategorijas
```

## 📁 Projekta Struktūra

```
marketplace/
├── app/
│   ├── config/
│   │   ├── config.php          # Galvenā konfigurācija
│   │   └── routes.php          # Maršrutu definīcijas
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── ProductController.php
│   │   ├── SellerController.php
│   │   └── ...
│   ├── core/
│   │   ├── App.php             # Galvenā aplikācijas klase
│   │   ├── Database.php        # Datubāzes klase
│   │   ├── Router.php          # Maršrutēšanas klase
│   │   ├── Session.php         # Sesiju pārvaldība
│   │   └── Lang.php            # Lokalizācija
│   ├── models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── Review.php
│   ├── views/
│   │   ├── layout.php          # Galvenais šablons
│   │   ├── home.php
│   │   ├── auth/
│   │   ├── products/
│   │   ├── seller/
│   │   └── errors/
│   └── helpers/
│       └── AuthHelper.php
├── assets/
│   ├── css/
│   │   └── style.css           # Galvenais stils
│   ├── js/
│   │   └── main.js             # JavaScript funkcionalitāte
│   └── images/
├── database/
│   ├── schema.sql              # Datubāzes struktūra
│   └── locations_lv.sql        # Latvijas lokācijas
├── lang/
│   └── lv/
│       └── messages.php        # Latviešu tulkojumi
├── public/
│   ├── .htaccess
│   └── index.php               # Ievades punkts
├── install/
│   └── index.php               # Instalācijas vednis
├── .htaccess
└── README.md
```

## 🔒 Drošība

### Implementētie Drošības Pasākumi

1. **SQL Injection Prevention**
   - PDO Prepared Statements
   - Parametrizēti vaicājumi

2. **XSS Protection**
   - HTML entity encoding
   - Output escaping (e() funkcija)

3. **CSRF Protection**
   - Token ģenerēšana katrā sesijā
   - Validācija visos POST foršos

4. **Password Security**
   - Bcrypt hashing
   - Minimālā paroles garuma prasība

5. **Session Security**
   - HTTP Only cookies
   - Session regeneration pēc login
   - Secure cookies (ar HTTPS)

6. **Access Control**
   - Role-based authorization
   - Permission checks
   - Route protection

### Drošības Ieteikumi

```bash
# 1. Iestatīt stingras failu tiesības
chmod 644 app/config/config.php
chmod 755 public/

# 2. Atspējot direktoriju pārlūkošanu
# (jau iekļauts .htaccess)

# 3. Regulāri atjaunināt PHP
sudo apt update && sudo apt upgrade php

# 4. Izmantot HTTPS
# Iegūstiet SSL sertifikātu (Let's Encrypt)
```

## 🔄 Turpmākā Attīstība

### Plānotās Funkcijas

- [ ] **Maksājumu Integrācija**: Stripe, PayPal atbalsts
- [ ] **E-pasta Paziņojumi**: SMTP konfigurācija
- [ ] **Attēlu Optimizācija**: Automātiska resize un kompresija
- [ ] **Meklēšanas Uzlabojumi**: Full-text search ar filters
- [ ] **Administratora Panelis**: Pilnvērtīgs admin dashboard
- [ ] **API Autentifikācija**: JWT tokens API
- [ ] **Produktu Importēšana**: CSV/Excel imports
- [ ] **Paziņojumu Sistēma**: Real-time notifications
- [ ] **Čata Funkcionalitāte**: Tiešā saziņa starp lietotājiem
- [ ] **Vairāku Valodu Atbalsts**: Angļu, krievu valodas

### Pielāgošana

#### Pievienot Jaunu Valodu

1. Izveidojiet jaunu direktoriju: `lang/en/`
2. Kopējiet `lang/lv/messages.php` uz `lang/en/messages.php`
3. Tulkojiet visas vērtības
4. Mainiet `config.php`: `'locale' => 'en'`

#### Pievienot Jaunu Kategoriju

```sql
INSERT INTO categories (name, slug, description, icon)
VALUES ('Jūsu kategorija', 'jusu-kategorija', 'Apraksts', 'icon-name');
```

#### Pievienot Jaunus Dinamiskos Laukus

Izmantojiet meta tabulas:

```php
// Lietotāja meta
$userModel->setMeta($userId, 'automašīnas_modelis', 'Toyota Prius');

// Produkta meta
$productModel->setMeta($productId, 'tilpums', '500ml');
```

## 🤝 Atbalsts

### Dokumentācija

Pilna dokumentācija pieejama projekta wiki.

### Kļūdu Ziņošana

Ja atradāt kļūdu, lūdzu izveidojiet GitHub issue ar:
- Kļūdas aprakstu
- Soļus, kā to reproducēt
- Paredzamo rezultātu
- Faktisko rezultātu
- Sistēmas informāciju (PHP versija, OS, utt.)

### Kontakti

- **E-pasts**: info@vietejaistirgus.lv
- **GitHub**: https://github.com/yourusername/marketplace

## 📄 Licence

Šis projekts ir licencēts ar MIT licenci. Skatīt [LICENSE](LICENSE) failu detaļām.

## 🙏 Pateicības

Paldies visiem, kas piedalījās šī projekta izstrādē!

---

**Izveidots ar ❤️ Latvijai**

# 🚀 VIENKĀRŠA INSTALĀCIJA (BEZ .htaccess)

Ja jums ir problēmas ar .htaccess failiem vai hosting redirect problēmas, izmantojiet šo vienkāršo metodi.

## 📦 1. Augšupielādēt failus

Augšupielādējiet VISUS failus uz serveri (ar FTP, cPanel File Manager, vai Git).

Struktūrai vajadzētu izskatīties:
```
/public_html/  (vai /www/, vai /httpdocs/)
├── setup.php           ← GALVENAIS instalācijas fails
├── app/
├── assets/
├── database/
├── lang/
└── public/
```

## 🔧 2. Palaist instalāciju

Atveriet pārlūkprogrammā:
```
http://celteka.lv/setup.php
```

**JA setup.php redirect uz GitHub**, tad:

### Risinājums A: Atspējot redirect hosting panelī

1. Ieejiet cPanel / Plesk / Hosting panelī
2. Meklējiet "Redirects" vai "Domains" sadaļu
3. Izdzēsiet jebkuru redirect uz GitHub

### Risinājums B: Pārsaukt setup.php

Ja viss cits nedarbojas, mēģiniet pārsaukt:
```
setup.php → install123.php
```

Tad atveriet:
```
http://celteka.lv/install123.php
```

### Risinājums C: Tiešā instalācija ar phpMyAdmin

Ja nekas cits nedarbojas:

1. **Izveidojiet datubāzi** phpMyAdmin:
   - Nosaukums: `marketplace` (vai jebkurš cits)
   - Collation: `utf8mb4_unicode_ci`

2. **Importējiet SQL failus**:
   - Atveriet `database/schema.sql` un importējiet
   - Atveriet `database/locations_lv.sql` un importējiet
   - Atveriet `database/categories.sql` un importējiet

3. **Izveidojiet config failu manuāli**:

   Izveidojiet failu: `app/config/config.php`

   ```php
   <?php
   return [
       'database' => [
           'host' => 'localhost',
           'dbname' => 'marketplace',     // JŪSU datubāzes nosaukums
           'username' => 'root',          // JŪSU lietotājvārds
           'password' => 'jūsu_parole',   // JŪSU parole
           'charset' => 'utf8mb4',
       ],
       'app' => [
           'name' => 'Vietējais Tirgus',
           'url' => 'http://celteka.lv',  // JŪSU domēns
           'timezone' => 'Europe/Riga',
           'locale' => 'lv',
           'debug' => false,
       ],
       'session' => [
           'name' => 'marketplace_session',
           'lifetime' => 86400,
           'secure' => false,
           'httponly' => true,
       ],
       'upload' => [
           'max_size' => 5242880,
           'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
           'path' => '/assets/images/uploads/',
       ],
   ];
   ```

4. **Izdzēsiet test failus**:
   ```
   setup.php
   test-root.php
   install/test.php
   public/test.php
   ```

5. **Atveriet platformu**:
   ```
   http://celteka.lv/public/index.php
   ```

## ✅ 3. Pēc instalācijas

1. **Reģistrējiet pirmo lietotāju**:
   ```
   http://celteka.lv/public/index.php
   ```

   Noklikšķiniet "Reģistrēties" un izveidojiet kontu.

2. **Padariet to par administratoru** (phpMyAdmin):
   ```sql
   UPDATE users SET role_id = 3 WHERE email = 'jūsu@epasts.lv';
   ```

   Role ID:
   - 1 = Pircējs
   - 2 = Ražotājs/Pārdevējs
   - 3 = Administrators

3. **Drošība**:
   - Izdzēsiet `setup.php`
   - Izdzēsiet visus `test-*.php` failus
   - Iestatiet `'debug' => false` config failā

## 🆘 Problēmu risināšana

### Problēma: Tukša lapa
**Risinājums**: Ieslēdziet PHP kļūdu ziņošanu:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// ... pārējais kods
```

### Problēma: 500 Internal Server Error
**Risinājums**:
1. Pārbaudiet Apache error log
2. Pārbaudiet PHP versiju (vajag 7.4+)
3. Pārbaudiet failu tiesības (chmod 755)

### Problēma: Database connection error
**Risinājums**:
1. Pārbaudiet MySQL/MariaDB darbību
2. Pārbaudiet datubāzes credentials
3. Pārbaudiet, vai lietotājam ir tiesības

### Problēma: Vēl joprojām redirect uz GitHub
**Risinājums**:
1. Pārbaudiet cPanel/Plesk redirects
2. Pārbaudiet .htaccess failus augstākā līmenī
3. Sazinieties ar hosting atbalstu

## 📞 Palīdzība

Ja neviens no šiem risinājumiem nepalīdz, sazinieties ar hosting atbalstu un jautājiet:

1. "Kāpēc visi faili redirect uz GitHub?"
2. "Vai ir globāls redirect iestatīts?"
3. "Kā atspējot redirect manā kontā?"

---

**Gatavs!** Kad instalācija pabeigta, varat sākt lietot platformu! 🎉

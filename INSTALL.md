# 🚀 Vienkārša Instalācija (BEZ .htaccess)

Pilnīgi vienkārša instalācija - **NAV VAJADZĪGI .htaccess vai mod_rewrite!**

## 📦 1. Augšupielādēt failus

Augšupielādējiet VISUS projekta failus uz serveri (ar FTP, Git, vai cPanel File Manager).

```
/public_html/       (vai /www/, /httpdocs/ - jūsu hosting root)
├── index.php       ← Galvenais fails
├── setup.php       ← Instalācijas vednis
├── app/
├── assets/
├── database/
├── lang/           ← 5 valodas: lv, en, ru, lt, ee
└── ...
```

## 🔧 2. Palaist instalāciju

Atveriet pārlūkprogrammā:

```
http://jūsu-domēns.lv/setup.php
```

Instalācijas vednis:
1. ✅ Pārbaudīs servera vidi (PHP, paplašinājumus)
2. 📝 Ievadīsiet datubāzes parametrus
3. 🎉 Automātiski izveidosies tabulas un dati

## 🌍 3. Atvērt platformu

Pēc instalācijas:

```
http://jūsu-domēns.lv/
```
vai
```
http://jūsu-domēns.lv/index.php
```

## 🎨 4. Valodas

Pieejamas 5 valodas ar valodas izvēlni headerī:
- 🇱🇻 **LV** - Latviešu (noklusējums)
- 🇬🇧 **EN** - English (fallback)
- 🇷🇺 **RU** - Русский
- 🇱🇹 **LT** - Lietuvių
- 🇪🇪 **EE** - Eesti

Valoda saglabājas sesijā. Lietotāji var mainīt ar pogām headerī.

## ⚙️ 5. Pirmais lietotājs

1. **Reģistrējieties** caur `/register`
2. **Padariet par Admin** (phpMyAdmin):
   ```sql
   UPDATE users SET role_id = 3 WHERE email = 'jūsu@epasts.lv';
   ```

Role ID:
- `1` = Pircējs
- `2` = Ražotājs/Pārdevējs
- `3` = Administrators

## 🔒 6. Drošība pēc instalācijas

```bash
# 1. Izdzēsiet instalācijas failu
rm setup.php

# 2. Izdzēsiet test failus
rm test-root.php
rm install/test.php
rm public/test.php

# 3. Iestatiet production mode
# app/config/config.php:
'debug' => false,
```

## 📁 7. Konfigurācija

Config fails: `app/config/config.php`

```php
return [
    'app' => [
        'locale' => 'lv',  // Noklusējuma valoda: lv, en, ru, lt, ee
        'debug' => false,   // Production: false
    ],
    // ...
];
```

## ✨ Īpašības

### ✅ Bez .htaccess
- Nav vajadzīgs mod_rewrite
- Darbojas uz jebkura hostinga
- Vienkārša URL struktūra

### 🌍 5 Valodas
- Automātisks fallback uz EN
- Ja nav tulkojuma - rāda atslēgu
- Viegli pievienot jaunas valodas

### 🎨 Moderns dizains
- Responsīvs (mobile, tablet, desktop)
- Violets gradient dizains
- Valodas izvēlne headerī

### 🔒 Drošība
- PDO prepared statements
- CSRF aizsardzība
- Password hashing (bcrypt)
- XSS aizsardzība
- Session security

## 🆘 Problēmu risināšana

### Problēma: Tukša lapa
```php
// Pievienojiet index.php sākumā:
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Problēma: CSS netiek ielādēts
Pārbaudiet, vai `assets/` direktorija ir augšupielādēta un pieejama.

### Problēma: 500 kļūda
1. Pārbaudiet PHP versiju (vajag 7.4+)
2. Pārbaudiet error_log
3. Pārbaudiet failu tiesības (755)

### Problēma: Database connection error
1. Pārbaudiet datubāzes credentials `app/config/config.php`
2. Pārbaudiet MySQL/MariaDB darbību
3. Pārbaudiet lietotāja tiesības

## 📚 URL Struktūra

```
/                    → Sākumlapa
/products            → Produktu saraksts
/product/{slug}      → Produkta detaļas
/login               → Ielogošanās
/register            → Reģistrācija
/seller/products     → Pārdevēja produkti
/lang/lv             → Mainīt valodu uz LV
/lang/en             → Mainīt valodu uz EN
...
```

## 🎯 Nākamie soļi

1. ✅ Instalējiet platformu
2. ✅ Izveidojiet admin kontu
3. ✅ Pievienojiet kategorijas
4. ✅ Pievienojiet produktus
5. ✅ Testējiet reģistrāciju/login
6. ✅ Testējiet pasūtījumus
7. ✅ Izdzēsiet setup.php

---

**Gatavs!** Platforma ir 100% standalone un gatava ražošanai! 🎉

**Nav vajadzīgi:**
- ❌ .htaccess faili
- ❌ mod_rewrite
- ❌ Sarežģīta Apache konfigurācija
- ❌ public/ redirect

**Vienkārši augšupielādējiet, palaidiet setup.php, un sāciet lietot!**

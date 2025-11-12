<?php
/**
 * Maršrutu Definīcijas
 * Visi aplikācijas URL maršruti
 */

$router = app()->getRouter();

// Valodas maiņa
$router->get('/lang/{locale}', 'LanguageController@change', 'language.change');

// Sākumlapa
$router->get('/', 'HomeController@index', 'home');

// Autentifikācija
$router->get('/login', 'AuthController@showLogin', 'login');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister', 'register');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout', 'logout');

// Produkti
$router->get('/products', 'ProductController@index', 'products');
$router->get('/products/category/{slug}', 'ProductController@category', 'products.category');
$router->get('/product/{slug}', 'ProductController@show', 'product.show');
$router->get('/search', 'ProductController@search', 'search');

// Pārdevēja funkcijas (jābūt autentificētam)
$router->get('/seller/products', 'SellerController@products', 'seller.products');
$router->get('/seller/products/create', 'SellerController@createProduct', 'seller.products.create');
$router->post('/seller/products', 'SellerController@storeProduct');
$router->get('/seller/products/{id}/edit', 'SellerController@editProduct', 'seller.products.edit');
$router->post('/seller/products/{id}', 'SellerController@updateProduct');
$router->post('/seller/products/{id}/delete', 'SellerController@deleteProduct');
$router->get('/seller/orders', 'SellerController@orders', 'seller.orders');

// Pircēja funkcijas
$router->get('/cart', 'CartController@index', 'cart');
$router->post('/cart/add/{id}', 'CartController@add', 'cart.add');
$router->post('/cart/update/{id}', 'CartController@update');
$router->post('/cart/remove/{id}', 'CartController@remove');
$router->get('/checkout', 'CartController@checkout', 'checkout');
$router->post('/checkout', 'CartController@placeOrder');

// Pasūtījumi
$router->get('/orders', 'OrderController@index', 'orders');
$router->get('/order/{id}', 'OrderController@show', 'order.show');
$router->post('/order/{id}/cancel', 'OrderController@cancel');

// Atsauksmes
$router->post('/review/order/{id}', 'ReviewController@store', 'review.store');

// Profils
$router->get('/profile', 'ProfileController@index', 'profile');
$router->get('/profile/edit', 'ProfileController@edit', 'profile.edit');
$router->post('/profile/update', 'ProfileController@update', 'profile.update');
$router->get('/profile/change-password', 'ProfileController@changePassword', 'profile.password');
$router->post('/profile/update-password', 'ProfileController@updatePassword', 'profile.password.update');

// Administratora funkcijas
$router->get('/admin', 'AdminController@index', 'admin');
$router->get('/admin/users', 'AdminController@users', 'admin.users');
$router->get('/admin/products', 'AdminController@products', 'admin.products');
$router->get('/admin/orders', 'AdminController@orders', 'admin.orders');
$router->get('/admin/categories', 'AdminController@categories', 'admin.categories');
$router->post('/admin/categories', 'AdminController@storeCategory');
$router->get('/admin/settings', 'AdminController@settings', 'admin.settings');
$router->post('/admin/settings', 'AdminController@saveSettings');

// Landing pages (tiešās saites)
$router->get('/l/{slug}', 'LandingController@show', 'landing');

// API endpoints (ja nepieciešams)
$router->get('/api/locations/{type}', 'ApiController@locations');
$router->get('/api/categories', 'ApiController@categories');

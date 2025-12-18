<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', function () {
  return redirect()->to('/signin');
});

$routes->get('signin', 'AuthController::signin');
$routes->post('signin/store', 'AuthController::signinStore');
$routes->get('logout', 'AuthController::logout');
$routes->match(['options'], '(:any)', 'CorsController::handleOptions');
$routes->get('/dashboard', 'DashboardController::index', ['filter' => 'authGuard']);

/** ================================= 
 *             ENDPOINT
 * ================================== */
$routes->group('api', ['filter' => 'cors'], function ($routes) {
  // AUTH
  $routes->group('auth', function ($routes) {
    $routes->post('login', 'AuthController::login');
    $routes->post('register', 'AuthController::register');
  });

  // PRODUCTS
  $routes->group('products', function ($routes) {
    $routes->get('/', 'ProductController::apiProduct');
    $routes->get('new-eyewear', 'ProductController::apiListNewEyewear');
    $routes->get('recommendations', 'ProductController::apiProductRecommendations');
    $routes->get('(:any)', 'ProductController::apiProductDetail/$1');
    $routes->get('category', 'ProductCategoryController::apiListProductCategory');
  });

  // PRODUCT VARIANTS
  $routes->group('variants', function ($routes) {
    $routes->get('', 'ProductVariantController::getByProductId');
  });

  // CART
  $routes->group('cart', ['filter' => 'authApi'], function ($routes) {
    $routes->post('add-to-cart', 'CartController::addToCart');
    $routes->get('', 'CartController::getCart');
    $routes->get('total-cart', 'CartController::getTotalCart');
    $routes->delete('delete/(:any)', 'CartController::deleteCartItem/$1');
  });

  // ORDER
  $routes->group('orders', ['filter' => 'authApi'], function ($routes) {
    $routes->get('', 'OrderController::orders',  ['filter' => 'authApi']);
    $routes->post('checkout', 'OrderController::checkout');
    $routes->post('payment', 'OrderController::uploadPaymentProof');
    $routes->get('check-status', 'OrderController::checkIfPaid');
  });
});


/** ================================= 
 *          WEB DASHBOARD
 * ================================== */
$routes->group('products', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('/', 'ProductController::webIndex');
  $routes->get('form', 'ProductController::form');
  $routes->post('save', 'ProductController::save');
  $routes->post('delete/(:any)', 'ProductController::webDelete/$1');
  $routes->post('delete-image', 'ProductController::deleteImage');
});

$routes->group('product-category', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('/', 'ProductCategoryController::webIndex');
  $routes->get('form', 'ProductCategoryController::form');
  $routes->post('save', 'ProductCategoryController::save');
  $routes->post('delete/(:any)', 'ProductCategoryController::webDelete/$1');
});

$routes->group('product-attribute', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('/', 'ProductAttributeController::webIndex');
  $routes->get('form', 'ProductAttributeController::form');
  $routes->post('save', 'ProductAttributeController::save');
  $routes->post('delete/(:any)', 'ProductAttributeController::webDelete/$1');
});

$routes->group('inventory', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('', 'InventoryTransactionsController::webIndex');
  $routes->get('form', 'InventoryTransactionsController::form');
  $routes->post('save', 'InventoryTransactionsController::save');
  $routes->post('delete/(:any)', 'InventoryTransactionsController::delete/$1');
});

$routes->group('customers', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('', 'CustomerController::index');
  $routes->get('form', 'CustomerController::form');
  $routes->post('save', 'CustomerController::save');
  $routes->post('delete/(:any)', 'CustomerController::delete/$1');
});

$routes->group('eye-examinations', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('', 'EyeExaminationController::index');
  $routes->get('form', 'EyeExaminationController::form');
  $routes->post('save', 'EyeExaminationController::save');
  $routes->post('delete/(:any)', 'EyeExaminationController::delete/$1');
});

$routes->group('users', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('', 'UserController::index');
  $routes->get('form', 'UserController::form');
  $routes->post('save', 'UserController::save');
  $routes->post('delete/(:any)', 'UserController::delete/$1');
});

$routes->group('in-store-sales', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('', 'OrderController::index');
  $routes->get('form', 'OrderController::form');
  $routes->post('save', 'OrderController::save');
  $routes->post('delete/(:any)', 'OrderController::delete/$1');
});

$routes->group('online-sales', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('', 'OrderController::index');
  $routes->get('form', 'OrderController::form');
  $routes->post('save', 'OrderController::save');
  $routes->post('delete/(:any)', 'OrderController::delete/$1');
});

$routes->group('roles', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('', 'RoleController::index');
  $routes->get('form', 'RoleController::form');
  $routes->post('save', 'RoleController::save');
  $routes->post('delete/(:any)', 'RoleController::delete/$1');
});

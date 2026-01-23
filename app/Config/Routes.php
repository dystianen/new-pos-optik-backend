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
$routes->get('/dashboard', 'DashboardController::index', ['filter' => 'authGuard']);

/** ================================= 
 *             ENDPOINT
 * ================================== */
$routes->group('api', ['filter' => 'cors'], function ($routes) {
  $routes->options('(:any)', function () {
    return service('response')->setStatusCode(200);
  });

  // AUTH
  $routes->group('auth', function ($routes) {
    $routes->post('login', 'Api\AuthApiController::login');
    $routes->post('register', 'Api\AuthApiController::register');
    $routes->post('refresh', 'Api\AuthApiController::refresh');
  });

  // PRODUCTS
  $routes->group('products', function ($routes) {
    $routes->get('', 'Api\ProductApiController::apiProduct');
    $routes->get('new-eyewear', 'Api\ProductApiController::apiListNewEyewear');
    $routes->get('best-seller', 'Api\ProductApiController::apiListBestSeller');
    $routes->get('categories', 'Api\ProductCategoryApiController::apiListProductCategory');
    $routes->get('recommendations/(:segment)', 'Api\ProductApiController::apiProductRecommendations/$1');
    $routes->get('search', 'Api\ProductApiController::apiSearchProduct');
    $routes->get('(:segment)', 'Api\ProductApiController::apiProductDetail/$1');
    $routes->get('(:segment)/attributes', 'Api\ProductApiController::apiProductAttributes/$1');
  });

  // PRODUCT VARIANTS
  $routes->group('variants', function ($routes) {
    $routes->get('', 'Api\ProductVariantApiController::getByProductId');
  });

  // CART
  $routes->group('cart', ['filter' => 'authApi'], function ($routes) {
    $routes->get('', 'Api\CartApiController::listCart');
    $routes->post('add-to-cart', 'Api\CartApiController::addToCart');
    $routes->get('total-cart', 'Api\CartApiController::getTotalCart');
    $routes->delete('delete/(:any)', 'Api\CartApiController::deleteCartItem/$1');
  });

  // ORDER
  $routes->group('orders', ['filter' => 'authApi'], function ($routes) {
    $routes->get('', 'Api\OnlineSalesApiController::listOrders');
    $routes->get('summary/(:segment)', 'Api\OnlineSalesApiController::summaryOrders/$1');
    $routes->post('submit/(:segment)', 'Api\OnlineSalesApiController::submitOrder/$1');
    $routes->post('payment', 'Api\OnlineSalesApiController::uploadPaymentProof');
    $routes->get('check-payment-status/(:segment)', 'Api\OnlineSalesApiController::checkPaymentStatus/$1');
    $routes->post('(:segment)/status', 'Api\OnlineSalesApiController::updateStatus/$1');
    $routes->get('(:segment)', 'Api\OnlineSalesApiController::getOrderDetail/$1');
  });

  // ONLINE SALES
  $routes->group('online-sales', ['filter' => 'authGuard'], function ($routes) {
    $routes->post('(:segment)/approve', 'OnlineSalesController::approvePayment/$1');
    $routes->post('(:segment)/reject', 'OnlineSalesController::rejectPayment/$1');
    $routes->post('(:segment)/status', 'OnlineSalesController::updateStatus/$1');
    $routes->post('(:segment)/ship', 'OnlineSalesController::shipOrder/$1');
  });


  // SHIPPING ADDRESS
  $routes->group('shipping', ['filter' => 'authApi'], function ($routes) {
    $routes->get('', 'Api\CustomerShippingAddressApiController::getAllShippingAddress');
    $routes->get('(:segment)', 'Api\CustomerShippingAddressApiController::getById/$1');
    $routes->post('save', 'Api\CustomerShippingAddressApiController::save');
  });

  // WISHLIST
  $routes->group('wishlist', ['filter' => 'authApi'], function ($routes) {
    $routes->get('', 'Api\WishlistApiController::index');
    $routes->post('toggle', 'Api\WishlistApiController::toggle');
    $routes->get('count', 'Api\WishlistApiController::count');
  });

  // REFUND ACCOUNTS
  $routes->group('refund-accounts', ['filter' => 'authApi'], function ($routes) {
    $routes->get('', 'Api\UserRefundAccountApiController::findOne');
    $routes->get('(:segment)', 'Api\UserRefundAccountApiController::getById/$1');
    $routes->post('save', 'Api\UserRefundAccountApiController::save');
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
  $routes->get('', 'InStoreSalesController::index');
  $routes->get('create', 'InStoreSalesController::create');
  $routes->post('store', 'InStoreSalesController::store');
  $routes->get('export', 'InStoreSalesController::export');

  $routes->get('success/(:segment)', 'InStoreSalesController::success/$1');
  $routes->get('print/(:segment)', 'InStoreSalesController::print/$1');
  $routes->get('(:segment)', 'InStoreSalesController::detail/$1');
});

$routes->group('online-sales', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('', 'OnlineSalesController::index');
  $routes->get('export', 'OnlineSalesController::export');
  $routes->get('(:segment)', 'OnlineSalesController::detail/$1');
});

$routes->group('roles', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('', 'RoleController::index');
  $routes->get('form', 'RoleController::form');
  $routes->post('save', 'RoleController::save');
  $routes->post('delete/(:any)', 'RoleController::delete/$1');
});

$routes->group('notifications', ['filter' => 'authGuard'], function ($routes) {
  $routes->get('', 'NotificationController::getAllNotifications');
  $routes->get('unread', 'NotificationController::getUnreadNotifications');
  $routes->post('read-all', 'NotificationController::markAllRead');
  $routes->post('read/(:segment)', 'NotificationController::markRead/$1');
});

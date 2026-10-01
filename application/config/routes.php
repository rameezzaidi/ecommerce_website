<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/* ---------------- Storefront ---------------- */
$route['shop']                  = 'shop/index';
$route['shop/(:any)']           = 'shop/index/$1';
$route['product/(:any)']        = 'product/view/$1';
$route['cart']                  = 'cart/index';
$route['checkout']              = 'checkout/index';
$route['checkout/success/(:any)'] = 'checkout/success/$1';
$route['customize']             = 'customizer/index';
$route['demand']                = 'demand/index';
$route['track-order']           = 'track/index';
$route['wishlist']              = 'wishlist/index';
$route['contact']               = 'pages/contact';
$route['page/(:any)']           = 'pages/view/$1';
$route['account']               = 'account/index';

/* ---------------- Admin ---------------- */
$route['admin']                 = 'admin/dashboard';
$route['admin/login']           = 'admin/auth/login';
$route['admin/logout']          = 'admin/auth/logout';
$route['admin/(categories|banners|coupons|pages|reviews|messages|subscribers|customers|products)']                  = 'admin/$1';
$route['admin/(categories|banners|coupons|pages|reviews|messages|subscribers|customers|products)/add']              = 'admin/$1/form';
$route['admin/(categories|banners|coupons|pages|reviews|messages|subscribers|customers|products)/edit/(:num)']      = 'admin/$1/form/$2';
$route['admin/(categories|banners|coupons|pages|reviews|messages|subscribers|customers|products)/save']             = 'admin/$1/save';
$route['admin/(categories|banners|coupons|pages|reviews|messages|subscribers|customers|products)/save/(:num)']      = 'admin/$1/save/$2';
$route['admin/(categories|banners|coupons|pages|reviews|messages|subscribers|customers|products)/delete/(:num)']    = 'admin/$1/delete/$2';
$route['admin/(categories|banners|coupons|pages|reviews|messages|subscribers|customers|products)/toggle/(:num)/(:any)'] = 'admin/$1/toggle/$2/$3';
$route['admin/custom-parts']             = 'admin/customparts';
$route['admin/custom-parts/add']         = 'admin/customparts/form';
$route['admin/custom-parts/edit/(:num)'] = 'admin/customparts/form/$1';
$route['admin/custom-parts/save']        = 'admin/customparts/save';
$route['admin/custom-parts/save/(:num)'] = 'admin/customparts/save/$1';
$route['admin/custom-parts/delete/(:num)'] = 'admin/customparts/delete/$1';
$route['admin/custom-parts/toggle/(:num)/(:any)'] = 'admin/customparts/toggle/$1/$2';
$route['admin/tracking-links']  = 'admin/analytics/links';

<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// ─── Public Routes ───────────────────────────────────────
$routes->get('/', 'Auth::index');
$routes->get('auth/login', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');

// ─── Admin Routes ─────────────────────────────────────────
$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // User Management
    $routes->get('users', 'UserManagement::index');
    $routes->get('users/create', 'UserManagement::create');
    $routes->post('users/store', 'UserManagement::store');
    $routes->get('users/edit/(:segment)', 'UserManagement::edit/$1');
    $routes->post('users/update/(:segment)', 'UserManagement::update/$1');
    $routes->post('users/toggle/(:segment)', 'UserManagement::toggle/$1');

    // Personel Management
    $routes->get('personel', 'Personel::index');
    $routes->get('personel/create', 'Personel::create');
    $routes->post('personel/store', 'Personel::store');
    $routes->get('personel/edit/(:segment)', 'Personel::edit/$1');
    $routes->post('personel/update/(:segment)', 'Personel::update/$1');
    $routes->get('personel/detail/(:segment)', 'Personel::detail/$1');
    $routes->post('personel/delete/(:segment)', 'Personel::delete/$1');

    $routes->get('personel/importExcel',  'Personel::importExcel');
    $routes->post('personel/prosesImport', 'Personel::prosesImport');
    $routes->get('personel/templateExcel', 'Personel::templateExcel');

    // Kendaraan Management
    $routes->get('kendaraan', 'Kendaraan::index');
    $routes->get('kendaraan/create', 'Kendaraan::create');
    $routes->post('kendaraan/store', 'Kendaraan::store');
    $routes->get('kendaraan/edit/(:segment)', 'Kendaraan::edit/$1');
    $routes->post('kendaraan/update/(:segment)', 'Kendaraan::update/$1');
    $routes->post('kendaraan/delete/(:segment)', 'Kendaraan::delete/$1');
    $routes->post('kendaraan/deletefoto/(:segment)', 'Kendaraan::deleteFoto/$1');

    $routes->get('kendaraan/cariPersonel', 'Kendaraan::cariPersonel');

    // QR Code
    $routes->get('qrcode', 'QRCode::index');
    $routes->post('qrcode/generate/(:segment)', 'QRCode::generate/$1');
    $routes->get('qrcode/print/(:segment)', 'QRCode::printCard/$1');
    $routes->post('qrcode/revoke/(:segment)', 'QRCode::revoke/$1');
    
    $routes->get('qrcode/download/(:segment)','QRCode::download/$1');
    $routes->get('qrcode/cariKendaraan', 'QRCode::cariKendaraan');

    // Laporan
    $routes->get('laporan', 'Laporan::index');
    $routes->get('laporan/export', 'Laporan::export');

    $routes->post('personel/toggle/(:segment)', 'Personel::toggle/$1');
    $routes->post('kendaraan/toggle/(:segment)', 'Kendaraan::toggle/$1');

    // ── Pangkat Management ────────────────────────────────
    $routes->get('pangkat',                    'Pangkat::index');
    $routes->get('pangkat/create',             'Pangkat::create');
    $routes->post('pangkat/store',             'Pangkat::store');
    $routes->get('pangkat/edit/(:segment)',    'Pangkat::edit/$1');
    $routes->post('pangkat/update/(:segment)', 'Pangkat::update/$1');
    $routes->post('pangkat/delete/(:segment)', 'Pangkat::delete/$1');

    // ── Satker Management ─────────────────────────────────
    $routes->get('satker',                    'Satker::index');
    $routes->get('satker/create',             'Satker::create');
    $routes->post('satker/store',             'Satker::store');
    $routes->get('satker/edit/(:segment)',    'Satker::edit/$1');
    $routes->post('satker/update/(:segment)', 'Satker::update/$1');
    $routes->post('satker/delete/(:segment)', 'Satker::delete/$1');
});

// ─── Petugas Routes ───────────────────────────────────────
$routes->group('petugas', ['filter' => 'role:petugas'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::petugasIndex');
    $routes->get('scan', 'Scan::index');
    $routes->post('scan/process', 'Scan::process');
    $routes->get('scan/monitor', 'Scan::monitor');
    $routes->get('scan/mobile', 'Scan::mobile');
    $routes->get('laporan', 'Laporan::petugasIndex');
});

// ── PWA Routes ────────────────────────────────────────
$routes->get('manifest.json',    'Pwa::manifest');
$routes->get('sw.js',            'Pwa::serviceWorker');
$routes->get('offline',          'Pwa::offline');
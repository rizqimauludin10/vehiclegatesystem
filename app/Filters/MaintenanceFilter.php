<?php

namespace App\Filters;

use App\Libraries\MaintenanceMode;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MaintenanceFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!MaintenanceMode::isActive()) {
            return;
        }

        helper('url');
        $path = trim(uri_string(), '/');

        // Halaman login & logout tetap bisa dibuka supaya admin bisa masuk
        if ($path === 'auth' || str_starts_with($path, 'auth/')) {
            return;
        }

        // Super Admin selalu lolos; admin lolos hanya jika cakupannya "Hanya petugas"
        if (MaintenanceMode::canBypass(
            session()->get('role'),
            (bool) session()->get('logged_in')
        )) {
            return;
        }

        $state    = MaintenanceMode::get();
        $response = service('response')
            ->setStatusCode(503)
            ->setHeader('Retry-After', '900')
            ->setHeader('Cache-Control', 'no-store');

        // Request AJAX (misalnya proses scan QR) → balas JSON, bukan halaman HTML
        if ($request instanceof IncomingRequest && $request->isAJAX()) {
            return $response->setJSON([
                'status'      => 'error',
                'maintenance' => true,
                'message'     => 'Sistem sedang dalam pemeliharaan. Estimasi selesai: ' . $state['estimasi'] . '.',
            ]);
        }

        return $response->setBody(view('errors/html/maintenance', ['state' => $state]));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
<?php

namespace App\Controllers;

class Pwa extends BaseController
{
    // Halaman offline
    public function offline(): string
    {
        return view('pwa/offline');
    }

    // Serve manifest.json dengan header yang benar
    public function manifest()
    {
        $manifest = file_get_contents(FCPATH . 'manifest.json');

        return $this->response
                    ->setHeader('Content-Type', 'application/manifest+json')
                    ->setBody($manifest);
    }

    // Serve service worker dengan header yang benar
    public function serviceWorker()
    {
        $sw = file_get_contents(FCPATH . 'sw.js');

        return $this->response
                    ->setHeader('Content-Type', 'application/javascript')
                    ->setHeader('Service-Worker-Allowed', '/')
                    ->setBody($sw);
    }
}
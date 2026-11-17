<?php

namespace App\Libraries;

/**
 * ImageCompressor
 *
 * Mengubah foto (JPG/PNG/WebP) menjadi JPEG yang lebih kecil:
 * - Sisi terpanjang diperkecil ke batas tertentu (default 1600px)
 * - Orientasi foto HP diperbaiki (foto tidak tampil miring)
 * - Latar transparan PNG/WebP diganti putih
 *
 * Dipakai oleh controller Kendaraan (dan bisa juga Personel).
 */
class ImageCompressor
{
    /** Batas piksel supaya server tidak kehabisan memori (±40 megapiksel) */
    private const MAX_PIXELS = 40_000_000;

    /**
     * Kompres foto ke JPEG.
     *
     * @param string $source  Path file asli (biasanya file sementara upload)
     * @param string $dest    Path tujuan (.jpg)
     * @param int    $maxSide Panjang sisi terpanjang maksimal (px)
     * @param int    $quality Kualitas JPEG 0–100
     *
     * @return bool true kalau berhasil
     */
    public static function toJpeg(string $source, string $dest, int $maxSide = 1600, int $quality = 80): bool
    {
        if (!function_exists('imagecreatetruecolor')) {
            log_message('error', '[ImageCompressor] Ekstensi GD tidak aktif.');
            return false;
        }

        $info = @getimagesize($source);
        if (!$info || empty($info[0]) || empty($info[1])) {
            return false;
        }

        if ($info[0] * $info[1] > self::MAX_PIXELS) {
            return false;
        }

        // Foto HP 12–50 MP butuh memori cukup besar saat diproses
        @ini_set('memory_limit', '512M');

        switch ($info['mime']) {
            case 'image/jpeg':
                $img = @imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $img = @imagecreatefrompng($source);
                break;
            case 'image/webp':
                $img = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : false;
                break;
            default:
                $img = false;
        }

        if (!$img) {
            return false;
        }

        // Perbaiki orientasi berdasarkan data EXIF (foto HP sering tersimpan miring)
        if ($info['mime'] === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif        = @exif_read_data($source);
            $orientation = (int) ($exif['Orientation'] ?? 1);
            $angle       = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;

            if ($angle !== 0) {
                $rotated = imagerotate($img, $angle, 0);
                if ($rotated) {
                    imagedestroy($img);
                    $img = $rotated;
                }
            }
        }

        $w     = imagesx($img);
        $h     = imagesy($img);
        $scale = min(1, $maxSide / max($w, $h));
        $nw    = max(1, (int) round($w * $scale));
        $nh    = max(1, (int) round($h * $scale));

        $canvas = imagecreatetruecolor($nw, $nh);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($img);

        $dir = dirname($dest);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $ok = imagejpeg($canvas, $dest, $quality);
        imagedestroy($canvas);

        return $ok && is_file($dest);
    }

    /** Terjemahkan kode error upload PHP menjadi pesan yang mudah dipahami */
    public static function uploadError(int $code): string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'ukuran file melebihi batas server (' . ini_get('upload_max_filesize') . ').';
            case UPLOAD_ERR_PARTIAL:
                return 'file hanya terunggah sebagian, kemungkinan koneksi terputus. Coba unggah ulang.';
            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
                return 'server gagal menyimpan file sementara. Hubungi administrator.';
            case UPLOAD_ERR_EXTENSION:
                return 'unggahan diblokir oleh server.';
            default:
                return 'file gagal diunggah.';
        }
    }
}
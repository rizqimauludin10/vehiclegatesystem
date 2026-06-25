/**
 * sw.js — VGS Vehicle Gate System
 * Ganti file sw.js lama kamu dengan ini
 */

const VERSION = "vgs-v1.0.0";
const CACHE_STATIC = `${VERSION}-static`;
const CACHE_PAGES = `${VERSION}-pages`;

// ─── Asset statis yang di-cache saat SW install ───────────
const PRECACHE = [
  "/petugas/scan/mobile",
  "/assets/icons/icon-192.png",
  "/assets/icons/icon-512.png",
];

// ─── URL yang TIDAK boleh di-cache (selalu ke network) ────
const BYPASS = [
  "/petugas/scan/process", // POST API scan — harus selalu hit server
  "/auth/login",
  "/auth/logout",
];

// ─── Helpers ──────────────────────────────────────────────
const isBypass = (url) => BYPASS.some((p) => url.pathname.includes(p));
const isStaticFile = (url) =>
  url.pathname.startsWith("/assets/") ||
  /\.(css|js|woff2?|ttf|png|jpe?g|gif|svg|ico|webp)(\?.*)?$/.test(url.pathname);
const isSameDomain = (url) => url.origin === self.location.origin;
const isCDN = (url) =>
  url.hostname.includes("cdn.jsdelivr.net") ||
  url.hostname.includes("fonts.gstatic.com") ||
  url.hostname.includes("fonts.googleapis.com");

// ═════════════════════════════════════════════════════════
// INSTALL
// ═════════════════════════════════════════════════════════
self.addEventListener("install", (event) => {
  console.log("[SW] Install:", VERSION);
  event.waitUntil(
    caches
      .open(CACHE_STATIC)
      .then((cache) =>
        Promise.allSettled(
          PRECACHE.map((url) =>
            cache
              .add(url)
              .catch((e) =>
                console.warn("[SW] Skip precache:", url, e.message),
              ),
          ),
        ),
      )
      .then(() => self.skipWaiting()),
  );
});

// ═════════════════════════════════════════════════════════
// ACTIVATE — bersihkan cache versi lama
// ═════════════════════════════════════════════════════════
self.addEventListener("activate", (event) => {
  console.log("[SW] Activate:", VERSION);
  event.waitUntil(
    caches
      .keys()
      .then((keys) =>
        Promise.all(
          keys
            .filter((k) => k !== CACHE_STATIC && k !== CACHE_PAGES)
            .map((k) => {
              console.log("[SW] Hapus cache lama:", k);
              return caches.delete(k);
            }),
        ),
      )
      .then(() => self.clients.claim()),
  );
});

// ═════════════════════════════════════════════════════════
// FETCH — router strategi cache
// ═════════════════════════════════════════════════════════
self.addEventListener("fetch", (event) => {
  const req = event.request;
  const url = new URL(req.url);

  // Abaikan non-HTTP & non-GET
  if (!url.protocol.startsWith("http")) return;
  if (req.method !== "GET") return;

  // ① BYPASS — langsung network, tidak di-cache
  if (isBypass(url)) {
    event.respondWith(fetch(req));
    return;
  }

  // ② STATIC FILE (lokal) — Cache First
  if (isSameDomain(url) && isStaticFile(url)) {
    event.respondWith(stratCacheFirst(req, CACHE_STATIC));
    return;
  }

  // ③ CDN (jsdelivr, google fonts) — Cache First
  if (isCDN(url)) {
    event.respondWith(stratCacheFirst(req, CACHE_STATIC));
    return;
  }

  // ④ NAVIGASI HALAMAN — Network First + fallback cache
  if (req.mode === "navigate") {
    event.respondWith(stratNetworkFirst(req));
    return;
  }

  // ⑤ Lainnya — Network saja
  event.respondWith(fetch(req).catch(() => {}));
});

// ═════════════════════════════════════════════════════════
// STRATEGI: Cache First
// ═════════════════════════════════════════════════════════
async function stratCacheFirst(req, cacheName) {
  const hit = await caches.match(req);
  if (hit) return hit;

  try {
    const res = await fetch(req);
    if (res.ok) {
      const cache = await caches.open(cacheName);
      cache.put(req, res.clone());
    }
    return res;
  } catch (e) {
    console.warn("[SW] Cache First gagal:", req.url);
    throw e;
  }
}

// ═════════════════════════════════════════════════════════
// STRATEGI: Network First
// Coba network → jika gagal → cache → jika tidak ada → offline page
// ═════════════════════════════════════════════════════════
async function stratNetworkFirst(req) {
  try {
    const res = await fetch(req);

    // Jika server redirect ke login (session expired), ikutin aja — jangan cache
    if (res.ok && res.status === 200) {
      const cache = await caches.open(CACHE_PAGES);
      cache.put(req, res.clone());
    }
    return res;
  } catch (e) {
    console.warn("[SW] Network gagal, coba cache:", req.url);
    const cached = await caches.match(req);
    if (cached) return cached;

    // Fallback ke halaman mobile yang di-precache
    const fallback = await caches.match("/petugas/scan/mobile");
    if (fallback) return fallback;

    return new Response(pageOffline(), {
      status: 200,
      headers: { "Content-Type": "text/html; charset=utf-8" },
    });
  }
}

// ═════════════════════════════════════════════════════════
// OFFLINE FALLBACK PAGE
// ═════════════════════════════════════════════════════════
function pageOffline() {
  return `<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
<title>VGS — Tidak Ada Koneksi</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{min-height:100vh;display:flex;flex-direction:column;align-items:center;
       justify-content:center;background:#080d17;color:#f0f4ff;
       font-family:system-ui,sans-serif;text-align:center;padding:24px;gap:0}
  .ico{font-size:72px;margin-bottom:20px;opacity:.45;line-height:1}
  h1{font-size:18px;font-weight:700;letter-spacing:.08em;margin-bottom:10px}
  p{font-size:13px;color:#64748b;line-height:1.6;max-width:260px;margin-bottom:24px}
  button{padding:11px 28px;border-radius:99px;border:1px solid rgba(56,189,248,.35);
         background:rgba(56,189,248,.12);color:#38bdf8;font-size:13px;font-weight:600;
         letter-spacing:.05em;cursor:pointer}
</style>
</head>
<body>
  <div class="ico">📡</div>
  <h1>TIDAK ADA KONEKSI</h1>
  <p>Periksa koneksi jaringan kamu, lalu coba muat ulang halaman.</p>
  <button onclick="location.reload()">MUAT ULANG</button>
</body>
</html>`;
}

// ═════════════════════════════════════════════════════════
// MESSAGE — dari halaman utama
// ═════════════════════════════════════════════════════════
self.addEventListener("message", (event) => {
  if (!event.data) return;
  if (event.data.type === "SKIP_WAITING") self.skipWaiting();
  if (event.data.type === "GET_VERSION" && event.ports[0])
    event.ports[0].postMessage({ version: VERSION });
});

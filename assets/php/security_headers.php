<?php
// Centralized security headers for the application.
// Include this file BEFORE any output is sent (before HTML).

// Best-effort detection of HTTPS (works on typical setups)
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

// Content-Security-Policy:
// Pragmatic CSP that allows your site and the CDN you use.
// If you can eliminate 'unsafe-inline' later, remove it and adopt nonces or hashes.
$csp = "default-src 'self'; ";
$csp .= "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; ";
$csp .= "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; ";
$csp .= "style-src-elem 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; ";
$csp .= "img-src 'self' data: blob:; ";
$csp .= "font-src 'self' data: https://fonts.gstatic.com; ";
$csp .= "connect-src 'self' blob: https://cdnjs.cloudflare.com; ";
$csp .= "object-src 'self' data: blob; frame-ancestors 'none'; base-uri 'self'; form-action 'self';";

header("Content-Security-Policy: $csp", true);

// Prevent referrer leakage
header("Referrer-Policy: strict-origin-when-cross-origin", true);

// Prevent clickjacking
header("X-Frame-Options: DENY", true);

// Prevent MIME sniffing
header("X-Content-Type-Options: nosniff", true);

// XSS filter (legacy; modern browsers rely on CSP)
header("X-XSS-Protection: 1; mode=block", true);

// Permissions policy (controls access to features)
// Adjust according to features your app uses (this denies geolocation, camera, microphone).
header("Permissions-Policy: geolocation=(), microphone=(), camera=()", true);

// HSTS: only send over HTTPS
if ($is_https) {
    // NOTE: enable preload only if you meet preloading requirements:
    //   - site served over HTTPS
    //   - all subdomains are HTTPS
    //   - you understand preload implications (can't easily remove)
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload", true);
}
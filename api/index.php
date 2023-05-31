<?php

/**
 * Built assets aren't currently routeable via vercel-php
 * Manually route assets to be found
 * https://github.com/juicyfx/vercel-examples/commit/1fcbe3ff98ae34830cfd779224433cca16bb4f93
 */
if (isset($_GET['type']) && isset($_GET['file']) && isset($_GET['api_request'])) {

    if ($_GET['type'] === 'css') {
        header("Content-type: text/css; charset: UTF-8");
        echo require __DIR__ . '/../public/css/' . basename($_GET['file']);
    } else if ($_GET['type'] === 'js') {
        header('Content-Type: application/javascript; charset: UTF-8');
        echo require __DIR__ . '/../public/js/' . basename($_GET['file']);
    }
} else {
    // Forward Vercel requests to normal index.php
    require __DIR__ . '/../public/index.php';
}

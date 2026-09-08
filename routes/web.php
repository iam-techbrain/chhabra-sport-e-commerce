<?php

use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Models\Product;

Route::get('/sitemap.xml', function () {
    $baseUrl = config('app.url', 'http://localhost');

    $categories = Category::all();
    $products = Product::all();

    $xml = '<' . '?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Core Pages
    $pages = [
        ['loc' => $baseUrl . '/', 'priority' => '1.0', 'changefreq' => 'daily'],
        ['loc' => $baseUrl . '/shop', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['loc' => $baseUrl . '/about', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ['loc' => $baseUrl . '/contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
    ];

    foreach ($pages as $page) {
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($page['loc']) . '</loc>' . "\n";
        $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . $page['changefreq'] . '</changefreq>' . "\n";
        $xml .= '    <priority>' . $page['priority'] . '</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }

    // Categories
    foreach ($categories as $cat) {
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($baseUrl . '/?category=' . $cat->slug) . '</loc>' . "\n";
        $xml .= '    <lastmod>' . ($cat->updated_at ? $cat->updated_at->format('Y-m-d') : date('Y-m-d')) . '</lastmod>' . "\n";
        $xml .= '    <changefreq>weekly</changefreq>' . "\n";
        $xml .= '    <priority>0.8</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }

    // Products
    foreach ($products as $prod) {
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($baseUrl . '/product/' . ($prod->code_id ?? $prod->id)) . '</loc>' . "\n";
        $xml .= '    <lastmod>' . ($prod->updated_at ? $prod->updated_at->format('Y-m-d') : date('Y-m-d')) . '</lastmod>' . "\n";
        $xml .= '    <changefreq>weekly</changefreq>' . "\n";
        $xml .= '    <priority>0.7</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }

    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'text/xml');
});

Route::get('/', function () {
    return view('app');
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');


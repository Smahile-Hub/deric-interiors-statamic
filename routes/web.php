<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\Yaml\Yaml;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;

// Route::statamic('example', 'example-view', [
//    'title' => 'Example'
// ]);

Route::get('/products/{slug?}', function (?string $slug = null) {
    return redirect($slug ? "/shop/{$slug}" : '/shop', 301);
})->where('slug', '.*');

Route::get('/product/{slug?}', function (?string $slug = null) {
    return redirect($slug ? "/shop/{$slug}" : '/shop', 301);
})->where('slug', '.*');

Route::get('/sitemap.xml', function () {
    $settings = GlobalSet::findByHandle('settings')?->inDefaultSite();
    $settingsFile = file_exists(base_path('content/globals/default/settings.yaml'))
        ? base_path('content/globals/default/settings.yaml')
        : base_path('content/globals/settings.yaml');
    $settingsData = file_exists($settingsFile) ? Yaml::parseFile($settingsFile) : [];
    $baseUrl = rtrim(
        $settings?->get('canonical_base_url')
        ?: $settings?->get('site_url')
        ?: ($settingsData['canonical_base_url'] ?? $settingsData['site_url'] ?? config('app.url')),
        '/'
    );

    $urls = Entry::whereInCollection(['pages', 'blog', 'products'])
        ->filter(fn ($entry) => $entry->published() && $entry->url() && ! $entry->get('noindex'))
        ->map(function ($entry) use ($baseUrl) {
            $path = $entry->url() === '/' ? '/' : '/'.ltrim($entry->url(), '/');

            $highPriorityPages = ['/services', '/about', '/contact', '/book-appointment'];
            $mediumPriorityPages = ['/blog', '/projects', '/shop', '/testimonials'];

            if ($path === '/') {
                $priority = '1.0';
            } elseif ($entry->collectionHandle() === 'blog') {
                $priority = '0.7';
            } elseif ($entry->collectionHandle() === 'products') {
                $priority = '0.7';
            } elseif (in_array($path, $highPriorityPages)) {
                $priority = '0.9';
            } elseif (in_array($path, $mediumPriorityPages)) {
                $priority = '0.8';
            } else {
                $priority = '0.6';
            }

            return [
                'loc' => $baseUrl.$path,
                'lastmod' => $entry->lastModified()->toAtomString(),
                'changefreq' => in_array($entry->collectionHandle(), ['blog', 'products']) ? 'monthly' : 'weekly',
                'priority' => $priority,
            ];
        })
        ->sortBy('loc')
        ->values();

    $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

    foreach ($urls as $url) {
        $xml .= "  <url>\n";
        $xml .= '    <loc>'.htmlspecialchars($url['loc'], ENT_XML1)."</loc>\n";
        $xml .= '    <lastmod>'.$url['lastmod']."</lastmod>\n";
        $xml .= '    <changefreq>'.$url['changefreq']."</changefreq>\n";
        $xml .= '    <priority>'.$url['priority']."</priority>\n";
        $xml .= "  </url>\n";
    }

    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml');
});

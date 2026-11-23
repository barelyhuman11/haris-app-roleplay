<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($urls as $url)
        <url>
            <loc>{{ $url['loc'] }}</loc>
            @if ($options->get('sitemap_lastmod', 'N') == 'Y' && $url['lastmod'])
                <lastmod>{{ $url['lastmod'] }}</lastmod>
            @endif

            @if (!empty($options->get('sitemap_changefreq', '')))
                <changefreq>{{ $options->get('sitemap_changefreq', '') }}</changefreq>
            @endif

            @if (!empty($options->get('sitemap_priority', '')) || is_numeric($options->get('sitemap_priority', '')))
                <priority>{{ $options->get('sitemap_priority', '') }}</priority>
            @endif
        </url>
    @endforeach
</urlset>
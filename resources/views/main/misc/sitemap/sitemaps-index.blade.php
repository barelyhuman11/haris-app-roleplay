<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($sitemaps as $sitemap)
        <sitemap>
            <loc>{{ $sitemap }}</loc>
            
            @if ($options->get('sitemap_index_lastmod', 'N') == 'Y')
                <lastmod>{{ date('Y-m-d') }}</lastmod>
            @endif
        </sitemap>
    @endforeach
</sitemapindex>
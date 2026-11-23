<x-admin.layouts.app :breadcrumb="$breadcrumb">
    <x-slot:pageTitle>{{ __('Settings') . ' : ' . __('Sitemap') }}</x-slot>
    <div class="row">
        <div class="col-12 col-lg-6">

            @if (session('success'))
                <x-admin.components.alert>
                    {{ session('success') }}
                </x-admin.components.alert>
            @endif
        
            @if (session('error'))
                <x-admin.components.alert type="danger">
                    {{ session('error') }}
                </x-admin.components.alert>
            @endif

            <form action="{{ route('admin.settings.sitemap') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">

                        <h2 class="h5">{{ __('Sitemap') }}</h2>

                        <x-admin.forms.input-text name="sitemap_items" :label="__('URL per sitemap')" :value="old('sitemap_items') ?? $options->get('sitemap_items', 1000)" :is-required="true" />
                        
                        <div class="mb-3">
                            <label>{!! __('Enable sitemap :tag', ['tag' => '<code>&lt;lastmod&gt;</code>']) !!}</label>
                            <x-admin.forms.radio name="sitemap_lastmod" value="Y" :label="__('Yes')" :selected="old('sitemap_lastmod') ?? $options->get('sitemap_lastmod', 'N')" />
                            <x-admin.forms.radio name="sitemap_lastmod" value="N" :label="__('No')" :selected="old('sitemap_lastmod') ?? $options->get('sitemap_lastmod', 'N')" />
                        </div>

                        <x-admin.forms.select name="sitemap_changefreq" :label="__('Sitemap :tag', ['tag' => '<code>&lt;changefreq&gt;</code>'])" :options="['' => 'None', 'always' => 'Always', 'hourly' => 'Hourly', 'daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly', 'never' => 'Never']" :selected="old('sitemap_changefreq') ?? $options->get('sitemap_changefreq', '')" />
                        <x-admin.forms.input-text name="sitemap_priority" :label="__('Sitemap :tag', ['tag' => '<code>&lt;priority&gt;</code>'])" :value="old('sitemap_priority') ?? $options->get('sitemap_priority', '')" />

                        <hr>

                        <h2 class="h5">{{ __('Sitemap Index') }}</h2>

                        <p>URL : <a href="{{ route('sitemaps') }}" target="_blank">{{ route('sitemaps') }}</a></p>

                        <div class="mb-3">
                            <label>{!! __('Enable sitemap index :tag', ['tag' => '<code>&lt;lastmod&gt;</code>']) !!}</label>
                            <x-admin.forms.radio name="sitemap_index_lastmod" value="Y" :label="__('Yes')" :selected="old('sitemap_index_lastmod') ?? $options->get('sitemap_index_lastmod', 'N')" />
                            <x-admin.forms.radio name="sitemap_index_lastmod" value="N" :label="__('No')" :selected="old('sitemap_index_lastmod') ?? $options->get('sitemap_index_lastmod', 'N')" />
                        </div>

                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-admin.layouts.app>
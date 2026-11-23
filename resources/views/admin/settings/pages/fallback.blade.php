<x-admin.layouts.app :breadcrumb="$breadcrumb">
    <x-slot:pageTitle>{{ __('Page Settings') . ' : ' . __('404') }}</x-slot>
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

            <form action="{{ route('admin.settings.pages.fallback') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">

                        <div class="form-group">
                            <div>
                                <label>{{ __('Enable fake error page') }}</label>
                                <div class="row">
                                    <div class="col-6">
                                        <x-admin.forms.radio name="fallback_page_enable_fake_error_page" value="Y" :label="__('Yes')" :selected="old('fallback_page_enable_fake_error_page') ?? $options->get('fallback_page_enable_fake_error_page', 'N')" />
                                    </div>
                                    <div class="col-6">
                                        <x-admin.forms.radio name="fallback_page_enable_fake_error_page" value="N" :label="__('No')" :selected="old('fallback_page_enable_fake_error_page') ?? $options->get('fallback_page_enable_fake_error_page', 'N')" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div>
                                <label>{{ __('Redirect to Home Page') }}</label>
                                <div class="row">
                                    <div class="col-6">
                                        <x-admin.forms.radio name="fallback_page_redirect_to_home_page" value="Y" :label="__('Yes')" :selected="old('fallback_page_redirect_to_home_page') ?? $options->get('fallback_page_redirect_to_home_page', 'N')" />
                                    </div>
                                    <div class="col-6">
                                        <x-admin.forms.radio name="fallback_page_redirect_to_home_page" value="N" :label="__('No')" :selected="old('fallback_page_redirect_to_home_page') ?? $options->get('fallback_page_redirect_to_home_page', 'N')" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <x-admin.forms.textarea name="blocked_slugs" :label="__('Block pages')" rows="3">{{ old('blocked_slugs') ?? $options->get('blocked_slugs') }}</x-admin.forms.textarea>

                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-admin.layouts.app>
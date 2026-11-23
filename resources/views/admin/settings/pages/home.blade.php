<x-admin.layouts.app :breadcrumb="$breadcrumb">
    <x-slot:pageTitle>{{ __('Page Settings') . ' : ' . __('Home') }}</x-slot>
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

            <form action="{{ route('admin.settings.pages.home') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">
                        <x-admin.forms.input-text name="home_page_title" :label="__('Home page title')" :value="old('home_page_title') ?? $options->get('home_page_title')" />
                        <x-admin.forms.textarea name="home_page_meta_description" :label="__('Home page meta description')" rows="3">{{ old('home_page_meta_description') ?? $options->get('home_page_meta_description') }}</x-admin.forms.textarea>

                        <div class="form-group">
                            <div>
                                <label>{{ __('Enable fake error page') }}</label>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <x-admin.forms.radio name="home_page_enable_fake_error_page" value="Y" :label="__('Yes')" :selected="old('home_page_enable_fake_error_page') ?? $options->get('home_page_enable_fake_error_page', 'N')" />
                                </div>
                                <div class="col-6">
                                    <x-admin.forms.radio name="home_page_enable_fake_error_page" value="N" :label="__('No')" :selected="old('home_page_enable_fake_error_page') ?? $options->get('home_page_enable_fake_error_page', 'N')" />
                                </div>
                            </div>
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
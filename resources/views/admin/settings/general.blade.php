<x-admin.layouts.app :breadcrumb="$breadcrumb">
    <x-slot:pageTitle>{{ __('Settings') . ' : ' . __('General') }}</x-slot>
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

            <form action="{{ route('admin.settings.general') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">

                        <x-admin.forms.input-text name="site_title" :label="__('Site title')" :value="old('site_title') ?? $options->get('site_title')" :is-required="true" />
                        <x-admin.forms.input-text name="site_tagline" :label="__('Tagline')" :value="old('site_tagline') ?? $options->get('site_tagline')" />

                        <div class="form-group">
                            <div>
                                <label>{{ __('Enable registration') }} <span class="text-danger">*</span></label>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <x-admin.forms.radio name="enable_register" value="Y" :label="__('Yes')" :selected="old('enable_register') ?? $options->get('enable_register', 'Y')" />
                                </div>
                                <div class="col-6">
                                    <x-admin.forms.radio name="enable_register" value="N" :label="__('No')" :selected="old('enable_register') ?? $options->get('enable_register', 'Y')" />
                                </div>
                            </div>
                        </div>

                        <x-admin.forms.select name="default_group" :label="__('Default Group')" :options="$groupsOptions" :selected="old('default_group') ?? $options->get('default_group', $defaultGroup->id)" :is-required="true" />

                        <x-admin.forms.select name="site_language" :label="__('Site language')" :options="$siteLanguages" :selected="old('site_language') ?? $options->get('site_language')" />

                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-admin.layouts.app>
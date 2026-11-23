<x-admin.layouts.app :breadcrumb="$breadcrumb">
    <x-slot:pageTitle>{{ __('Edit Widget') }}</x-slot>
    <div 
        class="row"
        x-data="{
            countries: '{{ old('countries') ?? \App\Helpers\Widgets::detail($widget->value, 'countries', 'ALL') }}',
        }"
    >
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

            <form action="{{ route('admin.settings.widgets.update', ['widget' => $widget]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">

                        <x-admin.forms.select name="section" :label="__('Section')" :options="$sections" :selected="old('section') ?? $widget->name" :is-required="true" />
                        <x-admin.forms.input-text name="widget_name" :label="__('Widget name')" :value="old('widget_name') ?? \App\Helpers\Widgets::detail($widget->value, 'name')" :is-required="true" />
                        <x-admin.forms.textarea name="content" :label="__('Content')" rows="5">{{ old('content') ?? \App\Helpers\Widgets::detail($widget->value, 'content') }}</x-admin.forms.textarea>
                        <x-admin.forms.select name="user_agent" :label="__('User Agent')" :options="$userAgents" :selected="old('user_agent') ?? \App\Helpers\Widgets::detail($widget->value, 'user_agent', 'ALL')" />

                        <div class="form-group">
                            <div>
                                <label>{{ __('Countries') }}</label>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="custom-control custom-radio">
                                        <input
                                            type="radio"
                                            class="custom-control-input"
                                            value="ALL"
                                            id="radioCountriesAll"
                                            x-model="countries"
                                        >
                                        <label for="radioCountriesAll" class="custom-control-label">{{ __('All') }}</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="custom-control custom-radio">
                                        <input
                                            type="radio"
                                            class="custom-control-input"
                                            value="WHITELIST"
                                            id="radioCountriesWhitelist"
                                            x-model="countries"
                                        >
                                        <label for="radioCountriesWhitelist" class="custom-control-label">{{ __('Whitelist') }}</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="custom-control custom-radio">
                                        <input
                                            type="radio"
                                            class="custom-control-input"
                                            value="BLACKLIST"
                                            id="radioCountriesBlacklist"
                                            x-model="countries"
                                        >
                                        <label for="radioCountriesBlacklist" class="custom-control-label">{{ __('Blacklist') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="countries !== 'ALL'">
                            <div class="form-group">
                                <label for="selectCountries">{{ __('Select Countries') }}</label>
                                <select
                                    class="form-control"
                                    id="selectCountries"
                                    name="list_countries[]"
                                    size="10"
                                    multiple
                                >
                                    @foreach ($countries as $countryCode => $countryName)
                                        <option value="{{ $countryCode }}" @selected(old('list_countries') && is_array(old('list_countries')) ? in_array($countryCode, old('list_countries')) : in_array($countryCode, \App\Helpers\Widgets::detail($widget->value, 'list_countries', [])))>{{ $countryName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-admin.layouts.app>
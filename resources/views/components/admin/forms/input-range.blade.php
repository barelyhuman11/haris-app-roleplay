{{-- Data Properties ['label'] Attributes ['name'] --}}

@props(['label', 'isRequired' => false])

<div class="form-group" x-data="{ rangeValue: '{{ old($attributes->get('name')) ?? $attributes->get('value') }}' }">
    <div class="d-flex justify-content-between align-items-center">
        <label for="field{{ Str::studly($attributes->get('name')) }}">
            {{ $label }}
            @if ($isRequired)
                <span class="text-danger">*</span>
            @endif
        </label>
        <span x-text="rangeValue"></span>
    </div>
    <input
        {{ $attributes
            ->class(['custom-range', 'is-invalid' => $errors->has($attributes->get('name'))]) 
            ->merge(['type' => 'range', 'id' => 'field' . Str::studly($attributes->get('name'))]) }}
        value="{{ empty($attributes->get('value')) ? old($attributes->get('name')) : $attributes->get('value') }}"
        x-model="rangeValue"
        @error($attributes->get('name')) aria-describedby="validation{{ Str::studly($attributes->get('name')) }}Feedback" @enderror
    >
    @error($attributes->get('name'))
        <div class="invalid-feedback" id="validation{{ Str::studly($attributes->get('name')) }}Feedback">
            {{ $message }}
        </div>
    @enderror
</div>
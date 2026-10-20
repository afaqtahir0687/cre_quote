@props(['label', 'name', 'type' => 'text', 'value' => '', 'placeholder' => ' '])

<div class="form-floating mb-3">
    <input type="{{ $type }}" 
           name="{{ $name }}" 
           id="{{ $name }}" 
           class="form-control @error($name) is-invalid @enderror" 
           placeholder="{{ $placeholder }}" 
           value="{{ old($name, $value) }}"
           {{ $attributes }}>
    <label for="{{ $name }}">{{ $label }}</label>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

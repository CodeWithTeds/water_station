@props(['type' => 'text', 'name', 'label', 'value' => '', 'required' => false, 'placeholder' => ''])

<div class="mb-5">
    <label for="{{ $name }}" class="block font-medium text-gray-700 mb-1.5 text-base">{{ $label }}</label>
    <input 
        type="{{ $type }}" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        placeholder="{{ $placeholder }}"
        value="{{ $type !== 'password' ? old($name, $value) : '' }}"
        {{ $required ? 'required' : '' }}
        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
    >
    @error($name)
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div> 
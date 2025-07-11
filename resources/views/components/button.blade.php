@props(['type' => 'submit'])

<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => 'w-full bg-primary hover:bg-blue text-white text-base font-bold py-2.5 px-4 rounded-md transition duration-200']) }}
>
    {{ $slot }}
</button> 
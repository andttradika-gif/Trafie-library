<button {{ $attributes->merge(['type' => 'submit', 'class' => 'tl-btn']) }}>
    {{ $slot }}
</button>
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-[#b91c1c] border border-transparent rounded-lg font-medium text-xs text-white hover:bg-red-800 focus:outline-none transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

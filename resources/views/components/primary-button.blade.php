<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-[#013981] border border-[#013981] rounded-lg font-medium text-xs text-white hover:bg-[#012b63] focus:outline-none transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

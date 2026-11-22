<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-white border border-[#e4e4e7] rounded-lg font-medium text-xs text-[#0b0b0c] hover:bg-[#f4f4f5] focus:outline-none disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

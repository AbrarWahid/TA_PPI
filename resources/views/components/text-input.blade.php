@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-[#e4e4e7] focus:border-[#013981] focus:ring-0 rounded-lg shadow-surface text-sm text-[#0b0b0c]']) }}>

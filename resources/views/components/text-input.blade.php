@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full border border-gray-300 rounded-md px-4 py-2 bg-white text-black focus:outline-none focus:ring-2 focus:ring-indigo-500']) }}>

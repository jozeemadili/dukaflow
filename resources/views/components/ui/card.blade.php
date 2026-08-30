@props(['padding' => 'p-6'])

<div {{ $attributes->merge(['class' => "bg-canvas border border-hairline rounded-lg shadow-[0_1px_3px_rgba(0,55,112,0.06)] $padding"]) }}>
    {{ $slot }}
</div>

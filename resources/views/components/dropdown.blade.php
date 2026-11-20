@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'p-1.5 bg-white'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    '56' => 'w-56',
    '64' => 'w-64',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" @close.stop="open = false">
    <div @click="open = ! open" :aria-expanded="open.toString()">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
            class="absolute z-50 mt-2 {{ $width }} rounded-2xl shadow-xl border border-emas-200/90 {{ $alignmentClasses }}"
            @click="open = false">
        <div class="rounded-2xl overflow-hidden {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>

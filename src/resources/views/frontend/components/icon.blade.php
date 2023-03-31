@props([
    'icon',
    'color' => null,
    'size' => null
])
    <i {{ $attributes->class([
    $icon,
    $color => "text-{$color}",
    $size => "fs-{$size}",
    ]) }}></i>


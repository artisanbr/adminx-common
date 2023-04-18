@props([
    'title' => null,
    'color' => 'primary',
    'bgType' => 'light',
    'noClose' => false,
    'icon' => null,
    'iconSize' => '2hx',
    'iconColor' => null,
    'iconClass' => null,
    'titleClass' => null,
])
<?php
$computedClass = function () use ($bgType, $color, $noClose, $icon): string {
    $finalClass = '';

    $finalClass .= match ($bgType) {
        'light' => " bg-light-{$color}",
        'solid' => " bg-{$color}",
        default => " alert-{$color}",
    };

    if ($noClose) {
        $finalClass .= ' alert-dismissible';
    }

    if ($icon) {
        $finalClass .= ' d-flex align-items-center';
    }

    return $finalClass;
}
?>
<div {{ $attributes->merge([
    'class' => 'alert '.$computedClass()
    ]) }}>

    @if(isset($icon))
        <x-frontend::icon :icon="$icon" :size="$iconSize" :color="$iconColor ?? $color" :class="'me-4 '.$iconClass"/>
    @endif
    <div class="d-flex flex-column pe-0 pe-sm-10">
        @if($title)
            <h4 class="mb-1 {{$titleClass()}}">{{ $title }}</h4>
        @endif
        <!--begin::Content-->
        <span>{{ $slot }}</span>
        <!--end::Content-->
    </div>

    @if(!$noClose)
        <button type="button"
                class="position-absolute top-0 end-0 mt-3 h-auto btn btn-icon"
                data-bs-dismiss="alert">
            <x-frontend::icon icon="close" size="2" :color="$color"/>
        </button>
    @endif


</div>

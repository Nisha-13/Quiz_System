@php
    $from = $from ?? '#0d9488';
    $to = $to ?? $from;
    $via = $via ?? null;
    $steps = max(2, (int) ($steps ?? 24));
    $height = (int) ($height ?? 8);
    $width = $width ?? '100%';
    $colors = \App\Support\Gradient::stops($from, $via, $to, $steps);
    $cellWidth = 100 / count($colors);
@endphp
<div class="cert-gradient {{ $class ?? '' }}"
    style="width: {{ $width }}; height: {{ $height }}px; margin: 0 auto;">
    <table cellpadding="0" cellspacing="0" border="0"
        style="width: 100%; height: {{ $height }}px; border-collapse: collapse; table-layout: fixed;">
        <tr>
            @foreach ($colors as $color)
                <td style="width: {{ number_format($cellWidth, 4) }}%; height: {{ $height }}px; padding: 0; border: 0; background-color: {{ $color }};"></td>
            @endforeach
        </tr>
    </table>
</div>

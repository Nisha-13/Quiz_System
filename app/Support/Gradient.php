<?php

namespace App\Support;

class Gradient
{
    /**
     * Build the list of colours of a gradient strip.
     *
     * CSS gradients are not supported by the PDF renderer, so gradients are
     * drawn as a row of solid coloured cells. The same markup is used on the
     * certificate page and inside the PDF, so both look identical.
     *
     * @return array<int, string>
     */
    public static function stops(string $from, ?string $via, string $to, int $steps): array
    {
        $steps = max(2, $steps);

        $start = self::rgb($from);
        $end = self::rgb($to);

        if ($via === null || $via === '') {
            $stops = [];

            for ($i = 0; $i < $steps; $i++) {
                $stops[] = self::hex(self::mix($start, $end, $i / ($steps - 1)));
            }

            return $stops;
        }

        $middle = self::rgb($via);
        $half = (int) floor($steps / 2);
        $stops = [];

        for ($i = 0; $i < $half; $i++) {
            $stops[] = self::hex(self::mix($start, $middle, $half === 1 ? 1 : $i / ($half - 1)));
        }

        for ($i = 0; $i < $steps - $half; $i++) {
            $stops[] = self::hex(self::mix($middle, $end, $steps - $half === 1 ? 1 : $i / ($steps - $half - 1)));
        }

        return $stops;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private static function rgb(string $hex): array
    {
        $hex = ltrim(trim($hex), '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * @param  array{0: int, 1: int, 2: int}  $a
     * @param  array{0: int, 1: int, 2: int}  $b
     * @return array{0: int, 1: int, 2: int}
     */
    private static function mix(array $a, array $b, float $ratio): array
    {
        return [
            (int) round($a[0] + ($b[0] - $a[0]) * $ratio),
            (int) round($a[1] + ($b[1] - $a[1]) * $ratio),
            (int) round($a[2] + ($b[2] - $a[2]) * $ratio),
        ];
    }

    /**
     * @param  array{0: int, 1: int, 2: int}  $rgb
     */
    private static function hex(array $rgb): string
    {
        return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
    }
}

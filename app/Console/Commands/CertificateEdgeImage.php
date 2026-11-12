<?php

namespace App\Console\Commands;

use App\Support\Gradient;
use Illuminate\Console\Command;

class CertificateEdgeImage extends Command
{
    /**
     * The colours of the gradient strips of the certificate.
     */
    private const FROM = '#0d9488';

    private const VIA = '#10b981';

    private const TO = '#0d9488';

    protected $signature = 'certificate:edge-image';

    protected $description = 'Regenerate the gradient strip used as the left/right border of the certificate';

    public function handle(): int
    {
        $path = public_path('images/cert-edge.png');

        if (! function_exists('imagecreatetruecolor')) {
            $this->error('The GD extension is required to generate the certificate edge image.');

            return self::FAILURE;
        }

        $width = 8;
        $height = 400;
        $stripes = 200;

        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));

        $colors = Gradient::stops(self::FROM, self::VIA, self::TO, $stripes);

        foreach ($colors as $index => $color) {
            [$r, $g, $b] = sscanf($color, '#%02x%02x%02x');

            $y = (int) round($index * $height / $stripes);
            $h = (int) round(($index + 1) * $height / $stripes) - $y;

            $shade = imagecolorallocate($image, $r, $g, $b);
            imagefilledrectangle($image, 0, $y, $width - 1, $y + $h - 1, $shade);
        }

        imagepng($image, $path);
        imagedestroy($image);

        $this->info('Certificate edge image written to '.$path);

        return self::SUCCESS;
    }
}

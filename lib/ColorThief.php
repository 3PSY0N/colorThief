<?php

class ColorThief
{
    public function img2hex($img, $default = 'ffffff'): string
    {
        if (!exif_imagetype($img)) {
            return $default;
        } else {
            $type = getimagesize($img)[2];

            switch ($type) {
                case 2:
                    $image = imagecreatefromjpeg($img);
                    break;
                case 3:
                    $image = imagecreatefrompng($img);
                    break;
                case 18:
                    $image = imagecreatefromwebp($img);
                    break;
                default:
                    return $default;
            }
        }

        $newImg = imagecreatetruecolor(1, 1);

        imagecopyresampled($newImg, $image, 0, 0, 0, 0, 1, 1, imagesx($image), imagesy($image));

        $hex = dechex(imagecolorat($newImg, 0, 0));

        return '#' . strtoupper(str_pad($hex, 6, '0', STR_PAD_LEFT));
    }

    public function hex2rgb(string $colour, $split = false): false|array|string
    {
        if ($colour[0] == '#') {
            $colour = substr($colour, 1);
        }

        if (strlen($colour) === 6) {
            [$r, $g, $b] = [$colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]];
        } elseif (strlen($colour) === 3) {
            [$r, $g, $b] = [$colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]];
        } else {
            return false;
        }

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        if ($split) {
            return ['r' => $r, 'g' => $g, 'b' => $b];
        }

        return $r . ' ' . $g . ' ' . $b;
    }

    public function getContrastColor(string $hexColor): string
    {
        // hexColor RGB
        $R1 = hexdec(substr($hexColor, 1, 2));
        $G1 = hexdec(substr($hexColor, 3, 2));
        $B1 = hexdec(substr($hexColor, 5, 2));

        // Black RGB
        $blackColor    = "#000000";
        $R2BlackColor = hexdec(substr($blackColor, 1, 2));
        $G2BlackColor = hexdec(substr($blackColor, 3, 2));
        $B2BlackColor = hexdec(substr($blackColor, 5, 2));

        // Calc contrast ratio
        $L1 = 0.2126 * pow($R1 / 255, 2.2) +
            0.7152 * pow($G1 / 255, 2.2) +
            0.0722 * pow($B1 / 255, 2.2);

        $L2 = 0.2126 * pow($R2BlackColor / 255, 2.2) +
            0.7152 * pow($G2BlackColor / 255, 2.2) +
            0.0722 * pow($B2BlackColor / 255, 2.2);

        if ($L1 > $L2) {
            $contrastRatio = (int)(($L1 + 0.05) / ($L2 + 0.05));
        } else {
            $contrastRatio = (int)(($L2 + 0.05) / ($L1 + 0.05));
        }

        // If contrast is more than 5, return black color
        if ($contrastRatio > 5) {
            return '#000000';
        } else {
            // if not, return white color.
            return '#FFFFFF';
        }
    }
}
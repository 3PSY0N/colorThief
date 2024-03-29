<?php

class ColorThief
{
    public function scan_d($dir)
    {
        $result = [];
        $cdir   = scandir($dir);

        foreach ($cdir as $key => $value) {
            if (!in_array($value, [".", ".."])) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = scan_d($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

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

$colorThief = new ColorThief();
$imgs       = $colorThief->scan_d('img');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-control" content="public">
    <style>
      img {
        border: 0;
        vertical-align: middle;
      }
      body {
          transition: background-color 0.8s ease;
      }
    </style>
    <title>Demo - Color Thief</title>
</head>
<body>
<h1 style="text-align:center;">Demo - Color Thief</h1>
<div style="display:flex;flex-wrap:wrap;justify-content:center;">
    <?php
    foreach ($imgs as $img):
        $img2hex = $colorThief->img2hex('img/' . $img);
        $rgb = $colorThief->hex2rgb($colorThief->img2hex('img/' . $img));
        $contrast = $colorThief->getContrastColor($img2hex);
        $mime = getimagesize('img/' . $img)['mime'];
    ?>
    <div  style="margin:1.2rem;text-align:center;box-shadow:0 0 10px 5px rgb(<?= $rgb; ?>);background-color:<?= $img2hex; ?>" >
        <img style="object-fit:cover;background-color:#<?= $img2hex; ?>" height="200" width="200" src="<?= 'img/' . $img ?>" alt="<?= $img2hex; ?>">
        <div class="hover" style="padding:2rem 0;font-family: Tahoma, sans-serif" data-rgb="<?= $rgb; ?>">
            <p>
                <span style="display:block;color: <?= $contrast; ?>"><?= $img2hex; ?></span>
                <span style="display:block;color: <?= $contrast; ?>"><?= 'rgb(' . $rgb . ')' ?></span>
                <span style="display:block;color: <?= $contrast; ?>">Contrast: <?= $contrast; ?></span>
                <span style="display:block;color: <?= $contrast; ?>">MIME: <?= $mime; ?></span>
            </p>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<script>
    const hoverElements = document.querySelectorAll('.hover');
    const body = document.body;

    hoverElements.forEach((element) => {
        element.addEventListener('mouseover', (event) => {
            const rgbValue = event.target.getAttribute('data-rgb');
            body.style.backgroundColor = `rgb(${rgbValue})`;
        });

        element.addEventListener('mouseout', () => {
            body.style.backgroundColor = '';
        });
    });
</script>
</body>
</html>

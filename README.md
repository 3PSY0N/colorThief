# Color Thief

The aim of this project is to recover the dominant color of an image in Hexadecimal and RGB.

Just add some pictures on img folder and run your PHP server.

![demo](https://github.com/3PSY0N/colorThief/assets/78256817/84261c67-1540-472d-b717-fac02a9c51fa)

**Usage**
```php
<?php

# Path to the image
$imgPath = '/path/to/img.jpg';

# Create a new instance of ColorThief
$colorThief = new ColorThief();

# Get the dominant color in hexadecimal
$img2hex = $colorThief->img2hex($imgPath);

# Get the dominant color in RGB
$rgb = $colorThief->hex2rgb($colorThief->img2hex($imgPath));

# Get the contrast color in hexadecimal
$contrast = $colorThief->getContrastColor($img2hex);
```
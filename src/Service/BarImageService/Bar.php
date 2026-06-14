<?php

namespace App\Service\BarImageService;

class Bar
{
    const COLOUR_GREEN = [148, 206, 88];
    const COLOUR_AMBER = [255, 191, 0];
    const COLOUR_RED = [252, 12, 27];

    const COLOUR_WHITE = [255, 255, 255];
    const COLOUR_BLACK = [0, 0, 0];
    const COLOUR_GREY = [190, 190, 190];

    const VALUE_BOX_WIDTH = 128;
    const VALUE_BOX_HALF_WIDTH = self::VALUE_BOX_WIDTH / 2;
    const VALUE_BOX_VALUE_FONT_SIZE = 18;
    const VALUE_BOX_UNIT_FONT_SIZE = 9;

    const BAR_Y_OFFSET = 25;

    const BOUNDARY_LABEL_FONT_SIZE = 9;

    const FONTS = [
        'sans-normal' => __DIR__ . '/Fonts/OpenSans-Regular.ttf',
        'sans-medium' => __DIR__ . '/Fonts/OpenSans-Medium.ttf',
        'sans-bold' => __DIR__ . '/Fonts/OpenSans-Bold.ttf',
    ];

    public function __construct(
        public int $width = 200,
        public int $height = 40,
        public array $borderColor = BAR::COLOUR_GREY,
        public int $borderWidth = 5,
        public int $cornerRadius = 18,
        public array $barColor = BAR::COLOUR_WHITE,
        public array $fillColor = BAR::COLOUR_WHITE,
        public float $value = 0,
        public string $unit = '',
        public array $boundaries = [0,0.4,0.8,1.2],
        public array $boundaryLabels = ['0.4','0.8'],
        public array $boundaryLabelsColor = BAR::COLOUR_BLACK,
        public array $valueLabelColor = BAR::COLOUR_BLACK,
        public string $font = 'sans-medium',
    ) {}

    /**
     * Fill a rounded rectangle
     * 
     * @param \GdImage $image
     * @param int $x The x coordinate of the top-left corner of the rectangle.
     * @param int $y The y coordinate of the top-left corner of the rectangle.
     * @param int $width The width of the rectangle.
     * @param int $height The height of the rectangle.
     * @param int $radius The radius of the corners.
     * @param int $color The color of the rectangle.
     * 
     * @return void
     */
    private function fillRoundedRect(
        \GdImage $image,
        int $x,
        int $y,
        int $width,
        int $height,
        int $radius,
        int $color,
    ): void {
        if ($width <= 0 || $height <= 0) {
            return;
        }

        $radius = max(0, min($radius, intdiv($width, 2), intdiv($height, 2)));

        imagefilledrectangle($image, $x + $radius, $y, $x + $width - $radius, $y + $height, $color);
        imagefilledrectangle($image, $x, $y + $radius, $x + $width, $y + $height - $radius, $color);

        if ($radius === 0) {
            return;
        }

        $diameter = $radius * 2;
        imagefilledellipse($image, $x + $radius, $y + $radius, $diameter, $diameter, $color);
        imagefilledellipse($image, $x + $width - $radius, $y + $radius, $diameter, $diameter, $color);
        imagefilledellipse($image, $x + $radius, $y + $height - $radius, $diameter, $diameter, $color);
        imagefilledellipse($image, $x + $width - $radius, $y + $height - $radius, $diameter, $diameter, $color);
    }

    /**
     * Draw a rounded box
     * 
     * @param \GdImage $image
     * @param int $x The x coordinate of the top-left corner of the box.
     * @param int $y The y coordinate of the top-left corner of the box.
     * @param int $width The width of the box.
     * @param int $height The height of the box.
     * @param int $radius The radius of the corners.
     * @param int $borderColor The color of the border.
     * @param int $borderWidth The width of the border.
     * @param int $fillColor The color of the fill.
     * 
     * @return void
     */
    private function drawRoundedBox(
        \GdImage $image,
        int $x,
        int $y,
        int $width,
        int $height,
        int $radius,
        int $borderColor,
        int $borderWidth,
        int $fillColor
    ): void {
        if ($borderWidth <= 0) {
            return;
        }

        $this->fillRoundedRect($image, $x, $y, $width, $height, $radius, $borderColor);

        $innerX = $x + $borderWidth;
        $innerY = $y + $borderWidth;
        $innerWidth = $width - ($borderWidth * 2);
        $innerHeight = $height - ($borderWidth * 2);
        $innerRadius = max(0, $radius - $borderWidth);

        if ($innerWidth <= 0 || $innerHeight <= 0) {
            return;
        }

        $this->fillRoundedRect($image, $innerX, $innerY, $innerWidth, $innerHeight, $innerRadius, $fillColor);
    }

    /**
     * Limit the x coordinate of the point to the width of the image.
     * 
     * @param int $pointX The x coordinate of the point.
     * 
     * @return int The limited x coordinate.
     */
    private function limitPointX(int $pointX): int
    {
        $maxPointX = $this->width - self::VALUE_BOX_HALF_WIDTH - $this->borderWidth;
        $minPointX = self::VALUE_BOX_HALF_WIDTH + $this->borderWidth + 15;
        if ($pointX < $minPointX) {
            return $minPointX;
        }
        if ($pointX > $maxPointX) {
            return $maxPointX;
        }
        return $pointX;
    }

    /**
     * Get the size of the text in the font.
     * 
     * @param string $font The font to use.
     * @param int $fontSize The size of the font.
     * @param string $text The text to get the size of.
     * 
     * @return array The width and height of the text.
     */
    private function fontTextSize(string $font, int $fontSize, string $text): array
    {
        $tSize = imagettfbbox($fontSize, 0, self::FONTS[$font], $text);

        $tWidth = abs($tSize[4] - $tSize[0]);
        $tHeight = abs($tSize[5] - $tSize[1]);

        return [$tWidth, $tHeight];
    }

    /**
     * Generate the image.
     * 
     * @return string The image data.
     */
    private function generateImage(): string
    {
        
        $png = imagecreatetruecolor($this->width, $this->height + self::BAR_Y_OFFSET);

        // Colours
        $white = imagecolorallocate($png, 255, 255, 255);
        
        $borderColor = imagecolorallocate($png, $this->borderColor[0], $this->borderColor[1], $this->borderColor[2]);
        $fillColor = imagecolorallocate($png, $this->fillColor[0], $this->fillColor[1], $this->fillColor[2]);
        $barColor = imagecolorallocate($png, $this->barColor[0], $this->barColor[1], $this->barColor[2]);
        $boundaryLabelsColor = imagecolorallocate($png, $this->boundaryLabelsColor[0], $this->boundaryLabelsColor[1], $this->boundaryLabelsColor[2]);
        $valueLabelColor = imagecolorallocate($png, $this->valueLabelColor[0], $this->valueLabelColor[1], $this->valueLabelColor[2]);

        imagefill($png, 0, 0, $white);

        // Main Border
        $this->drawRoundedBox(
            $png,
            0,
            self::BAR_Y_OFFSET,
            $this->width,
            $this->height,
            $this->cornerRadius,
            $borderColor,
            $this->borderWidth,
            $fillColor
        );

        // Value
        $startValue = $this->boundaries[0];
        $endValue = $this->boundaries[count($this->boundaries) - 1];
        $position = ($this->value - $startValue) / ($endValue - $startValue);

        $pointX = $this->limitPointX($this->width * $position);

        // Boundary labels
        $totalLabels = count($this->boundaryLabels);
        if ($totalLabels > 0) {
            $labelWidth = $this->width / ($totalLabels + 1);
            for ($labelIndex = 0; $labelIndex < $totalLabels; $labelIndex++) {
                $labelX = ($labelIndex + 1) * $labelWidth;
                imagefilledrectangle($png, $labelX - 1, self::BAR_Y_OFFSET, $labelX + 1, self::BAR_Y_OFFSET - 8, $borderColor);

                $labelSize = $this->fontTextSize($this->font, self::BOUNDARY_LABEL_FONT_SIZE, $this->boundaryLabels[$labelIndex]);
                $textStartX = $labelX - ($labelSize[0] / 2);
                $textStartY = self::BAR_Y_OFFSET - 8 - ($labelSize[1] / 2);

                imagettftext($png, self::BOUNDARY_LABEL_FONT_SIZE, 0, $textStartX, $textStartY, $boundaryLabelsColor, self::FONTS[$this->font], $this->boundaryLabels[$labelIndex]);
            }
        }

        // Bar colour
        $this->fillRoundedRect(
            $png,
            0,
            self::BAR_Y_OFFSET,
            $pointX + (self::VALUE_BOX_HALF_WIDTH) + $this->borderWidth,
            $this->height,
            $this->cornerRadius,
            $barColor
        );

        // Value box
        $valueBoxX = $pointX - (self::VALUE_BOX_HALF_WIDTH);
        $valueBoxY = self::BAR_Y_OFFSET + $this->borderWidth;
        $valueBoxHeight = $this->height - ($this->borderWidth*2);

        // Value box background
        $this->fillRoundedRect(
            $png,
            $valueBoxX,
            $valueBoxY,
            self::VALUE_BOX_WIDTH,
            $valueBoxHeight,
            max(0, $this->cornerRadius - $this->borderWidth),
            $fillColor
        );

        // Value box text

        $valueSize = $this->fontTextSize($this->font, self::VALUE_BOX_VALUE_FONT_SIZE, $this->value);
        $unitSize = $this->fontTextSize($this->font, self::VALUE_BOX_UNIT_FONT_SIZE, $this->unit);
        $totalTextWidth = $valueSize[0] + $unitSize[0] + 2;
        $textStartX = $pointX - ($totalTextWidth / 2);
        $textStartY = $valueBoxY + ($valueBoxHeight/2) + ($valueSize[1]/2);

        imagettftext($png, self::VALUE_BOX_VALUE_FONT_SIZE, 0, $textStartX, $textStartY, $valueLabelColor, self::FONTS[$this->font], $this->value);
        imagettftext($png, self::VALUE_BOX_UNIT_FONT_SIZE, 0, $textStartX + 2 +$valueSize[0], $textStartY, $valueLabelColor, self::FONTS[$this->font], $this->unit);

        // Get the image data
        ob_start();
        imagepng($png);
        $imagedata = ob_get_clean();

        return $imagedata;
    }

    /**
     * Get the image as a base64 encoded string.
     * 
     * @return string The base64 encoded image.
     */
    public function getB64Image(): string
    {
        $image = $this->generateImage();
    
        return 'data:image/png;base64,' . base64_encode($image);
    }
}
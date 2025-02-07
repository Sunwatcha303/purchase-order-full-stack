<?php
class Dashboard_Repo
{
    static public function createBarChart($title, $productNames, $totalRevenue)
    {
        $fontFile = __DIR__ . '/../font/Sarabun-Regular.ttf';
        if (!file_exists($fontFile)) {
            $fontFile = null;
        }

        $width = 700;
        $height = 500;
        $padding = 100;

        $image = imagecreate($width, $height);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $barColor = imagecolorallocate($image, 0, 0, 255);
        $lineColor = imagecolorallocate($image, 0, 0, 0);

        $titleFontSize = 16; // Font size for title
        $titleX = $width / 2 - 90; // X position for the title (centered horizontally)
        $titleY = $padding / 2; // Y position for the title (near the top)

        // Add title to the chart
        imagettftext($image, $titleFontSize, 0, $titleX, $titleY, $lineColor, $fontFile, $title);

        $barWidth = ($width - $padding * 2) / count($productNames);
        $maxRevenue = max($totalRevenue) ?: 1;

        // Draw Y-Axis numbers and grid lines
        $numTicks = 5;
        $step = ceil($maxRevenue / $numTicks);

        for ($i = 0; $i <= $numTicks; $i++) {
            $y = $height - $padding - ($i * ($height - 2 * $padding) / $numTicks);
            imageline($image, $padding, $y, $width - $padding, $y, $lineColor);
            $label = $i * $step;
            if ($fontFile) {
                imagettftext($image, 10, 0, 10 + 20, $y + 5, $lineColor, $fontFile, $label);
            } else {
                imagestring($image, 4, 10, $y - 6, $label, $lineColor);
            }
        }

        // Draw X and Y Axis
        imageline($image, $padding, $height - $padding, $width - $padding, $height - $padding, $lineColor); // X-Axis
        imageline($image, $padding, $padding, $padding, $height - $padding, $lineColor); // Y-Axis

        // Draw bars and rotated product names
        foreach ($totalRevenue as $index => $revenue) {
            $barHeight = ($revenue / $maxRevenue) * ($height - 2 * $padding);
            $x1 = $index * $barWidth + $padding * 1.01;
            $x2 = $x1 + $barWidth - 10;
            $y1 = $height - $padding - $barHeight;
            $y2 = $height - $padding;

            imagefilledrectangle($image, $x1, $y1, $x2, $y2, $barColor);

            // Rotate product name 90 degrees
            $labelX = $x1 + ($barWidth / 2) - 10; // Centered under the bar
            $labelY = $height - $padding + 10; // Position further below X-axis

            if ($fontFile) {
                imagettftext($image, 10, -45, $labelX, $labelY, $lineColor, $fontFile, $productNames[$index]);
            } else {
                imagestringup($image, 3, $labelX, $labelY, $productNames[$index], $lineColor);
            }
        }


        // Output chart as a base64 image
        ob_start();
        imagepng($image);
        $barImageData = ob_get_contents();
        ob_end_clean();

        return base64_encode($barImageData);
    }

    static public function createPieChart($title, $productNames, $totalQuantities)
    {
        $fontFile = __DIR__ . '/../font/Sarabun-Regular.ttf';
        if (!file_exists($fontFile)) {
            $fontFile = null;
        }

        $width = 600;
        $height = 500;
        $image = imagecreate($width, $height);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $colors = [
            imagecolorallocate($image, 255, 0, 0),    // Red
            imagecolorallocate($image, 0, 255, 0),    // Green
            imagecolorallocate($image, 0, 0, 255),    // Blue
            imagecolorallocate($image, 255, 255, 0),  // Yellow
            imagecolorallocate($image, 255, 165, 0),  // Orange
            imagecolorallocate($image, 128, 0, 128),  // Purple
            imagecolorallocate($image, 0, 255, 255),  // Cyan
            imagecolorallocate($image, 255, 192, 203),// Pink
            imagecolorallocate($image, 165, 42, 42),  // Brown
            imagecolorallocate($image, 0, 128, 128),  // Teal
            imagecolorallocate($image, 75, 0, 130),   // Indigo
            imagecolorallocate($image, 192, 192, 192),// Silver
            imagecolorallocate($image, 128, 128, 0),  // Olive
            imagecolorallocate($image, 255, 105, 180),// Hot Pink
            imagecolorallocate($image, 173, 216, 230) // Light Blue
        ];

        $black = imagecolorallocate($image, 0, 0, 0);

        $totalSales = array_sum($totalQuantities);
        $startAngle = 0;
        $centerX = $width / 2 - 100;
        $centerY = $height / 2;
        $radius = 150;

        // Set the top right position for the labels
        $labelXStart = $width - 160;  // X-position for labels
        $labelYStart = 150;            // Y-position for labels
        $labelYOffset = 20;           // Vertical offset between labels

        $titleFontSize = 16; // Font size for title
        $titleX = $width / 2 - 160; // X position for the title (centered horizontally)
        $titleY = 40; // Y position for the title (near the top)

        // Add title to the chart
        imagettftext($image, $titleFontSize, 0, $titleX, $titleY, $black, $fontFile, $title);

        // Draw pie slices
        foreach ($totalQuantities as $index => $quantity) {
            $angle = ($quantity / $totalSales) * 360;
            $color = $colors[$index % count($colors)];

            // Draw pie slice
            imagefilledarc($image, $centerX, $centerY, 300, 300, $startAngle, $startAngle + $angle, $color, IMG_ARC_PIE);

            // Calculate the midpoint angle of the slice
            $midAngle = deg2rad($startAngle + $angle / 2);
            $textX = $centerX + cos($midAngle) * ($radius / 1.5); // Position X inside the slice
            $textY = $centerY + sin($midAngle) * ($radius / 1.5); // Position Y inside the slice

            // Calculate percentage
            $percentage = round(($quantity / $totalSales) * 100, 1);
            $label = "{$percentage}%";

            // Draw the percentage text inside the pie slice
            imagettftext($image, 12, 0, $textX, $textY, $black, $fontFile, $label);

            // Update the starting angle for the next slice
            $startAngle += $angle;
        }

        // Draw the labels with colored rectangles at the top-right
        $labelYPosition = $labelYStart;
        $rectangleWidth = 20;  // Width of the colored rectangle
        $rectangleHeight = 10; // Height of the colored rectangle
        foreach ($totalQuantities as $index => $quantity) {
            $percentage = round(($quantity / $totalSales) * 100, 1);
            $label = "{$productNames[$index]} ({$percentage}%)";

            // Draw the colored rectangle next to the label
            imagefilledrectangle($image, $labelXStart - $rectangleWidth - 5, $labelYPosition - 10, $labelXStart - 5, $labelYPosition + $rectangleHeight - 10, $colors[$index % count($colors)]);

            // Draw the label text in the top-right corner
            imagettftext($image, 10, 0, $labelXStart, $labelYPosition, $black, $fontFile, $label);

            // Move down for the next label
            $labelYPosition += $labelYOffset;
        }

        // Output pie chart as base64 image
        ob_start();
        imagepng($image);
        $pieImageData = ob_get_contents();
        ob_end_clean();

        return base64_encode($pieImageData);
    }

}

?>
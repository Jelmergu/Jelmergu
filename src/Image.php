<?php
/**
 * @author    Jelmer Wijnja <info@jelmerwijnja.nl>
 * @copyright jelmerwijnja.nl
 * @since     1.0.7
 * @version   1.0
 *
 * @package   Jelmergu/Jelmergu
 */

namespace Jelmergu;

/**
 * A Image class, implementing some GD functions
 *
 * This class is a wrapper for some GD functions
 *
 * @package Jelmergu/Jelmergu
 */
class Image
{
    private $image;
    private $formats = ["png" => "imagepng", "gif" => "imagegif", "jpeg" => "imagejpeg"];

    /**
     * Image constructor.
     *
     * @since   1.0.7
     * @version 1.0
     *
     * @param string $image The image to load
     * @param bool   $file  True when $image is a filename, false if $image is a string
     */
    public function __construct(string $image, bool $file = true)
    {
        if ($file === true && file_exists($image) === true) {
            $image = file_get_contents($image);
        }
        $this->image = imagecreatefromstring($image);

    }

    /**
     * Get the width of the current image
     *
     * @since   1.0.7
     * @version 1.0
     *
     * @return int
     */
    public function getWidth()
    {
        return imagesx($this->image);
    }

    /**
     * Get the height of the current image
     *
     * @since   1.0.7
     * @version 1.0
     *
     * @return int
     */
    public function getHeight()
    {
        return imagesy($this->image);
    }

    /**
     * Makes the image bigger while preserving aspect ratio, unless both width and height are set
     *
     * @since   1.0.7
     * @version 1.0
     *
     * @param int|null $width  New width to resize to, or null if only the height should be a set value
     * @param int|null $height New height to resize to, or null if only the width should be a set value
     *
     * @return void
     */
    public function upscalePreserveAR(int $width = null, int $height = null)
    {
        $width = is_null($width) === true || $width < $this->getWidth() ? null : $width;
        $height = is_null($height) === true || $height < $this->getHeight() ? null : $height;
        if (is_null($width) === false || is_null($height) === false) {
            $this->resizePreserveAR($width, $height);
        }
    }

    /**
     * Makes the image smaller while preserving aspect ratio, unless both width and height are set
     *
     * @since   1.0.7
     * @version 1.0
     *
     * @param int|null $width  New width to resize to, or null if only the height should be a set value
     * @param int|null $height New height to resize to, or null if only the width should be a set value
     *
     * @return void
     */
    public function downscalePreserveAR(int $width = null, int $height = null)
    {
        $width = is_null($width) === true || $width > $this->getWidth() ? null : $width;
        $height = is_null($height) === true || $height > $this->getHeight() ? null : $height;
        if (is_null($width) === false || is_null($height) === false) {
            $this->resizePreserveAR($width, $height);
        }
    }

    /**
     * Resizes the image while preserving aspect ratio, unless both width and height are set
     *
     * @since   1.0.7
     * @version 1.0
     *
     * @param int|null $width  New width to resize to, or null if only the height should be a set value
     * @param int|null $height New height to resize to, or null if only the width should be a set value
     *
     * @return void
     */
    public function resizePreserveAR(int $width = null, int $height = null)
    {
        if (is_null($width) === false && is_null($height) === false) {
            $newHeight = $height;
            $newWidth = $width;

        } elseif (is_null($width) === false) {
            $newWidth = $width;
            $widthRatio = $this->getWidth() / $newWidth;
            $newHeight = $this->getHeight() / $widthRatio;
        } elseif (is_null($height) === false) {
            $newHeight = $height;
            $heightRatio = $this->getHeight() / $newHeight;
            $newWidth = $this->getWidth() / $heightRatio;
        } else {
            return;
        }
        $this->image = imagescale($this->image, $newWidth, $newHeight);
    }

    /**
     * Get the expected file size for specified format, or all formats
     *
     * @since   1.0.7
     * @version 1.0
     *
     * @param string|null $format The format to get the expected size of
     *
     * @return array|int
     */
    public function getSize(string $format = null)
    {
        if (in_array($format, array_flip($this->formats)) === true) {
            ob_start();
            $this->formats[$format]($this->image);

            return mb_strlen(ob_get_clean(), "8bit");
        } else {
            $return = [];
            foreach ($this->formats as $key => $format) {
                ob_start();
                $format($this->image);
                $return[$key] = mb_strlen(ob_get_clean(), '8bit');
            }

            return $return;
        }

    }

    /**
     * Provide debugInfo
     *
     * @since   1.0.7
     * @version 1.0
     *
     * @return array
     */
    public function __debugInfo()
    {
        $info = [
            "width"  => $this->getWidth(),
            "height" => $this->getHeight(),
            "size"   => $this->getSize(),
        ];

        return $info;
    }

    public function __destruct()
    {
        imagedestroy($this->image);
    }
}
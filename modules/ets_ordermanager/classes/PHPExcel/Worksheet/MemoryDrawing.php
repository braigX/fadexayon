<?php
/**
 * 2007-2023 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2023 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

class PHPExcel_Worksheet_MemoryDrawing extends PHPExcel_Worksheet_BaseDrawing implements PHPExcel_IComparable
{
    /* Rendering functions */
    const RENDERING_DEFAULT = 'imagepng';
    const RENDERING_PNG     = 'imagepng';
    const RENDERING_GIF     = 'imagegif';
    const RENDERING_JPEG    = 'imagejpeg';

    /* MIME types */
    const MIMETYPE_DEFAULT  = 'image/png';
    const MIMETYPE_PNG      = 'image/png';
    const MIMETYPE_GIF      = 'image/gif';
    const MIMETYPE_JPEG     = 'image/jpeg';

    /**
     * Image resource
     *
     * @var resource
     */
    private $imageResource;

    /**
     * Rendering function
     *
     * @var string
     */
    private $renderingFunction;

    /**
     * Mime type
     *
     * @var string
     */
    private $mimeType;

    /**
     * Unique name
     *
     * @var string
     */
    private $uniqueName;

    /**
     * Create a new PHPExcel_Worksheet_MemoryDrawing
     */
    public function __construct()
    {
        // Initialise values
        $this->imageResource     = null;
        $this->renderingFunction = self::RENDERING_DEFAULT;
        $this->mimeType          = self::MIMETYPE_DEFAULT;
        $this->uniqueName        = md5(rand(0, 9999). time() . rand(0, 9999));

        // Initialize parent
        parent::__construct();
    }

    /**
     * Get image resource
     *
     * @return resource
     */
    public function getImageResource()
    {
        return $this->imageResource;
    }

    /**
     * Set image resource
     *
     * @param    $value resource
     * @return PHPExcel_Worksheet_MemoryDrawing
     */
    public function setImageResource($value = null)
    {
        $this->imageResource = $value;

        if (!is_null($this->imageResource)) {
            // Get width/height
            $this->width  = imagesx($this->imageResource);
            $this->height = imagesy($this->imageResource);
        }
        return $this;
    }

    /**
     * Get rendering function
     *
     * @return string
     */
    public function getRenderingFunction()
    {
        return $this->renderingFunction;
    }

    /**
     * Set rendering function
     *
     * @param string $value
     * @return PHPExcel_Worksheet_MemoryDrawing
     */
    public function setRenderingFunction($value = PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT)
    {
        $this->renderingFunction = $value;
        return $this;
    }

    /**
     * Get mime type
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set mime type
     *
     * @param string $value
     * @return PHPExcel_Worksheet_MemoryDrawing
     */
    public function setMimeType($value = PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT)
    {
        $this->mimeType = $value;
        return $this;
    }

    /**
     * Get indexed filename (using image index)
     *
     * @return string
     */
    public function getIndexedFilename()
    {
        $extension = strtolower($this->getMimeType());
        $extension = explode('/', $extension);
        $extension = $extension[1];

        return $this->uniqueName . $this->getImageIndex() . '.' . $extension;
    }

    /**
     * Get hash code
     *
     * @return string    Hash code
     */
    public function getHashCode()
    {
        return md5(
            $this->renderingFunction .
            $this->mimeType .
            $this->uniqueName .
            parent::getHashCode() .
            __CLASS__
        );
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}

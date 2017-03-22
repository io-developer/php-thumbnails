<?php

namespace iodev\Lib\Thumbnails;

use iodev\Lib\Thumbnails\Areas\Area;
use iodev\Lib\Thumbnails\Watermarks\IWatermark;

/**
 * @author Sergey Sedyshev
 */
class Thumbnail
{
    /**
     * @param string $name
     * @param int $width
     * @param int $height
     * @param string $mode
     * @param ThumbnailFormat $format
     * @param IWatermark $watermark
     */
    public function __construct( $name, $width, $height, $mode, ThumbnailFormat $format, $watermark=null )
    {
        $this->_name = $name;
        $this->_width = (int)$width;
        $this->_height = (int)$height;
        $this->_mode = $mode;
        $this->_format = $format;
        $this->_watermark = $watermark;
    }

    
    /** @var string */
    private $_name;
    
    /** @var int */
    private $_width;
    
    /** @var int */
    private $_height;
    
    /** @var string */
    private $_mode;
    
    /** @var ThumbnailFormat */
    private $_format;
    
    /** @var IWatermark */
    private $_watermark;
    
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->_width;
    }
    
    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->_height;
    }
    
    /**
     * @return string
     */
    public function getMode()
    {
        return $this->_mode;
    }
    
    /**
     * @return ThumbnailFormat
     */
    public function getFormat()
    {
        return $this->_format;
    }
    
    /**
     * @return IWatermark
     */
    public function getWatermark()
    {
        return $this->_watermark;
    }

    /**
     * @return Area
     */
    public function toArea()
    {
        $area = new Area($this->_name);
        $area->baseWidth = $this->_width;
        $area->baseHeight = $this->_height;
        return $area;
    }
}

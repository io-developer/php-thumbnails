<?php

namespace iodev\Lib\Thumbnails;

use iodev\Lib\Thumbnails\Watermarks\AdaptiveWatermark;
use iodev\Lib\Thumbnails\Watermarks\CommonWatermark;
use iodev\Lib\Thumbnails\Watermarks\IWatermark;

/**
 * @author Sergey Sedyshev
 */
class ThumbnailBuilder
{
    /**
     * @return ThumbnailBuilder
     */
    public static function create()
    {
        return new ThumbnailBuilder();
    }
    
    public function __construct()
    {
        $this->_width = 256;
        $this->_height = 256;
        $this->_mode = ThumbnailMode::CONTAIN;
        $this->_format = ThumbnailFormat::createSource();
        $this->_watermark = null;
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
     * @param string $name
     * @return $this
     */
    public function name( $name )
    {
        $this->_name = $name;
        return $this;
    }
    
    /**
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function size( $width, $height )
    {
        $this->_width = (int)$width;
        $this->_height = (int)$height;
        return $this;
    }
    
    /**
     * @param string $mode
     * @return $this
     */
    public function mode( $mode )
    {
        $this->_mode = $mode;
        return $this;
    }
    
    /**
     * @return $this
     */
    public function modeContain()
    {
        $this->_mode = ThumbnailMode::CONTAIN;
        return $this;
    }
    
    /**
     * @return $this
     */
    public function modeCover()
    {
        $this->_mode = ThumbnailMode::COVER;
        return $this;
    }
    
    /**
     * @return $this
     */
    public function modeArea()
    {
        $this->_mode = ThumbnailMode::AREA;
        return $this;
    }
    
    /**
     * @param ThumbnailFormat $format
     * @return $this
     */
    public function format( ThumbnailFormat $format )
    {
        $this->_format = $format;
        return $this;
    }
    
    /**
     * @param int $quality
     * @return $this
     */
    public function formatSource( $quality=100 )
    {
        $this->_format = ThumbnailFormat::createSource((int)$quality);
        return $this;
    }
    
    /**
     * @param int $quality
     * @return $this
     */
    public function formatJpeg( $quality=100 )
    {
        $this->_format = ThumbnailFormat::createJpeg((int)$quality);
        return $this;
    }
    
    /**
     * @return $this
     */
    public function formatPng()
    {
        $this->_format = ThumbnailFormat::createPng();
        return $this;
    }
    
    /**
     * @param string $file
     * @param int $left
     * @param int $top
     * @param int $right
     * @param int $bottom
     * @return $this
     */
    public function watermarkCommon( $file, $left=null, $top=null, $right=10, $bottom=10 )
    {
        $this->_watermark = new CommonWatermark($file, $left, $top, $right, $bottom);
        return $this;
    }
    
    /**
     * @param string $file
     * @param double $u
     * @param double $v
     * @param double $areaFactor
     * @return $this
     */
    public function watermarkAdaptive( $file, $u=0.9, $v=0.9, $areaFactor=0.25 )
    {
        $this->_watermark = new AdaptiveWatermark($file, $u, $v, $areaFactor);
        return $this;
    }
    
    /**
     * @return Thumbnail
     */
    public function toThumbnail()
    {
        return new Thumbnail(
            $this->_name
            , $this->_width
            , $this->_height
            , $this->_mode
            , $this->_format
            , $this->_watermark
        );
    }
}

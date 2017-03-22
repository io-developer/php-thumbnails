<?php

namespace iodev\Lib\Thumbnails;

/**
 * @author Sergey Sedyshev
 */
class ThumbnailFormat
{
    /**
     * @param string $type
     * @param int $quality
     * @return ThumbnailFormat
     */
    public static function fromType( $type, $quality=100 )
    {
        return new ThumbnailFormat($type, $quality);
    }
    
    /**
     * @param int $quality
     * @return ThumbnailFormat
     */
    public static function createSource( $quality=100 )
    {
        return self::fromType(ThumbnailFormatType::SOURCE, $quality);
    }
    
    /**
     * @param int $quality
     * @return ThumbnailFormat
     */
    public static function createJpeg( $quality=100 )
    {
        return self::fromType(ThumbnailFormatType::JPEG, $quality);
    }
    
    /**
     * @param int $quality
     * @return ThumbnailFormat
     */
    public static function createPng( $quality=100 )
    {
        return self::fromType(ThumbnailFormatType::PNG, $quality);
    }
    
    /**
     * @param int $quality
     * @return ThumbnailFormat
     */
    public static function createGif( $quality=100 )
    {
        return self::fromType(ThumbnailFormatType::GIF, $quality);
    }
    
    
    /**
     * @param string $type
     * @param int $quality
     */
    public function __construct( $type, $quality=100 )
    {
        $this->_type = $type;
        $this->_quality = (int)$quality;
    }


    /** @var string */
    private $_type;
    
    /** @var int */
    private $_quality = 100;
    
    
    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return int
     */
    public function getQuality()
    {
        return $this->_quality;
    }

    /**
     * @return string
     */
    public function toExtension()
    {
        if ($this->_type == ThumbnailFormatType::JPEG) {
            return ".jpg";
        }
        if ($this->_type == ThumbnailFormatType::PNG) {
            return ".png";
        }
        if ($this->_type == ThumbnailFormatType::GIF) {
            return ".gif";
        }
        return "";
    }
}

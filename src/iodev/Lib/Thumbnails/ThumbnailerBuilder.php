<?php

namespace iodev\Lib\Thumbnails;

use iodev\Lib\Thumbnails\Resolvers\CommonResolver;
use iodev\Lib\Thumbnails\Resolvers\IResolver;

/**
 * @author Sergey Sedyshev
 */
class ThumbnailerBuilder
{
    /**
     * @return ThumbnailerBuilder
     */
    public static function create()
    {
        return new ThumbnailerBuilder();
    }
    
    
    public function __construct()
    {
    }
    
    
    /** @var Thumbnail[] */
    private $_thumbnails = [];
    
    /** @var string */
    private $_primaryName = "";
    
    /** @var IResolver */
    private $_resolver;
    
    
    /**
     * @param IResolver $resolver
     * @return ThumbnailerBuilder
     */
    public function setResolver( IResolver $resolver )
    {
        $this->_resolver = $resolver;
        return $this;
    }
    
    /**
     * @param string $docrootDir
     * @param string $baseDir
     * @param int $mode
     * @return ThumbnailerBuilder
     */
    public function setCommonResolver( $docrootDir, $baseDir, $mode=0755 )
    {
        $this->_resolver = new CommonResolver($docrootDir, $baseDir, $mode);
        return $this;
    }
    
    /**
     * @param string $name
     * @return ThumbnailerBuilder
     */
    public function setPrimaryThumbnailName( $name )
    {
        $this->_primaryName = $name;
        return $this;
    }
    
    /**
     * @param Thumbnail $t
     * @return ThumbnailerBuilder
     */
    public function addThumbnail( Thumbnail $t )
    {
        $this->_thumbnails[] = $t;
        return $this;
    }
    
    /**
     * @return Thumbnailer
     */
    public function toThumbnailer()
    {
        return new Thumbnailer(
            new ThumbnailSet($this->_thumbnails, $this->_primaryName)
            , $this->_resolver
        );
    }
}

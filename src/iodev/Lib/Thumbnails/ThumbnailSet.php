<?php

namespace iodev\Lib\Thumbnails;

use iodev\Lib\Thumbnails\Areas\AreaSet;

/**
 * @author Sergey Sedyshev
 */
class ThumbnailSet
{
    /**
     * @param Thumbnail[] $thumbnails
     * @param string $primaryName
     */
    public function __construct( $thumbnails, $primaryName="" )
    {
        $this->_dict = [];
        foreach ($thumbnails as $thumbnail) {
            $this->_dict[$thumbnail->getName()] = $thumbnail;
        }
        
        $this->_primaryName = $primaryName;
    }
    
    
    /** @var Thumbnail[] */
    private $_dict;
    
    /** @var string */
    private $_primaryName;
    
    
    /**
     * @return Thumbnail[]
     */
    public function getDict()
    {
        return $this->_dict;
    }
    
    /**
     * @return Thumbnail[]
     */
    public function getSecondaryDict()
    {
        $d = $this->_dict;
        unset($d[$this->_primaryName]);
        return $d;
    }
    
    /**
     * @return Thumbnail
     */
    public function getPrimary()
    {
        return $this->_dict[$this->_primaryName];
    }
    
    /**
     * @return string
     */
    public function getPrimaryName()
    {
        return $this->_primaryName;
    }
    
    /**
     * @param string $name
     * @return Thumbnail
     */
    public function getByName( $name )
    {
        return $this->_dict[$name];
    }
    
    /**
     * @param int $imageWidth
     * @param int $imageHeight
     * @return AreaSet
     */
    public function toAreaSet( $imageWidth=0, $imageHeight=0 )
    {
        $areas = [];
        foreach ($this->_dict as $thumb) {
            if ($thumb->getMode() == ThumbnailMode::AREA) {
                $areas[] = $thumb->toArea();
            }
        }
        
        $set = new AreaSet($areas, $imageWidth, $imageHeight);
        $set->fitAreas();
        
        return $set;
    }
}

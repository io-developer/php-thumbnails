<?php

namespace iodev\Lib\Thumbnails\Areas;

/**
 * @author Sergey Sedyshev
 */
class AreaSet
{
    /**
     * @param Area[] $areas
     */
    public function __construct( $areas, $srcWidth=0, $srcHeight=0 )
    {
        $this->_dict = [];
        foreach ($areas as $area) {
            $this->_dict[$area->getName()] = $area;
        }
        
        $this->_srcWidth = max(0, (int)$srcWidth);
        $this->_srcHeight = max(0, (int)$srcHeight);
    }
    
    
    /** @var Area[] */
    private $_dict;
    
    /** @var int */
    private $_srcWidth;
    
    /** @var int */
    private $_srcHeight;
    
    
    /**
     * @return Area[]
     */
    public function dict()
    {
        return $this->_dict;
    }
    
    /**
     * @param string $name
     * @return Area
     */
    public function getAreaByName( $name )
    {
        return $this->_dict[$name];
    }
    
    /**
     * @return int
     */
    public function srcWidth()
    {
        return $this->_srcWidth;
    }
    
    /**
     * @return int
     */
    public function srcHeight()
    {
        return $this->_srcHeight;
    }
    
        /**
     * @return bool
     */
    public function isNonzeroSized()
    {
        return $this->_srcWidth > 0 && $this->_srcHeight > 0;
    }
    
    /**
     * @return AreaSet
     */
    public function fitAreas()
    {
        if ($this->isNonzeroSized()) {
            foreach ($this->_dict as $area) {
                $area->fitToSize($this->_srcWidth, $this->_srcHeight);
            }
        }
        return $this;
    }
    
    /**
     * @param AreaSet $secondary
     * @return AreaSet
     */
    public function smartMergeWith( AreaSet $secondary )
    {
        $set = clone $this;
        foreach ($secondary->dict() as $name => $dst) {
            $src = $this->_dict[$name];
            if (!$src) {
                continue;
            }
            if ($dst->inBox($set->srcWidth(), $set->srcHeight())) {
                $dst = clone $dst;
                $dst->baseWidth = $src->baseWidth;
                $dst->baseHeight = $src->baseHeight;
                $set->_dict[$name] = $dst;
            }
        }
        return $set;
    }
}

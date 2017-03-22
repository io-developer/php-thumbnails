<?php

namespace iodev\Lib\Thumbnails\Areas;

/**
 * @author Sergey Sedyshev
 */
class Area
{
    /**
     * @param string $name
     */
    public function __construct( $name="" )
    {
        $this->_name = $name;
    }
    
    
    /** @var string */
    private $_name;
    
    /** @var int */
    public $baseWidth = 0;
    
    /** @var int */
    public $baseHeight = 0;
    
    /** @var int */
    public $x0 = 0;
    
    /** @var int */
    public $y0 = 0;
    
    /** @var int */
    public $x1 = 0;
    
    /** @var int */
    public $y1 = 0;
    
    
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
    public function left()
    {
        return min($this->x0, $this->x1);
    }
    
    /**
     * @return int
     */
    public function top()
    {
        return min($this->y0, $this->y1);
    }
    
    /**
     * @return int
     */
    public function right()
    {
        return $this->left() + $this->width();
    }
    
    /**
     * @return int
     */
    public function bottom()
    {
        return $this->top() + $this->height();
    }
    
    /**
     * @return int
     */
    public function width()
    {
        return abs($this->x1 - $this->x0);
    }
    
    /**
     * @return int
     */
    public function height()
    {
        return abs($this->y1 - $this->y0);
    }
    
    /**
     * @param string $width
     * @param string $height
     * @return boolean
     */
    public function inBox( $width, $height )
    {
        if ($this->width() > $width || $this->height() > $height) {
            return false;
        }
        if ($this->left() < 0 || $this->top() < 0) {
            return false;
        }
        if ($this->right() > $width || $this->bottom() > $height) {
            return false;
        }
        return true;
    }

    /**
     * @param int $width
     * @param int $height
     * @return CropdataItem
     */
    public function fitToSize( $width, $height )
    {
        $w = (int)$width;
        $h = (int)$height;
        
        $sw = (int)$this->baseWidth;
        $sh = (int)$this->baseHeight;

        $k = min($w / $sw, $h / $sh);
        
        $this->x0 = (int)(0.5 * ($w - $sw * $k));
        $this->y0 = (int)(0.5 * ($h - $sh * $k));
        $this->x1 = $this->x0 + (int)($sw * $k);
        $this->y1 = $this->y0 + (int)($sh * $k);
        
        return $this;
    }
}

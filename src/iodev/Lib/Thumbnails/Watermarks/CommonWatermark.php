<?php

namespace iodev\Lib\Thumbnails\Watermarks;

/**
 * @author Sergey Sedyshev
 */
class CommonWatermark implements IWatermark
{
    public function __construct( $file, $left=null, $top=null, $right=10, $bottom=10 )
    {
        $this->_file = $file;
        $this->_left = $left;
        $this->_top = $top;
        $this->_right = $right;
        $this->_bottom = $bottom;
    }
    
    
    /** @var string */
    private $_file;
    
    /** @var int */
    public $_left;
    
    /** @var int */
    private $_top;
    
    /** @var int */
    private $_right;
    
    /** @var int */
    private $_bottom;
    
    
    /**
     * @return string
     */
    public function getFile()
    {
        return $this->_file;
    }
    
    /**
     * @param int $watermarkW
     * @param int $watermarkH
     * @param int $containerW
     * @param int $containerH
     * @return int
     */
    public function calcLeft( $watermarkW, $watermarkH, $containerW, $containerH )
    {
        if (isset($this->_left)) {
            return (int)$this->_left;
        }
        if (isset($this->_right)) {
            return (int)$this->_right;
        }
        return 0;
    }
    
    /**
     * @param int $watermarkW
     * @param int $watermarkH
     * @param int $containerW
     * @param int $containerH
     * @return int
     */
    public function calcTop( $watermarkW, $watermarkH, $containerW, $containerH )
    {
        if (isset($this->_top)) {
            return (int)$this->_top;
        }
        if (isset($this->_bottom)) {
            return (int)$this->_bottom;
        }
        return 0;
    }
    
    /**
     * @param int $watermarkW
     * @param int $watermarkH
     * @param int $containerW
     * @param int $containerH
     * @return int
     */
    public function calcWidth( $watermarkW, $watermarkH, $containerW, $containerH )
    {
        return $watermarkW;
    }
    
    /**
     * @param int $watermarkW
     * @param int $watermarkH
     * @param int $containerW
     * @param int $containerH
     * @return int
     */
    public function calcHeight( $watermarkW, $watermarkH, $containerW, $containerH )
    {
        return $watermarkH;
    }
}

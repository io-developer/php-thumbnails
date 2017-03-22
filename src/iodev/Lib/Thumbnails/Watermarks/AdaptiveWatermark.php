<?php

namespace iodev\Lib\Thumbnails\Watermarks;

/**
 * @author Sergey Sedyshev
 */
class AdaptiveWatermark implements IWatermark
{
    public function __construct( $file, $u=0.9, $v=0.9, $areaFactor=0.25 )
    {
        $this->_file = $file;
        $this->_u = (double)$u;
        $this->_v = (double)$v;
        $this->_areaFactor = (double)$areaFactor;
    }
    
    
    /** @var string */
    private $_file;
    
    /** @var double */
    private $_u;
    
    /** @var double */
    private $_v;
    
    /** @var double */
    private $_areaFactor;
    
    
    /**
     * @return string
     */
    public function getFile()
    {
        return $this->_file;
    }
    
    /**
     * @return double
     */
    public function getU()
    {
        return $this->_u;
    }
    
    /**
     * @return double
     */
    public function getV()
    {
        return $this->_v;
    }
    
    /**
     * @return double
     */
    public function getAreaFactor()
    {
        return $this->_areaFactor;
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
        return $this->_u * ($containerW - $this->calcWidth($watermarkW, $watermarkH, $containerW, $containerH));
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
        return $this->_v * ($containerH - $this->calcHeight($watermarkW, $watermarkH, $containerW, $containerH));
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
        $watermarkS = $watermarkW * $watermarkH;
        $containerS = $containerW * $containerH;
        $ratio = sqrt($watermarkS / $containerS);
        return $watermarkW * $this->_areaFactor / $ratio;
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
        $watermarkS = $watermarkW * $watermarkH;
        $containerS = $containerW * $containerH;
        $ratio = sqrt($watermarkS / $containerS);
        return $watermarkH * $this->_areaFactor / $ratio;
    }
}

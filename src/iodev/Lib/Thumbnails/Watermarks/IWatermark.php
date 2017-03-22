<?php

namespace iodev\Lib\Thumbnails\Watermarks;

/**
 * @author Sergey Sedyshev
 */
interface IWatermark
{
    /**
     * @return string
     */
    function getFile();
    
    /**
     * @param int $watermarkW
     * @param int $watermarkH
     * @param int $containerW
     * @param int $containerH
     * @return int
     */
    function calcLeft( $watermarkW, $watermarkH, $containerW, $containerH );
    
    /**
     * @param int $watermarkW
     * @param int $watermarkH
     * @param int $containerW
     * @param int $containerH
     * @return int
     */
    function calcTop( $watermarkW, $watermarkH, $containerW, $containerH );
    
    /**
     * @param int $watermarkW
     * @param int $watermarkH
     * @param int $containerW
     * @param int $containerH
     * @return int
     */
    function calcWidth( $watermarkW, $watermarkH, $containerW, $containerH );
    
    /**
     * @param int $watermarkW
     * @param int $watermarkH
     * @param int $containerW
     * @param int $containerH
     * @return int
     */
    function calcHeight( $watermarkW, $watermarkH, $containerW, $containerH );
}

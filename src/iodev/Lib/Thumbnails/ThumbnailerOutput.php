<?php

namespace iodev\Lib\Thumbnails;

use iodev\Lib\Thumbnails\Areas\AreaSet;

/**
 * @author Sergey Sedyshev
 */
class ThumbnailerOutput
{
    /** @var ThumbnailOutput */
    public $primaryOutput = null;
    
    /** @var ThumbnailOutput[] */
    public $outputDict = [];
    
    /** @var AreaSet */
    public $areaSet = null;
    
    
    /**
     * @return string
     */
    public function getPrimaryPath()
    {
        return $this->primaryOutput->path;
    }
}

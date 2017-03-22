<?php

namespace iodev\Lib\Thumbnails;

use iodev\Lib\Thumbnails\Areas\Area;

/**
 * @author Sergey Sedyshev
 */
class ThumbnailOutput
{
    /** @var string */
    public $name = "";
    
    /** @var string */
    public $path = "";
    
    /** @var string */
    public $file = "";
    
    /** @var int */
    public $width = 0;
    
    /** @var int */
    public $height = 0;
    
    /** @var Area */
    public $area = null;
    
    
    /**
     * @return string
     */
    public function correctPath()
    {
        $this->path = $this->_replaceExtBasedOn($this->path, $this->file);
        return $this->path;
    }
    
    /**
     * @param string $path
     * @param string $basePath
     * @return string
     */
    private function _replaceExtBasedOn( $path, $basePath )
    {
        return (
                ($path !== basename($path))
                    ? rtrim(dirname($path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
                    : ""
            )
            . pathinfo($path, PATHINFO_FILENAME)
            . "."
            . ltrim(pathinfo($basePath, PATHINFO_EXTENSION), ".");
    }
}

<?php

namespace iodev\Lib\Thumbnails\Resolvers;

/**
 * @author Sergey Sedyshev
 */
class CommonResolver implements IResolver
{
    /**
     * @param string $docrootDir
     * @param string $basePath
     * @param int $mode
     */
    public function __construct( $docrootDir, $basePath="/tmp", $mode=0755 )
    {
        $this->_docrootDir = rtrim($docrootDir, DIRECTORY_SEPARATOR);
        $this->_basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
        $this->_mode = (int)$mode;
    }
    
    
    /** @var string */
    private $_docrootDir;
    
    /** @var string */
    private $_basePath;
    
    /** @var int */
    private $_mode;


    /**
     * @param string $path
     * @return string
     */
    public function resolveFile( $path )
    {
        return $this->_docrootDir . $path;
    }
    
    /**
     * @param string $path
     * @return string
     */
    public function resolveLocation( $path )
    {
        return $path;
    }
    
    /**
     * @param string $path
     * @param string $thumbnailName
     * @param string $thumbnailExt
     * @return string
     */
    public function resolveThumbnailedPath( $path, $thumbnailName, $thumbnailExt="" )
    {
        $p = empty($thumbnailName)
            ? $path
            : $this->_suffixFilename($path, "-" . $thumbnailName);
        
        $p = empty($thumbnailExt)
            ? $p
            : $this->_replaceExt($p, $thumbnailExt);
        
        return $p;
    }
    
    /**
     * @param string $matId
     * @return string
     */
    public function nextPathFor( $matId )
    {
        return $this->_basePath
            . DIRECTORY_SEPARATOR . $matId
            . DIRECTORY_SEPARATOR . "img" . time() . rand(1000, 9999) . ".tmp";
    }
    
    /**
     * @param string $path
     */
    public function mkdirForPath( $path )
    {
        $dir = pathinfo($this->resolveFile($path), PATHINFO_DIRNAME);
        if (!is_dir($dir)) {
            mkdir($dir, $this->_mode, true);
        }
    }
    
    /**
     * @param string $path
     * @param string $suffix
     * @return string
     */
    private function _suffixFilename( $path, $suffix )
    {
        return (
                ($path !== basename($path))
                    ? rtrim(dirname($path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
                    : ""
            )
            . pathinfo($path, PATHINFO_FILENAME)
            . $suffix
            . "."
            . ltrim(pathinfo($path, PATHINFO_EXTENSION), ".");
    }
    
    /**
     * @param string $path
     * @param string $newExt
     * @return string
     */
    private function _replaceExt( $path, $newExt )
    {
        return (
                ($path !== basename($path))
                    ? rtrim(dirname($path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
                    : ""
            )
            . pathinfo($path, PATHINFO_FILENAME)
            . "."
            . ltrim($newExt, ".");
    }
}

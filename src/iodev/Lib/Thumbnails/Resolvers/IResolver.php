<?php

namespace iodev\Lib\Thumbnails\Resolvers;

/**
 * @author Sergey Sedyshev
 */
interface IResolver
{
    /**
     * @param string $path
     * @return string
     */
    function resolveFile( $path );
    
    /**
     * @param string $path
     * @return string
     */
    function resolveLocation( $path );
    
    /**
     * @param string $path
     * @param string $thumbnailName
     * @param string $thumbnailExt
     * @return string
     */
    function resolveThumbnailedPath( $path, $thumbnailName, $thumbnailExt="" );
    
    /**
     * @param string $matId
     * @return string
     */
    function nextPathFor( $matId );
    
    /**
     * @param string $path
     */
    function mkdirForPath( $path );
}

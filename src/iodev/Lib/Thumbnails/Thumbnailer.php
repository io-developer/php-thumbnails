<?php

namespace iodev\Lib\Thumbnails;

use iodev\Lib\ImageTransform\ImageTransform;
use iodev\Lib\ImageTransform\ImageTransformFactory;
use iodev\Lib\Thumbnails\Areas\AreaHelper;
use iodev\Lib\Thumbnails\Areas\AreaSet;
use iodev\Lib\Thumbnails\Resolvers\IResolver;

/**
 * @author Sergey Sedyshev
 */
class Thumbnailer
{
    public function __construct( ThumbnailSet $set, IResolver $resolver )
    {
        $this->_set = $set;
        $this->_resolver = $resolver;
    }
    
    
    /** @var ThumbnailSet */
    private $_set;
    
    /** @var IResolver */
    private $_resolver;
    
    
    /**
     * @return ImageTransform
     */
    protected function createTransform()
    {
        return ImageTransformFactory::create();
    }
    
    /**
     * @return ThumbnailExporter
     */
    protected function createExporter()
    {
        return new ThumbnailExporter($this->createTransform(), $this->_set, $this->_resolver);
    }
    
    /**
     * @return ThumbnailSet
     */
    public function getThumbnailSet()
    {
        return $this->_set;
    }

    /**
     * @return IResolver
     */
    public function getResolver()
    {
        return $this->_resolver;
    }
    
    /**
     * @param string $path
     * @param AreaSet $secondary
     * @return AreaSet
     */
    public function mergeAreaSetFor( $path, AreaSet $secondary )
    {
        $w = 0;
        $h = 0;
        
        $file = $this->resolvePrimaryFile($path);
        if (is_file($file)) {
            $ctx = $this->createTransform()
                ->inputFile($this->resolvePrimaryFile($path))
                ->getContext();
            
            $w = $ctx->getWidth();
            $h = $ctx->getHeight();
        }
        
        return $this->_set
            ->toAreaSet($w, $h)
            ->smartMergeWith($secondary);
    }
    
    /**
     * @param string $path
     * @param string $serializedAreaSet
     * @return AreaSet
     */
    public function mergeSerializedAreaSetFor( $path, $serializedAreaSet )
    {
        return $this->mergeAreaSetFor($path, AreaHelper::parseAreaSet($serializedAreaSet));
    }


    /**
     * @param string $path
     * @param string $name
     * @return string
     */
    public function resolvePathByName( $path, $name )
    {
        $t = $this->_set->getByName($name);
        if ($t) {
            $f = $t->getFormat();
            return $this->_resolver->resolveThumbnailedPath($path, $name, $f->toExtension());
        }
        return $this->_resolver->resolveThumbnailedPath($path, $name);
    }
    
    /**
     * @param string $path
     * @param string $name
     * @return string
     */
    public function resolveLocationByName( $path, $name )
    {
        return $this->_resolver->resolveLocation(
            $this->resolvePathByName($path, $name)
        );
    }
    
    /**
     * @param string $path
     * @param string $name
     * @return string
     */
    public function resolveFileByName( $path, $name )
    {
        return $this->_resolver->resolveFile(
            $this->resolvePathByName($path, $name)
        );
    }
    
    /**
     * @param string $path
     * @return string
     */
    public function resolvePrimaryFile( $path )
    {
        return $this->_resolver->resolveFile(
            $this->resolvePathByName($path, $this->_set->getPrimaryName())
        );
    }
    
    /**
     * @param string $path
     * @return string[]
     */
    public function resolveAllFiles( $path )
    {
        $dict = [];
        foreach ($this->_set->getDict() as $name => $thumb) {
            $dict[$name] = $this->resolveFileByName($path, $name);
        }
        return $dict;
    }
    
    /**
     * @param string $path
     */
    public function deleteAllFiles( $path )
    {
        $files = $this->resolveAllFiles($path);
        foreach ($files as $file) {
            unlink($file);
        }
    }
    
    /**
     * @param string $matId
     * @param string $inputName
     * @param AreaSet $areaSet
     * @param bool $watermarkEnabled
     * @return ThumbnailerOutput
     */
    public function thumbnailFormInput( $matId, $inputName, $areaSet=null, $watermarkEnabled=true )
    {
        $tmpFiles = $this->_listTmpFilesFor($inputName);
        if (empty($tmpFiles)) {
            return null;
        }
        $tmpFile = $tmpFiles[0];
        return $this->thumbnailAndMoveUploadedFile($matId, $tmpFile, $areaSet, $watermarkEnabled);
    }
    
    /**
     * @param string $matId
     * @param string $inputName
     * @param bool $watermarkEnabled
     * @return ThumbnailerOutput[]
     */
    public function thumbnailFormInputMulti( $matId, $inputName, $watermarkEnabled=true )
    {
        $results = [];
        $tmpFiles = $this->_listTmpFilesFor($inputName);
        foreach ($tmpFiles as $tmpFile) {
            $results[] = $this->thumbnailAndMoveUploadedFile($matId, $tmpFile, $watermarkEnabled);
        }
        return $results;
    }
    
    /**
     * @param string $matId
     * @param string $tmpFile
     * @param AreaSet $areaSet
     * @param bool $watermarkEnabled
     * @return ThumbnailerOutput
     */
    public function thumbnailAndMoveUploadedFile( $matId, $tmpFile, $areaSet=null, $watermarkEnabled=true )
    {
        $matPath = $this->_resolver->nextPathFor($matId);
        $this->_resolver->mkdirForPath($matPath);
        
        $srcFile = $this->_resolver->resolveFile($matPath);
        $srcFile = $this->_replaceExt($srcFile, "temp");
        move_uploaded_file($tmpFile, $srcFile);
        
        $res = $this->createExporter()
            ->setWatermarkEnabled($watermarkEnabled)
            ->exportAll($matPath, $srcFile, $areaSet);
        
        unlink($srcFile);
        return $res;
    }
    
    /**
     * @param string $matId
     * @param string $srcFile
     * @param AreaSet $areaSet
     * @param bool $watermarkEnabled
     * @return ThumbnailerOutput
     */
    public function thumbnailFile( $matId, $srcFile, $areaSet=null, $watermarkEnabled=true )
    {
        $matPath = $this->_resolver->nextPathFor($matId);
        $this->_resolver->mkdirForPath($matPath);
        
        return $this->createExporter()
            ->setWatermarkEnabled($watermarkEnabled)
            ->exportAll($matPath, $srcFile, $areaSet);
    }
    
    /**
     * @param string $matId
     * @param string $path
     * @param AreaSet $areaSet
     * @param bool $watermarkEnabled
     * @return ThumbnailerOutput
     */
    public function reThumbnailPath( $matId, $path, $areaSet=null, $watermarkEnabled=true )
    {
        $matPath = $this->_resolver->nextPathFor($matId);
        $this->_resolver->mkdirForPath($matPath);
        
        $res = $this->createExporter()
            ->setWatermarkEnabled($watermarkEnabled)
            ->exportAll($matPath, $this->resolvePrimaryFile($path), $areaSet);
        
        $this->deleteAllFiles($path);
        return $res;
    }
    
    /**
     * @param string $inputName
     * @return string[]
     */
    private function _listTmpFilesFor( $inputName )
    {
        $byKey = $_FILES[$inputName];
        if (!$byKey) {
            return [];
        }
        $tmpName = $byKey["tmp_name"];
        if (!$tmpName) {
            return [];
        }
        $nonemptyFiles = [];
        $files = is_array($tmpName) ? $tmpName : [ $tmpName ];
        foreach ($files as $file) {
            if (!empty($file)) {
                $nonemptyFiles[] = $file;
            }
        }
        return $nonemptyFiles;
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

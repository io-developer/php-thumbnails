<?php

namespace iodev\Lib\Thumbnails;

use iodev\Lib\ImageTransform\ExportFormats\ExportFormat;
use iodev\Lib\ImageTransform\ImageTransform;
use iodev\Lib\ImageTransform\Output;
use iodev\Lib\Thumbnails\Areas\Area;
use iodev\Lib\Thumbnails\Areas\AreaSet;
use iodev\Lib\Thumbnails\Resolvers\IResolver;

/**
 * @author Sergey Sedyshev
 */
class ThumbnailExporter
{
    public function __construct( ImageTransform $transform, ThumbnailSet $set, IResolver $resolver )
    {
        $this->_transform = $transform;
        $this->_set = $set;
        $this->_resolver = $resolver;
    }
    
    
    /** @var ImageTransform */
    private $_transform;
    
    /** @var ThumbnailSet */
    private $_set;
    
    /** @var IResolver */
    private $_resolver;
    
    /** @var bool */
    private $_watermarkEnabled = true;
    
    
    /**
     * @param type $enabled
     * @return $this
     */
    public function setWatermarkEnabled( $enabled )
    {
        $this->_watermarkEnabled = (bool)$enabled;
        return $this;
    }
    
    /**
     * @param string $matPath
     * @param string $srcFile
     * @param AreaSet $areaSet
     * @return ThumbnailerOutput
     */
    public function exportAll( $matPath, $srcFile, $areaSet=null )
    {
        $t = $this->_transform;
        $t->inputFile($srcFile);
        $ctx = $t->getContext();
        
        $primaryAreaSet = $this->_set->toAreaSet($ctx->getWidth(), $ctx->getHeight());
        if ($areaSet) {
            $primaryAreaSet = $primaryAreaSet->smartMergeWith($areaSet);
        }
        
        $primaryThumb = $this->_set->getPrimary();
        $primaryOutput = $this->_exportThumbnail($t, $primaryThumb, $matPath, $primaryAreaSet);
        
        $finalAreaSet = $this->_set->toAreaSet($primaryOutput->width, $primaryOutput->height);
        if ($areaSet) {
            $finalAreaSet = $finalAreaSet->smartMergeWith($areaSet);
        }
        
        $outputDict = [];
        $outputDict[$primaryThumb->getName()] = $primaryOutput;
        
        foreach ($this->_set->getSecondaryDict() as $name => $thumb) {
            $outputDict[$name] = $this->_exportThumbnail($t->cloneTransform(), $thumb, $matPath, $finalAreaSet);
        }
        
        $result = new ThumbnailerOutput();
        $result->primaryOutput = $primaryOutput;
        $result->outputDict = $outputDict;
        $result->areaSet = $finalAreaSet;
        
        return $result;
    }
    
    /**
     * @param ImageTransform $t
     * @param Thumbnail $thumbnail
     * @param string $matPath
     * @param AreaSet $areaSet
     * @return ThumbnailOutput
     */
    private function _exportThumbnail( ImageTransform $t, Thumbnail $thumbnail, $matPath, AreaSet $areaSet )
    {
        $format = $thumbnail->getFormat();
        $path = $this->_resolver->resolveThumbnailedPath($matPath, $thumbnail->getName(), $format->toExtension());
        $dstFile = $this->_resolver->resolveFile($path);

        $area = $areaSet->getAreaByName($thumbnail->getName());
        
        $this->_transformThumbnailMode($t, $thumbnail, $area);
        $this->_transformThumbnailWatermark($t, $thumbnail);
        
        $transformOutput = new Output();
        if ($format->getType() == ThumbnailFormatType::SOURCE) {
            $t->exportFileWithInputFormat($dstFile, $format->getQuality(), $transformOutput);
        } else {
            $t->exportFileWithFormatExt(
                $dstFile
                , ExportFormat::fromType($format->getType(), $format->getQuality())
                , $transformOutput
            );
        }
        
        $output = new ThumbnailOutput();
        $output->name = $thumbnail->getName();
        $output->path = $path;
        $output->file = $transformOutput->file;
        $output->width = $transformOutput->width;
        $output->height = $transformOutput->height;
        $output->area = $area;
        $output->correctPath();
        
        return $output;
    }
    
    /**
     * @param ImageTransform $t
     * @param Thumbnail $thumbnail
     * @param Area $area
     */
    private function _transformThumbnailMode( ImageTransform $t, Thumbnail $thumbnail, $area=null )
    {
        $mode = $thumbnail->getMode();
        
        if ($mode == ThumbnailMode::AREA && !$area) {
            $mode = ThumbnailMode::COVER;
        }
        
        if ($mode == ThumbnailMode::AREA) {
            $t->cropCoord($area->left(), $area->top(), $area->right(), $area->bottom())
                ->fitOuterCrop($thumbnail->getWidth(), $thumbnail->getHeight());
        } elseif ($mode == ThumbnailMode::COVER) {
            $t->fitOuterCrop($thumbnail->getWidth(), $thumbnail->getHeight());
        } elseif ($mode == ThumbnailMode::CONTAIN) {
            $t->reduce($thumbnail->getWidth(), $thumbnail->getHeight());
        }
    }
    
    /**
     * @param ImageTransform $t
     * @param Thumbnail $thumbnail
     */
    private function _transformThumbnailWatermark( ImageTransform $t, Thumbnail $thumbnail )
    {
        $watermark = $thumbnail->getWatermark();
        if (!$watermark || !$this->_watermarkEnabled) {
            return;
        }
        
        $file = $watermark->getFile();
        $fileInfo = $t->readInfoFromFile($file);
        
        $watermarkW = $fileInfo->width;
        $watermarkH = $fileInfo->height;
        
        $containerW = $t->getContext()->getWidth();
        $containerH = $t->getContext()->getHeight();
        
        $x = $watermark->calcLeft($watermarkW, $watermarkH, $containerW, $containerH);
        $y = $watermark->calcTop($watermarkW, $watermarkH, $containerW, $containerH);
        $width = $watermark->calcWidth($watermarkW, $watermarkH, $containerW, $containerH);
        $height = $watermark->calcHeight($watermarkW, $watermarkH, $containerW, $containerH);
        
        $t->overlayFile($file, $x, $y, $width, $height);
    }
}

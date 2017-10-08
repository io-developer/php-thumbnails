# Thumbnails
PHP library for image thumbnaling and watermarking

## Requirements
PHP >= 5.4
- gd
- iodev\Lib\ImageTransform (https://github.com/io-developer/php-imagetransform)


## Usage

### Defining image types
```php
<?php

class MyImageType
{
    const ORIGINAL = "";
    const FULLSIZE = "fullsize";
    const WIDE = "wide";
    const SQUARE = "square";
}

```

### Defining thumbnailer creation
```php
<?php

use iodev\Lib\Thumbnails\ThumbnailBuilder;
use iodev\Lib\Thumbnails\Thumbnailer;
use iodev\Lib\Thumbnails\ThumbnailerBuilder;

class MyImageFactory
{
    /**
     * @return Thumbnailer
     */
    public static function createThumbnailer()
    {
        return ThumbnailerBuilder::create()
            ->setCommonResolver($_SERVER["DOCUMENT_ROOT"], "/files/content-by-id")
            ->setPrimaryThumbnailName(MyImageType::ORIGINAL)
            ->addThumbnail(
                ThumbnailBuilder::create()
                    ->name(MyImageType::ORIGINAL)
                    ->size(4096, 4096)
                    ->modeContain()
                    ->formatSource()
                    ->toThumbnail()
            )
            ->addThumbnail(
                ThumbnailBuilder::create()
                    ->name(MyImageType::FULLSIZE)
                    ->size(4096, 4096)
                    ->modeContain()
                    ->formatSource()
                    ->watermarkAdaptive("watermark.png")
                    ->toThumbnail()
            )
            ->addThumbnail(
                ThumbnailBuilder::create()
                    ->name(MyImageType::WIDE)
                    ->size(1280, 720)
                    ->modeCover()
                    ->formatJpeg(95)
                    ->watermarkAdaptive("watermark.png")
                    ->toThumbnail()
            )
            ->addThumbnail(
                ThumbnailBuilder::create()
                    ->name(MyImageType::SQUARE)
                    ->size(150, 150)
                    ->modeCover()
                    ->formatPng()
                    ->toThumbnail()
            )
            ->toThumbnailer();
    }
}
```

### Defining image class
```php
<?php

use iodev\Lib\Thumbnails\Thumbnailer;

class MyImage
{
    /** @var Thumbnailer */
    private static $_thumbnailer = null;
    
    /**
     * @return Thumbnailer
     */
    public static function getThumbnailer()
    {
        if (self::$_thumbnailer == null) {
            self::$_thumbnailer = MyImageFactory::createThumbnailer();
        }
        return self::$_thumbnailer;
    }
    
    
    /**
     * @param string $path
     */
    public function __construct( $path )
    {
        $m = new MyImage();
        $m->path = $path;
        return $m;
    }
    
    /** @var string */
    public $path = "";
    
    /**
     * @return string
     */
    public function toUri( $imageType="" )
    {
        return self::getThumbnailer()
            ->resolveLocationByName($this->path, $imageType);
    }
    
    /**
     * @return string
     */
    public function toFullsizeUri()
    {
        return $this->toUri(MyImageType::FULLSIZE);
    }
    
    /**
     * @return string
     */
    public function toWideUri()
    {
        return $this->toUri(MyImageType::WIDE);
    }
    
    /**
     * @return string
     */
    public function toSquareUri()
    {
        return $this->toUri(MyImageType::SQUARE);
    }
}
```

### Uploading on form submitting
```php
<?php

// For example: you need to thumbnail main image of some article. So content ID it's article ID :)
$contentId = 123;
$inputName = "image-file-form-input-name";
$areaSet = null;
$overlayWatermarks = true;

$thumbnailer = MyImage::getThumbnailer();
$result = $thumbnailer->thumbnailFormInput($contentId, $inputName, $areaSet, $overlayWatermarks);

// thumbnailed image path to store to somewhere
$path = $result->getPrimaryPath();

$myThumbnailedImage = new MyImage($path);

```

### Updating
```php
<?php

$contentId = 123;
$oldPath = "previous-generated-thumbnail-path.jpg";
$areaSet = null;
$overlayWatermarks = false;

$thumbnailer = MyImage::getThumbnailer();
$result = $thumbnailer->reThumbnailPath($contentId, $oldPath, $areaSet, $overlayWatermarks);

// thumbnailed image path to save
$newPath = $result->getPrimaryPath();

$myThumbnailedImage = new MyImage($newPath);

```

### Using thumbnailed images
```php
<?php

$image = new MyImage("saved-thumbnailed-path.jpg");

// original
echo $image->toUri();

// fullsized watermarked
echo $image->toFullsizeUri();

// wide watermarked
echo $image->toWideUri();

// square
echo $image->toSquareUri();

```

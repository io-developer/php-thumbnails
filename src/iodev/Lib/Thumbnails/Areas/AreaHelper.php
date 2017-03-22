<?php

namespace iodev\Lib\Thumbnails\Areas;

/**
 * @author Sergey Sedyshev
 */
class AreaHelper
{
    /**
     * @param string $s
     * @return AreaSet
     */
    public static function parseAreaSet( $s )
    {
        $areas = [];
        $typeds = explode(";", $s);
        foreach ($typeds as $typed) {
            $nameVals = explode("=", $typed);
            $area = self::parseArea($nameVals[0], $nameVals[1]);
			if ($area) {
				$areas[] = $area;
			}
        }
        return new AreaSet($areas);
    }
    
    /**
     * @param string[] $areaStringDict
     * @return Cropdata
     */
    public static function parseAreaDict( $areaStringDict )
    {
        $areas = [];
        foreach ($areaStringDict as $name => $datastr) {
            $area = self::parseArea($name, $datastr);
			if ($area) {
				$areas[] = $area;
			}
        }
        return new AreaSet($areas);
    }
    
    /**
     * @param string $name
     * @param string $datastr
     * @return Area
     */
    public static function parseArea( $name, $datastr )
    {
        if (!$datastr) {
            return null;
        }
        
        $vals = explode(",", $datastr);

        $m = new Area($name);
        $m->x0 = (int)$vals[0];
        $m->y0 = (int)$vals[1];
        $m->x1 = (int)$vals[2];
        $m->y1 = (int)$vals[3];

        return $m;
    }
    
    /**
     * @param AreaSet $set
     * @return string
     */
    public static function serializeAreaSet( AreaSet $set )
    {
        $strs = [];
        foreach ($set->dict() as $area) {
            $strs[] = self::serializeAreaNamed($area);
        }
        return implode(";", $strs);
    }
    
    /**
     * @param Area $area
     * @return string
     */
    public static function serializeAreaNamed( Area $area )
    {
        return $area->getName() . "=" . self::serializeArea($area);
    }
    
    /**
     * @param Area $area
     * @return string
     */
    public static function serializeArea( Area $area )
    {
        return implode(',', [
                (int)$area->x0
                , (int)$area->y0
                , (int)$area->x1
                , (int)$area->y1
            ]);
    }
}

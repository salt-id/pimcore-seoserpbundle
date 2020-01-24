<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 23/01/2020
 * Time: 20:14
 */

namespace SaltId\SeoSerpBundle\Helper;

class GeneralHelper
{
    public static function removeSpace($string)
    {
        return preg_replace('/\s+/', '', $string);
    }
}
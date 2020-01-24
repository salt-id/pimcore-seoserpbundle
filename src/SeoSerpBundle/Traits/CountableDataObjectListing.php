<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 23/01/2020
 * Time: 21:51
 */

namespace SaltId\SeoSerpBundle\Traits;

trait CountableDataObjectListing
{
    public function getCount()
    {
        return count($this->load()) ?? 0;
    }
}
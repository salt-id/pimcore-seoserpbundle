<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 23/01/2020
 * Time: 21:47
 */

namespace SaltId\SeoSerpBundle\Traits;

trait ArrayableDataObjectListing
{
    public function toArray(array $exclude = [], array $except = [], $useKey = false)
    {
        $data = [];
        if ($this->getCount() < 1) {
            return [];
        }

        foreach ($this->load() as $object) {
            $data[] = $object->toArray($exclude, $except, $useKey);
        }

        return $data;
    }
}
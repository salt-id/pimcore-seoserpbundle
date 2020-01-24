<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 23/01/2020
 * Time: 21:30
 */

namespace SaltId\SeoSerpBundle\Traits;

trait ArrayableDataObject
{
    public function toArray(array $exclude = [], array $except = [], $useKey = false)
    {
        $data = [];
        if (count($exclude) > 0 &&
            in_array('*', $exclude, false) &&
            count($except) === 0
        ) {
            return $data;
        }

        $fields = get_object_vars($this);
        foreach ($except as $item) {
            $getter = 'get' . ucfirst($item);
            if (!method_exists($this, $getter)) {
                return $data;
            }
            if ($useKey) {
                $data[$item] = $this->$getter();
            }
            if (!$useKey) {
                $data = $this->$getter();
            }
        }
        return $data;
    }
}
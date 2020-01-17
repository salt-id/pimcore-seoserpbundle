<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 16/01/2020
 * Time: 10:34
 */

namespace SaltId\SeoSerpBundle\Traits;

use SaltId\SeoSerpBundle\Model\Seo;

trait Seoable
{
    public function getSeoTitle()
    {
        $seo = $this->getSeoSerp();
        $data = $seo ? $seo->getData() : null;

        return json_decode($data, false)->seoTitle;
    }

    public function getSeoDescription()
    {
        $seo = $this->getSeoSerp();
        $data = $seo ? $seo->getData() : null;

        return json_decode($data, false)->seoDescription;
    }

    public function getSeoMetaData()
    {
        $seo = $this->getSeoSerp();
        $data = $seo ? $seo->getData() : null;

        return json_decode($data, false)->metadata;
    }

    /**
     * @return Seo|null
     */
    private function getSeoSerp()
    {
        return Seo::getByObjectId($this->o_id);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 17/01/2020
 * Time: 11:52
 */

namespace SaltId\SeoSerpBundle\Model\SeoRule;

use Pimcore\Model\Listing\AbstractListing;
use SaltId\SeoSerpBundle\Traits\ArrayableDataObjectListing;
use SaltId\SeoSerpBundle\Traits\CountableDataObjectListing;

class Listing extends AbstractListing
{
    use ArrayableDataObjectListing, CountableDataObjectListing;

    /**
     * List of seoRule.
     *
     * @var array
     */
    protected $seoRules = null;

    public function setSeoRules(array $seoRules)
    {
        $this->seoRules = $seoRules;

        return $this;
    }

    public function getSeoRules()
    {
        if ($this->seoRules === null) {
            $this->getDao()->load();
        }

        return $this->seoRules;
    }
}
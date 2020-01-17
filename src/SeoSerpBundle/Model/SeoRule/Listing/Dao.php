<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 17/01/2020
 * Time: 11:52
 */

namespace SaltId\SeoSerpBundle\Model\SeoRule\Listing;

use Pimcore\Model\Listing\Dao\AbstractDao;
use SaltId\SeoSerpBundle\Model\SeoRule;

class Dao extends AbstractDao
{
    public function load()
    {
        $ids = $this->db->fetchCol('SELECT id FROM bundle_seoserp_seo_rule' . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(), $this->model->getConditionVariables());

        $seoRules = [];
        foreach ($ids as $id) {
            $seoRules[] = SeoRule::getById($id);
        }

        $this->model->setSeoRules($seoRules);

        return $seoRules;
    }
}
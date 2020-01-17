<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 16/01/2020
 * Time: 17:47
 */

namespace SaltId\SeoSerpBundle\Model;

use Pimcore\Model\AbstractModel;

class Seo extends AbstractModel
{
    /**
     * @var int $id
     */
    public $id;

    /** @var int $objectId */
    public $objectId;

    /** @var string $data */
    public $data;

    public static function getByObjectId($objectId)
    {
        try {
            $obj = new self;
            $obj->getDao()->getByObjectId($objectId);
            return $obj;
        } catch (\Exception $e) {

        }

        return null;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param int $objectId
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
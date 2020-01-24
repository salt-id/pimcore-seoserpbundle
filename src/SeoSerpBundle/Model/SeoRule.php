<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 16/01/2020
 * Time: 23:14
 */

namespace SaltId\SeoSerpBundle\Model;

use Pimcore\Model\AbstractModel;
use SaltId\SeoSerpBundle\Traits\ArrayableDataObject;

class SeoRule extends AbstractModel
{
    use ArrayableDataObject;

    /** @var integer $id */
    public $id;

    /** @var string $name */
    public $name;

    /** @var string $routeName */
    public $routeName;

    /** @var  */
    public $routeVariable;

    /** @var string $className */
    public $className;

    /** @var string $classField */
    public $classField;

    /** @var boolean */
    public $active;

    public static function getById($id)
    {
        try {
            $obj = new self;
            $obj->getDao()->getById($id);
            return $obj;
        } catch (\Exception $e) {

        }

        return null;
    }

    public static function getByRouteName($routeName)
    {
        try {
            $obj = new self;
            $obj->getDao()->getByRouteName($routeName);

            return $obj;
        } catch (\Exception $e) {

        }

        return null;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;
    }

    /**
     * @return string
     */
    public function getRouteVariable()
    {
        return $this->routeVariable;
    }

    /**
     * @param string $routeVariable
     */
    public function setRouteVariable($routeVariable)
    {
        $this->routeVariable = $routeVariable;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getClassField()
    {
        return $this->classField;
    }

    /**
     * @param string $classField
     */
    public function setClassField($classField)
    {
        $this->classField = $classField;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return (bool) $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}
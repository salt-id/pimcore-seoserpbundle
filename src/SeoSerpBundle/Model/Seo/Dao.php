<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 16/01/2020
 * Time: 17:49
 */

namespace SaltId\SeoSerpBundle\Model\Seo;

use Pimcore\Model\Dao\AbstractDao;

class Dao extends AbstractDao
{
    protected $tableName = 'bundle_seoserp_seo';

    public function getByObjectId($objectId = null)
    {
        if ($objectId != null) {
            $this->model->setObjectId($objectId);
        }

        $data = $this->db->fetchRow('SELECT * FROM ' . $this->tableName . ' WHERE objectId = ?', $this->model->getObjectId());

        if (!$data['objectId']) {
            throw new \Exception('Object with the objectId ' . $this->model->getObjectId() . ' does not exists');
        }

        $this->assignVariablesToModel($data);
    }

    public function save()
    {
        $vars = get_object_vars($this->model);
        $buffer = [];

        $validColumns = $this->getValidTableColumns($this->tableName);

        if(count($vars))
            foreach ($vars as $k => $v) {

                if(!in_array($k, $validColumns))
                    continue;

                $getter = 'get' . ucfirst($k);

                if(!is_callable([$this->model, $getter]))
                    continue;

                $value = $this->model->$getter();

                if(is_bool($value))
                    $value = (int)$value;

                $buffer[$k] = $value;
            }

        if($this->model->getId() !== null) {
            $this->db->update($this->tableName, $buffer, ['id' => $this->model->getId()]);
            return;
        }

        $this->db->insert($this->tableName, $buffer);
        $this->model->setId($this->db->lastInsertId());
    }

    /**
     * delete
     */
    public function delete()
    {
        $this->db->delete($this->tableName, ['id' => $this->model->getId()]);
    }
}
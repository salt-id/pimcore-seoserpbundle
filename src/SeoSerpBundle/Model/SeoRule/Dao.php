<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 16/01/2020
 * Time: 23:15
 */

namespace SaltId\SeoSerpBundle\Model\SeoRule;

use Pimcore\Model\Dao\AbstractDao;

class Dao extends AbstractDao
{
    protected $tableName = 'bundle_seoserp_seo_rule';

    public function getById($id = null)
    {
        if ($id != null) {
            $this->model->setId($id);
        }

        $data = $this
            ->db
            ->fetchRow('SELECT * FROM ' . $this->tableName . ' WHERE id = ?', $this->model->getId());

        if (!$data['id']) {
            throw new \Exception('Object with the id ' . $this->model->getId() . ' does not exists');
        }

        $this->assignVariablesToModel($data);
    }

    public function getByRouteName($routeName)
    {
        if ($routeName != null) {
            $this->model->setRouteName($routeName);
        }

        $data = $this
            ->db
            ->fetchRow('SELECT * FROM ' . $this->tableName . ' WHERE routeName = ?', $this->model->getRouteName());

        if (!$data['routeName']) {
            throw new \Exception('Object with routeName ' . $this->model->getRouteName() . ' does not exits');
        }

        $this->assignVariablesToModel($data);
    }

    /**
     * save
     */
    public function save() {
        $vars = get_object_vars($this->model);

        $buffer = [];

        $validColumns = $this->getValidTableColumns($this->tableName);

        if(count($vars))
            foreach ($vars as $k => $v) {

                if(!in_array($k, $validColumns))
                    continue;

                $getter = "get" . ucfirst($k);

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
<?php
namespace SoftDelete\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\Datasource\EntityInterface;

trait SoftDeleteTrait {

    public $softDeleteField = 'deleted';

    /**
     * Add the conditions `deleted IS NULL` to every find request in order not to return
     * soft deleted records.
     * To also find soft deleted records, `$options` shall contains `'withDeleted'`.
     *
     * @return \Cake\ORM\Query
     */
    public function find($type = 'all', $options = [])
    {
        if(in_array('withDeleted', $options)) {
            return parent::find($type, $options);
        }

        if(!isset($options['conditions'])) {
            $options['conditions'] = [];
        }

        $options['conditions'] = array_merge($options['conditions'], [$this->alias() . '.' . $this->softDeleteField . ' IS NULL']);

        return parent::find($type, $options);
    }

    /**
     * Perform the delete operation.
     *
     * Will soft delete the entity provided. Will remove rows from any
     * dependent associations, and clear out join tables for BelongsToMany associations.
     *
     * @param \Cake\DataSource\EntityInterface $entity The entity to soft delete.
     * @param \ArrayObject $options The options for the delete.
     * @throws \InvalidArgumentException if there are no primary key values of the
     * passed entity
     * @return bool success
     */
    protected function _processDelete($entity, $options)
    {
        if ($entity->isNew()) {
            return false;
        }

        $primaryKey = (array)$this->primaryKey();
        if (!$entity->has($primaryKey)) {
            $msg = 'Deleting requires all primary key values.';
            throw new \InvalidArgumentException($msg);
        }

        if ($options['checkRules'] && !$this->checkRules($entity, RulesChecker::DELETE, $options)) {
            return false;
        }

        $event = $this->dispatchEvent('Model.beforeDelete', [
            'entity' => $entity,
            'options' => $options
        ]);

        if ($event->isStopped()) {
            return $event->result;
        }

        $this->_associations->cascadeDelete(
            $entity,
            ['_primary' => false] + $options->getArrayCopy()
        );

        $query = $this->query();
        $conditions = (array)$entity->extract($primaryKey);
        $statement = $query->update()
            ->set([$this->softDeleteField => date('Y-m-d H:i:s')])
            ->where($conditions)
            ->execute();

        $success = $statement->rowCount() > 0;
        if (!$success) {
            return $success;
        }

        $this->dispatchEvent('Model.afterDelete', [
            'entity' => $entity,
            'options' => $options
        ]);

        return $success;
    }

    /**
     * Soft deletes all records matching `$conditions`.
     * @return int number of affected rows.
     */
    public function deleteAll($conditions)
    {
        $query = $this->query()
            ->update()
            ->set([$this->softDeleteField => date('Y-m-d H:i:s')])
            ->where($conditions);
        $statement = $query->execute();
        $statement->closeCursor();
        return $statement->rowCount();
    }

    /**
     * Hard deletes the given $entity.
     * @return bool true in case of success, false otherwise.
     */
    public function hardDelete(EntityInterface $entity)
    {
        if(!$this->delete($entity)) {
            return false;
        }
        $primaryKey = (array)$this->primaryKey();
        $query = $this->query();
        $conditions = (array)$entity->extract($primaryKey);
        $statement = $query->delete()
            ->where($conditions)
            ->execute();

        $success = $statement->rowCount() > 0;
        if (!$success) {
            return $success;
        }

        return $success;
    }

    /**
     * Hard deletes all records that were soft deleted before a given date.
     * @param \DateTime $until Date until witch soft deleted records must be hard deleted.
     * @return int number of affected rows.
     */
    public function hardDeleteAll(\Datetime $until)
    {
        $query = $this->query()
            ->delete()
            ->where([
                $this->softDeleteField . ' IS NOT NULL',
                $this->softDeleteField . ' <=' => $until->format('Y-m-d H:i:s')
            ]);
        $statement = $query->execute();
        $statement->closeCursor();
        return $statement->rowCount();
    }

}

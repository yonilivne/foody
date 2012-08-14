<?php
/**
 * User: vladvidican
 * Date: 7/26/12
 * Time: 4:46 PM
 */
class Model_PostMapper
{
    protected $_dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Model_DbTable_Post');
        }
        return $this->_dbTable;
    }

    /**
     * @description Save a post to the database
     * @param Model_Post $post
     * @return mixed
     */
    public function save(Model_Post $post)
    {
        $data = $post->toArray();
        try {
            if (null == ($id = $post->getId())) {
                unset($data['id']);
                return $this->getDbTable()->insert($data);
            }else{
                return $this->getDbTable()->update($data, array('id = ?' => $id));
            }
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
            echo "Message: " . $e->getMessage() . "\n";
        }
    }

    /**
     * @description Return all posts in a database
     * @return array
     */
    public function fetchAll()
    {
        $result = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($result as $row) {
            $entry = new Model_Post($row->toArray());
            $entries[] = $entry;
        }
        return $entries;
    }

    /**
     * @description Find a post in the database by it's id
     * @param $id
     * @return Model_Post|null
     */
    public function find($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return null;
        }
        $row = $result->current();
        $post = new Model_Post($row->toArray());
        return $post;

    }
}

<?php
/**
 * User: vladvidican
 * Date: 7/26/12
 * Time: 4:46 PM
 */
class Model_PostTextMapper
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
            $this->setDbTable('Model_DbTable_PostText');
        }
        return $this->_dbTable;
    }

    /**
     * @description Save a post text to the database
     * @param Model_PostText $postText
     * @return mixed
     */
    public function save(Model_PostText $postText)
    {
        $data = $postText->toArray();
        try {
            if (null == ($id = $postText->getId())) {
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
     * @description Fetch a text by it's post id
     * @param $postId
     * @return array
     */
    public function fetchByPostId($postId)
    {
        $select = $this->getDbTable()->select();
        $select->where('post_id =?',$postId);
        $result = $this->getDbTable()->fetchAll($select);
        $entries   = array();
        foreach ($result as $row) {
            $entry = new Model_PostText($row->toArray());
            $entries[] = $entry;
        }
        return $entries;
    }
}

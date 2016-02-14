<?php
/**
 * Ibrams_CmsExtended extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Ibrams
 * @package        Ibrams_CmsExtended
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Cmspage resource model
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Resource_Cmspage extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Cmspage tree object
     * @var Varien_Data_Tree_Db
     */
    protected $_tree;

    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        $this->_init('ibrams_cmsextended/cmspage', 'entity_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @access public
     * @param int $cmspageId
     * @return array
     * @author Ultimate Module Creator
     */
    public function lookupStoreIds($cmspageId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('ibrams_cmsextended/cmspage_store'), 'store_id')
            ->where('cmspage_id = ?', (int)$cmspageId);
        return $adapter->fetchCol($select);
    }

    /**
     * Perform operations after object load
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage
     * @author Ultimate Module Creator
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Ibrams_CmsExtended_Model_Cmspage $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('cmsextended_cmspage_store' => $this->getTable('ibrams_cmsextended/cmspage_store')),
                $this->getMainTable() . '.entity_id = cmsextended_cmspage_store.cmspage_id',
                array()
            )
            ->where('cmsextended_cmspage_store.store_id IN (?)', $storeIds)
            ->order('cmsextended_cmspage_store.store_id DESC')
            ->limit(1);
        }
        return $select;
    }

    /**
     * Retrieve cmspage tree object
     *
     * @access protected
     * @return Varien_Data_Tree_Db
     * @author Ultimate Module Creator
     */
    protected function _getTree()
    {
        if (!$this->_tree) {
            $this->_tree = Mage::getResourceModel('ibrams_cmsextended/cmspage_tree')->load();
        }
        return $this->_tree;
    }

    /**
     * Process cmspage data before delete
     * update children count for parent cmspage
     * delete child cmspages
     *
     * @access protected
     * @param Varien_Object $object
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage
     * @author Ultimate Module Creator
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_beforeDelete($object);
        /**
         * Update children count for all parent cmspages
         */
        $parentIds = $object->getParentIds();
        if ($parentIds) {
            $childDecrease = $object->getChildrenCount() + 1; // +1 is itself
            $data = array('children_count' => new Zend_Db_Expr('children_count - ' . $childDecrease));
            $where = array('entity_id IN(?)' => $parentIds);
            $this->_getWriteAdapter()->update($this->getMainTable(), $data, $where);
        }
        $this->deleteChildren($object);
        return $this;
    }

    /**
     * Delete children cmspages of specific cmspage
     *
     * @access public
     * @param Varien_Object $object
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage
     * @author Ultimate Module Creator
     */
    public function deleteChildren(Varien_Object $object)
    {
        $adapter = $this->_getWriteAdapter();
        $pathField = $adapter->quoteIdentifier('path');
        $select = $adapter->select()
            ->from($this->getMainTable(), array('entity_id'))
            ->where($pathField . ' LIKE :c_path');
        $childrenIds = $adapter->fetchCol($select, array('c_path' => $object->getPath() . '/%'));
        if (!empty($childrenIds)) {
            $adapter->delete(
                $this->getMainTable(),
                array('entity_id IN (?)' => $childrenIds)
            );
        }
        /**
         * Add deleted children ids to object
         * This data can be used in after delete event
         */
        $object->setDeletedChildrenIds($childrenIds);
        return $this;
    }

    /**
     * Process cmspage data after save cmspage object
     *
     * @access protected
     * @param Varien_Object $object
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage
     * @author Ultimate Module Creator
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (substr($object->getPath(), -1) == '/') {
            $object->setPath($object->getPath() . $object->getId());
            $this->_savePath($object);
        }


        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('ibrams_cmsextended/cmspage_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = array(
                'cmspage_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );
            $this->_getWriteAdapter()->delete($table, $where);
        }
        if ($insert) {
            $data = array();
            foreach ($insert as $storeId) {
                $data[] = array(
                    'cmspage_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * Update path field
     *
     * @access protected
     * @param Ibrams_CmsExtended_Model_Cmspage $object
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage
     * @author Ultimate Module Creator
     */
    protected function _savePath($object)
    {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('path' => $object->getPath()),
                array('entity_id = ?' => $object->getId())
            );
        }
        return $this;
    }

    /**
     * Get maximum position of child cmspages by specific tree path
     *
     * @access protected
     * @param string $path
     * @return int
     * @author Ultimate Module Creator
     */
    protected function _getMaxPosition($path)
    {
        $adapter = $this->getReadConnection();
        $positionField = $adapter->quoteIdentifier('position');
        $level   = count(explode('/', $path));
        $bind = array(
            'c_level' => $level,
            'c_path'  => $path . '/%'
        );
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'MAX(' . $positionField . ')')
            ->where($adapter->quoteIdentifier('path') . ' LIKE :c_path')
            ->where($adapter->quoteIdentifier('level') . ' = :c_level');

        $position = $adapter->fetchOne($select, $bind);
        if (!$position) {
            $position = 0;
        }
        return $position;
    }

    /**
     * Get children cmspages count
     *
     * @access public
     * @param int $cmspageId
     * @return int
     * @author Ultimate Module Creator
     */
    public function getChildrenCount($cmspageId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'children_count')
            ->where('entity_id = :entity_id');
        $bind = array('entity_id' => $cmspageId);
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Check if cmspage id exist
     *
     * @access public
     * @param int $entityId
     * @return bool
     * @author Ultimate Module Creator
     */
    public function checkId($entityId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('entity_id = :entity_id');
        $bind =  array('entity_id' => $entityId);
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Check array of cmspages identifiers
     *
     * @access public
     * @param array $ids
     * @return array
     * @author Ultimate Module Creator
     */
    public function verifyIds(array $ids)
    {
        if (empty($ids)) {
            return array();
        }
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('entity_id IN(?)', $ids);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Get count of active/not active children cmspages
     *
     * @param Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @param bool $isActiveFlag
     * @return int
     * @author Ultimate Module Creator
     */
    public function getChildrenAmount($cmspage, $isActiveFlag = true)
    {
        $bind = array(
            'active_flag'  => $isActiveFlag,
            'c_path'   => $cmspage->getPath() . '/%'
        );
        $select = $this->_getReadAdapter()->select()
            ->from(array('m' => $this->getMainTable()), array('COUNT(m.entity_id)'))
            ->where('m.path LIKE :c_path')
            ->where('status' . ' = :active_flag');
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Return parent cmspages of cmspage
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @return array
     * @author Ultimate Module Creator
     */
    public function getParentCmspages($cmspage)
    {
        $pathIds = array_reverse(explode('/', $cmspage->getPath()));
        $cmspages = Mage::getResourceModel('ibrams_cmsextended/cmspage_collection')
            ->addFieldToFilter('entity_id', array('in' => $pathIds))
            ->load()
            ->getItems();
        return $cmspages;
    }

    /**
     * Return child cmspages
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Collection
     * @author Ultimate Module Creator
     */
    public function getChildrenCmspages($cmspage)
    {
        $collection = $cmspage->getCollection();
        $collection
            ->addIdFilter($cmspage->getChildCmspages())
            ->setOrder('position', Varien_Db_Select::SQL_ASC)
            ->load();
        return $collection;
    }
    /**
     * Return children ids of cmspage
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @param boolean $recursive
     * @return array
     * @author Ultimate Module Creator
     */
    public function getChildren($cmspage, $recursive = true)
    {
        $bind = array(
            'c_path'   => $cmspage->getPath() . '/%'
        );
        $select = $this->_getReadAdapter()->select()
            ->from(array('m' => $this->getMainTable()), 'entity_id')
            ->where('status = ?', 1)
            ->where($this->_getReadAdapter()->quoteIdentifier('path') . ' LIKE :c_path');
        if (!$recursive) {
            $select->where($this->_getReadAdapter()->quoteIdentifier('level') . ' <= :c_level');
            $bind['c_level'] = $cmspage->getLevel() + 1;
        }
        return $this->_getReadAdapter()->fetchCol($select, $bind);
    }

    /**
     * Process cmspage data before saving
     * prepare path and increment children count for parent cmspages
     *
     * @access protected
     * @param Varien_Object $object
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage
     * @author Ultimate Module Creator
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $accessroles = $object->getAccessroles();
        if (is_array($accessroles)) {
            $object->setAccessroles(implode(',', $accessroles));
        }
        $permittedroleactions = $object->getPermittedroleactions();
        if (is_array($permittedroleactions)) {
            $object->setPermittedroleactions(implode(',', $permittedroleactions));
        }
        if (!$object->getInitialSetupFlag()) {
            $urlKey = $object->getData('url_key');
            if ($urlKey == '') {
                $urlKey = $object->getName();
            }
            $urlKey = $this->formatUrlKey($urlKey);
            $validKey = false;
            while (!$validKey) {
                $entityId = $this->checkUrlKey($urlKey, $object->getStoreId(), false);
                if ($entityId == $object->getId() || empty($entityId)) {
                    $validKey = true;
                } else {
                    $parts = explode('-', $urlKey);
                    $last = $parts[count($parts) - 1];
                    if (!is_numeric($last)) {
                        $urlKey = $urlKey.'-1';
                    } else {
                        $suffix = '-'.($last + 1);
                        unset($parts[count($parts) - 1]);
                        $urlKey = implode('-', $parts).$suffix;
                    }
                }
            }
            $object->setData('url_key', $urlKey);
        }
        parent::_beforeSave($object);
        if (!$object->getChildrenCount()) {
            $object->setChildrenCount(0);
        }
        if ($object->getLevel() === null) {
            $object->setLevel(1);
        }
        if (!$object->getId() && !$object->getInitialSetupFlag()) {
            $object->setPosition($this->_getMaxPosition($object->getPath()) + 1);
            $path  = explode('/', $object->getPath());
            $level = count($path);
            $object->setLevel($level);
            if ($level) {
                $object->setParentId($path[$level - 1]);
            }
            $object->setPath($object->getPath() . '/');
            $toUpdateChild = explode('/', $object->getPath());
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('children_count'  => new Zend_Db_Expr('children_count+1')),
                array('entity_id IN(?)' => $toUpdateChild)
            );
        }
        return $this;
    }

    /**
     * Retrieve cmspages
     *
     * @access public
     * @param integer $parent
     * @param integer $recursionLevel
     * @param boolean|string $sorted
     * @param boolean $asCollection
     * @param boolean $toLoad
     * @return Varien_Data_Tree_Node_Collection|Ibrams_CmsExtended_Model_Resource_Cmspage_Collection
     * @author Ultimate Module Creator
     */
    public function getCmspages(
        $parent,
        $recursionLevel = 0,
        $sorted = false,
        $asCollection = false,
        $toLoad = true
    )
    {
        $tree = Mage::getResourceModel('ibrams_cmsextended/cmspage_tree');
        $nodes = $tree->loadNode($parent)
            ->loadChildren($recursionLevel)
            ->getChildren();
        $tree->addCollectionData(null, $sorted, $parent, $toLoad, true);
        if ($asCollection) {
            return $tree->getCollection();
        }
        return $nodes;
    }

    /**
     * Return all children ids of cmspage (with cmspage id)
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @return array
     * @author Ultimate Module Creator
     */
    public function getAllChildren($cmspage)
    {
        $children = $this->getChildren($cmspage);
        $myId = array($cmspage->getId());
        $children = array_merge($myId, $children);
        return $children;
    }

    /**
     * Check cmspage is forbidden to delete.
     *
     * @access public
     * @param integer $cmspageId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function isForbiddenToDelete($cmspageId)
    {
        return ($cmspageId == Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId());
    }

    /**
     * Get cmspage path value by its id
     *
     * @access public
     * @param int $cmspageId
     * @return string
     * @author Ultimate Module Creator
     */
    public function getCmspagePathById($cmspageId)
    {
        $select = $this->getReadConnection()->select()
            ->from($this->getMainTable(), array('path'))
            ->where('entity_id = :entity_id');
        $bind = array('entity_id' => (int)$cmspageId);
        return $this->getReadConnection()->fetchOne($select, $bind);
    }

    /**
     * Move cmspage to another parent node
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @param Ibrams_CmsExtended_Model_Cmspage $newParent
     * @param null|int $afterCmspageId
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage
     * @author Ultimate Module Creator
     */
    public function changeParent(
        Ibrams_CmsExtended_Model_Cmspage $cmspage,
        Ibrams_CmsExtended_Model_Cmspage $newParent,
        $afterCmspageId = null
    )
    {
        $childrenCount  = $this->getChildrenCount($cmspage->getId()) + 1;
        $table          = $this->getMainTable();
        $adapter        = $this->_getWriteAdapter();
        $levelFiled     = $adapter->quoteIdentifier('level');
        $pathField      = $adapter->quoteIdentifier('path');

        /**
         * Decrease children count for all old cmspage parent cmspages
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count - ' . $childrenCount)),
            array('entity_id IN(?)' => $cmspage->getParentIds())
        );
        /**
         * Increase children count for new cmspage parents
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count + ' . $childrenCount)),
            array('entity_id IN(?)' => $newParent->getPathIds())
        );

        $position = $this->_processPositions($cmspage, $newParent, $afterCmspageId);

        $newPath  = sprintf('%s/%s', $newParent->getPath(), $cmspage->getId());
        $newLevel = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $cmspage->getLevel();

        /**
         * Update children nodes path
         */
        $adapter->update(
            $table,
            array(
                'path' => new Zend_Db_Expr(
                    'REPLACE(' . $pathField . ','.
                    $adapter->quote($cmspage->getPath() . '/'). ', '.$adapter->quote($newPath . '/').')'
                ),
                'level' => new Zend_Db_Expr($levelFiled . ' + ' . $levelDisposition)
            ),
            array($pathField . ' LIKE ?' => $cmspage->getPath() . '/%')
        );
        /**
         * Update moved cmspage data
         */
        $data = array(
            'path'  => $newPath,
            'level' => $newLevel,
            'position'  =>$position,
            'parent_id' =>$newParent->getId()
        );
        $adapter->update($table, $data, array('entity_id = ?' => $cmspage->getId()));
        // Update cmspage object to new data
        $cmspage->addData($data);
        return $this;
    }

    /**
     * Process positions of old parent cmspage children and new parent cmspage children.
     * Get position for moved cmspage
     *
     * @access protected
     * @param Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @param Ibrams_CmsExtended_Model_Cmspage $newParent
     * @param null|int $afterCmspageId
     * @return int
     * @author Ultimate Module Creator
     */
    protected function _processPositions($cmspage, $newParent, $afterCmspageId)
    {
        $table  = $this->getMainTable();
        $adapter= $this->_getWriteAdapter();
        $positionField  = $adapter->quoteIdentifier('position');

        $bind = array(
            'position' => new Zend_Db_Expr($positionField . ' - 1')
        );
        $where = array(
            'parent_id = ?' => $cmspage->getParentId(),
            $positionField . ' > ?' => $cmspage->getPosition()
        );
        $adapter->update($table, $bind, $where);

        /**
         * Prepare position value
         */
        if ($afterCmspageId) {
            $select = $adapter->select()
                ->from($table, 'position')
                ->where('entity_id = :entity_id');
            $position = $adapter->fetchOne($select, array('entity_id' => $afterCmspageId));
            $bind = array(
                'position' => new Zend_Db_Expr($positionField . ' + 1')
            );
            $where = array(
                'parent_id = ?' => $newParent->getId(),
                $positionField . ' > ?' => $position
            );
            $adapter->update($table, $bind, $where);
        } elseif ($afterCmspageId !== null) {
            $position = 0;
            $bind = array(
                'position' => new Zend_Db_Expr($positionField . ' + 1')
            );
            $where = array(
                'parent_id = ?' => $newParent->getId(),
                $positionField . ' > ?' => $position
            );
            $adapter->update($table, $bind, $where);
        } else {
            $select = $adapter->select()
                ->from($table, array('position' => new Zend_Db_Expr('MIN(' . $positionField. ')')))
                ->where('parent_id = :parent_id');
            $position = $adapter->fetchOne($select, array('parent_id' => $newParent->getId()));
        }
        $position += 1;
        return $position;
    }

    /**
     * check url key
     *
     * @access public
     * @param string $urlKey
     * @param int $storeId
     * @param bool $active
     * @return mixed
     * @author Ultimate Module Creator
     */
    public function checkUrlKey($urlKey, $storeId, $active = true)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_initCheckUrlKeySelect($urlKey, $stores);
        if ($active) {
            $select->where('e.status = ?', $active);
        }
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Check for unique URL key
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     * @author Ultimate Module Creator
     */
    public function getIsUniqueUrlKey(Mage_Core_Model_Abstract $object)
    {
        if (Mage::app()->isSingleStoreMode() || !$object->hasStores()) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }
        $select = $this->_initCheckUrlKeySelect($object->getData('url_key'), $stores);
        if ($object->getId()) {
            $select->where('e.entity_id <> ?', $object->getId());
        }
        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }
        return true;
    }

    /**
     * Check if the URL key is numeric
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     * @author Ultimate Module Creator
     */
    protected function isNumericUrlKey(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     * Check if the URL key is valid
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     * @author Ultimate Module Creator
     */
    protected function isValidUrlKey(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('url_key'));
    }

    /**
     * format string as url key
     *
     * @access public
     * @param string $str
     * @return string
     * @author Ultimate Module Creator
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }

    /**
     * init the check select
     *
     * @access protected
     * @param string $urlKey
     * @param array $store
     * @return Zend_Db_Select
     * @author Ultimate Module Creator
     */
    protected function _initCheckUrlKeySelect($urlKey, $store)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('e' => $this->getMainTable()))
            ->join(
                array('es' => $this->getTable('ibrams_cmsextended/cmspage_store')),
                'e.entity_id = es.cmspage_id',
                array())
            ->where('e.url_key = ?', $urlKey)
            ->where('es.store_id IN (?)', $store);
        return $select;
    }
}

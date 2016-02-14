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
 * Page collection resource model
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Resource_Page_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_joinedFields = array();

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('ibrams_cmsextended/page');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Add filter by store
     *
     * @access public
     * @param int|Mage_Core_Model_Store $store
     * @param bool $withAdmin
     * @return Ibrams_CmsExtended_Model_Resource_Page_Collection
     * @author Ultimate Module Creator
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!isset($this->_joinedFields['store'])) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }
            if (!is_array($store)) {
                $store = array($store);
            }
            if ($withAdmin) {
                $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
            }
            $this->addFilter('store', array('in' => $store), 'public');
            $this->_joinedFields['store'] = true;
        }
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @access protected
     * @return Ibrams_CmsExtended_Model_Resource_Page_Collection
     * @author Ultimate Module Creator
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('ibrams_cmsextended/page_store')),
                'main_table.entity_id = store_table.page_id',
                array()
            )
            ->group('main_table.entity_id');
            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        return parent::_renderFiltersBefore();
    }

    /**
     * Add Id filter
     *
     * @access public
     * @param array $pageIds
     * @return Ibrams_CmsExtended_Model_Resource_Page_Collection
     * @author Ultimate Module Creator
     */
    public function addIdFilter($pageIds)
    {
        if (is_array($pageIds)) {
            if (empty($pageIds)) {
                $condition = '';
            } else {
                $condition = array('in' => $pageIds);
            }
        } elseif (is_numeric($pageIds)) {
            $condition = $pageIds;
        } elseif (is_string($pageIds)) {
            $ids = explode(',', $pageIds);
            if (empty($ids)) {
                $condition = $pageIds;
            } else {
                $condition = array('in' => $ids);
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    /**
     * Add page path filter
     *
     * @access public
     * @param string $regexp
     * @return Ibrams_CmsExtended_Model_Resource_Page_Collection
     * @author Ultimate Module Creator
     */
    public function addPathFilter($regexp)
    {
        $this->addFieldToFilter('path', array('regexp' => $regexp));
        return $this;
    }

    /**
     * Add page path filter
     *
     * @access public
     * @param array|string $paths
     * @return Ibrams_CmsExtended_Model_Resource_Page_Collection
     * @author Ultimate Module Creator
     */
    public function addPathsFilter($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        $write  = $this->getResource()->getWriteConnection();
        $cond   = array();
        foreach ($paths as $path) {
            $cond[] = $write->quoteInto('e.path LIKE ?', "$path%");
        }
        if ($cond) {
            $this->getSelect()->where(join(' OR ', $cond));
        }
        return $this;
    }

    /**
     * Add page level filter
     *
     * @access public
     * @param int|string $level
     * @return Ibrams_CmsExtended_Model_Resource_Page_Collection
     * @author Ultimate Module Creator
     */
    public function addLevelFilter($level)
    {
        $this->addFieldToFilter('level', array('lteq' => $level));
        return $this;
    }

    /**
     * Add root page filter
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Resource_Page_Collection
     */
    public function addRootLevelFilter()
    {
        $this->addFieldToFilter('path', array('neq' => '1'));
        $this->addLevelFilter(1);
        return $this;
    }

    /**
     * Add order field
     *
     * @access public
     * @param string $field
     * @return Ibrams_CmsExtended_Model_Resource_Page_Collection
     */
    public function addOrderField($field)
    {
        $this->setOrder($field, self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * Add active page filter
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Resource_Page_Collection
     */
    public function addStatusFilter($status = 1)
    {
        $this->addFieldToFilter('status', $status);
        return $this;
    }

    /**
     * get pages as array
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _toOptionArray($valueField='entity_id', $labelField='name', $additional=array())
    {
        $res = array();
        $additional['value'] = $valueField;
        $additional['label'] = $labelField;

        foreach ($this as $item) {
            if ($item->getId() == Mage::helper('ibrams_cmsextended/page')->getRootPageId()) {
                continue;
            }
            foreach ($additional as $code => $field) {
                $data[$code] = $item->getData($field);
            }
            $res[] = $data;
        }
        return $res;
    }

    /**
     * get options hash
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _toOptionHash($valueField='entity_id', $labelField='name')
    {
        $res = array();
        foreach ($this as $item) {
            if ($item->getId() == Mage::helper('ibrams_cmsextended/page')->getRootPageId()) {
                continue;
            }
            $res[$item->getData($valueField)] = $item->getData($labelField);
        }
        return $res;
    }

    /**
     * add the product filter to collection
     *
     * @access public
     * @param mixed (Mage_Catalog_Model_Product|int) $product
     * @return Ibrams_CmsExtended_Model_Resource_Page_Collection
     * @author Ultimate Module Creator
     */
    public function addProductFilter($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $product = $product->getId();
        }
        if (!isset($this->_joinedFields['product'])) {
            $this->getSelect()->join(
                array('related_product' => $this->getTable('ibrams_cmsextended/page_product')),
                'related_product.page_id = main_table.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_product.product_id = ?', $product);
            $this->_joinedFields['product'] = true;
        }
        return $this;
    }

    /**
     * add the category filter to collection
     *
     * @access public
     * @param mixed (Mage_Catalog_Model_Category|int) $category
     * @return Ibrams_CmsExtended_Model_Resource_Page_Collection
     * @author Ultimate Module Creator
     */
    public function addCategoryFilter($category)
    {
        if ($category instanceof Mage_Catalog_Model_Category) {
            $category = $category->getId();
        }
        if (!isset($this->_joinedFields['category'])) {
            $this->getSelect()->join(
                array('related_category' => $this->getTable('ibrams_cmsextended/page_category')),
                'related_category.page_id = main_table.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_category.category_id = ?', $category);
            $this->_joinedFields['category'] = true;
        }
        return $this;
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @access public
     * @return Varien_Db_Select
     * @author Ultimate Module Creator
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}

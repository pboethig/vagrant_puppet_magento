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
 * Page model
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Page extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'ibrams_cmsextended_page';
    const CACHE_TAG = 'ibrams_cmsextended_page';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'ibrams_cmsextended_page';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'page';
    protected $_productInstance = null;
    protected $_categoryInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('ibrams_cmsextended/page');
    }

    /**
     * before save page
     *
     * @access protected
     * @return Ibrams_CmsExtended_Model_Page
     * @author Ultimate Module Creator
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * get the url to the page details page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPageUrl()
    {
        if ($this->getUrlKey()) {
            $urlKey = '';
            if ($prefix = Mage::getStoreConfig('ibrams_cmsextended/page/url_prefix')) {
                $urlKey .= $prefix.'/';
            }
            $urlKey .= $this->getUrlKey();
            if ($suffix = Mage::getStoreConfig('ibrams_cmsextended/page/url_suffix')) {
                $urlKey .= '.'.$suffix;
            }
            return Mage::getUrl('', array('_direct'=>$urlKey));
        }
        return Mage::getUrl('ibrams_cmsextended/page/view', array('id'=>$this->getId()));
    }

    /**
     * check URL key
     *
     * @access public
     * @param string $urlKey
     * @param bool $active
     * @return mixed
     * @author Ultimate Module Creator
     */
    public function checkUrlKey($urlKey, $active = true)
    {
        return $this->_getResource()->checkUrlKey($urlKey, $active);
    }

    /**
     * save page relation
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Page
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        $this->getProductInstance()->savePageRelation($this);
        $this->getCategoryInstance()->savePageRelation($this);
        return parent::_afterSave();
    }

    /**
     * get product relation model
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Page_Product
     * @author Ultimate Module Creator
     */
    public function getProductInstance()
    {
        if (!$this->_productInstance) {
            $this->_productInstance = Mage::getSingleton('ibrams_cmsextended/page_product');
        }
        return $this->_productInstance;
    }

    /**
     * get selected products array
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedProducts()
    {
        if (!$this->hasSelectedProducts()) {
            $products = array();
            foreach ($this->getSelectedProductsCollection() as $product) {
                $products[] = $product;
            }
            $this->setSelectedProducts($products);
        }
        return $this->getData('selected_products');
    }

    /**
     * Retrieve collection selected products
     *
     * @access public
     * @return Ibrams_CmsExtended_Resource_Page_Product_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedProductsCollection()
    {
        $collection = $this->getProductInstance()->getProductCollection($this);
        return $collection;
    }

    /**
     * get category relation model
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Page_Category
     * @author Ultimate Module Creator
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('ibrams_cmsextended/page_category');
        }
        return $this->_categoryInstance;
    }

    /**
     * get selected categories array
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesCollection() as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

    /**
     * Retrieve collection selected categories
     *
     * @access public
     * @return Ibrams_CmsExtended_Resource_Page_Category_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedCategoriesCollection()
    {
        $collection = $this->getCategoryInstance()->getCategoryCollection($this);
        return $collection;
    }

    /**
     * get the tree model
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Resource_Page_Tree
     * @author Ultimate Module Creator
     */
    public function getTreeModel()
    {
        return Mage::getResourceModel('ibrams_cmsextended/page_tree');
    }

    /**
     * get tree model instance
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Resource_Page_Tree
     * @author Ultimate Module Creator
     */
    public function getTreeModelInstance()
    {
        if (is_null($this->_treeModel)) {
            $this->_treeModel = Mage::getResourceSingleton('ibrams_cmsextended/page_tree');
        }
        return $this->_treeModel;
    }

    /**
     * Move page
     *
     * @access public
     * @param   int $parentId new parent page id
     * @param   int $afterPageId page id after which we have put current page
     * @return  Ibrams_CmsExtended_Model_Page
     * @author Ultimate Module Creator
     */
    public function move($parentId, $afterPageId)
    {
        $parent = Mage::getModel('ibrams_cmsextended/page')->load($parentId);
        if (!$parent->getId()) {
            Mage::throwException(
                Mage::helper('ibrams_cmsextended')->__(
                    'Page move operation is not possible: the new parent page was not found.'
                )
            );
        }
        if (!$this->getId()) {
            Mage::throwException(
                Mage::helper('ibrams_cmsextended')->__(
                    'Page move operation is not possible: the current page was not found.'
                )
            );
        } elseif ($parent->getId() == $this->getId()) {
            Mage::throwException(
                Mage::helper('ibrams_cmsextended')->__(
                    'Page move operation is not possible: parent page is equal to child page.'
                )
            );
        }
        $this->setMovedPageId($this->getId());
        $eventParams = array(
            $this->_eventObject => $this,
            'parent'            => $parent,
            'page_id'     => $this->getId(),
            'prev_parent_id'    => $this->getParentId(),
            'parent_id'         => $parentId
        );
        $moveComplete = false;
        $this->_getResource()->beginTransaction();
        try {
            $this->getResource()->changeParent($this, $parent, $afterPageId);
            $this->_getResource()->commit();
            $this->setAffectedPageIds(array($this->getId(), $this->getParentId(), $parentId));
            $moveComplete = true;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        if ($moveComplete) {
            Mage::app()->cleanCache(array(self::CACHE_TAG));
        }
        return $this;
    }

    /**
     * Get the parent page
     *
     * @access public
     * @return  Ibrams_CmsExtended_Model_Page
     * @author Ultimate Module Creator
     */
    public function getParentPage()
    {
        if (!$this->hasData('parent_page')) {
            $this->setData(
                'parent_page',
                Mage::getModel('ibrams_cmsextended/page')->load($this->getParentId())
            );
        }
        return $this->_getData('parent_page');
    }

    /**
     * Get the parent id
     *
     * @access public
     * @return  int
     * @author Ultimate Module Creator
     */
    public function getParentId()
    {
        $parentIds = $this->getParentIds();
        return intval(array_pop($parentIds));
    }

    /**
     * Get all parent pages ids
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), array($this->getId()));
    }

    /**
     * Get all pages children
     *
     * @access public
     * @param bool $asArray
     * @return mixed (array|string)
     * @author Ultimate Module Creator
     */
    public function getAllChildren($asArray = false)
    {
        $children = $this->getResource()->getAllChildren($this);
        if ($asArray) {
            return $children;
        } else {
            return implode(',', $children);
        }
    }

    /**
     * Get all pages children
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getChildPages()
    {
        return implode(',', $this->getResource()->getChildren($this, false));
    }

    /**
     * check the id
     *
     * @access public
     * @param int $id
     * @return bool
     * @author Ultimate Module Creator
     */
    public function checkId($id)
    {
        return $this->_getResource()->checkId($id);
    }

    /**
     * Get array pages ids which are part of page path
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    /**
     * Retrieve level
     *
     * @access public
     * @return int
     * @author Ultimate Module Creator
     */
    public function getLevel()
    {
        if (!$this->hasLevel()) {
            return count(explode('/', $this->getPath())) - 1;
        }
        return $this->getData('level');
    }

    /**
     * Verify page ids
     *
     * @access public
     * @param array $ids
     * @return bool
     * @author Ultimate Module Creator
     */
    public function verifyIds(array $ids)
    {
        return $this->getResource()->verifyIds($ids);
    }

    /**
     * check if page has children
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function hasChildren()
    {
        return $this->_getResource()->getChildrenAmount($this) > 0;
    }

    /**
     * check if page can be deleted
     *
     * @access protected
     * @return Ibrams_CmsExtended_Model_Page
     * @author Ultimate Module Creator
     */
    protected function _beforeDelete()
    {
        if ($this->getResource()->isForbiddenToDelete($this->getId())) {
            Mage::throwException(Mage::helper('ibrams_cmsextended')->__("Can't delete root page."));
        }
        return parent::_beforeDelete();
    }

    /**
     * get the pages
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Page $parent
     * @param int $recursionLevel
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @author Ultimate Module Creator
     */
    public function getPages($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true)
    {
        return $this->getResource()->getPages($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
    }

    /**
     * Return parent pages of current page
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getParentPages()
    {
        return $this->getResource()->getParentPages($this);
    }

    /**
     * Return children pages of current page
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getChildrenPages()
    {
        return $this->getResource()->getChildrenPages($this);
    }

    /**
     * check if parents are enabled
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function getStatusPath()
    {
        $parents = $this->getParentPages();
        $rootId = Mage::helper('ibrams_cmsextended/page')->getRootPageId();
        foreach ($parents as $parent) {
            if ($parent->getId() == $rootId) {
                continue;
            }
            if (!$parent->getStatus()) {
                return false;
            }
        }
        return $this->getStatus();
    }

    /**
     * check if comments are allowed
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getAllowComments()
    {
        if ($this->getData('allow_comment') == Ibrams_CmsExtended_Model_Adminhtml_Source_Yesnodefault::NO) {
            return false;
        }
        if ($this->getData('allow_comment') == Ibrams_CmsExtended_Model_Adminhtml_Source_Yesnodefault::YES) {
            return true;
        }
        return Mage::getStoreConfigFlag('ibrams_cmsextended/page/allow_comment');
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        $values['in_rss'] = 1;
        $values['allow_comment'] = Ibrams_CmsExtended_Model_Adminhtml_Source_Yesnodefault::USE_DEFAULT;
        $values['version'] = '1.0';
        $values['startdate'] = '2015-01-01';
        $values['enddate'] = '2019-01-01';
        $values['permittedroleactions'] = '1';

        return $values;
    }
    
    /**
      * get accessroles
      *
      * @access public
      * @return array
      * @author Ultimate Module Creator
      */
    public function getAccessroles()
    {
        if (!$this->getData('accessroles')) {
            return explode(',', $this->getData('accessroles'));
        }
        return $this->getData('accessroles');
    }
    /**
      * get permittedroleactions
      *
      * @access public
      * @return array
      * @author Ultimate Module Creator
      */
    public function getPermittedroleactions()
    {
        if (!$this->getData('permittedroleactions')) {
            return explode(',', $this->getData('permittedroleactions'));
        }
        return $this->getData('permittedroleactions');
    }
}

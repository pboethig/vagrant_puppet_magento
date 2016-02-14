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
 * Cmspage model
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Cmspage extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'ibrams_cmsextended_cmspage';
    const CACHE_TAG = 'ibrams_cmsextended_cmspage';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'ibrams_cmsextended_cmspage';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'cmspage';
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
        $this->_init('ibrams_cmsextended/cmspage');
    }

    /**
     * before save cmspage
     *
     * @access protected
     * @return Ibrams_CmsExtended_Model_Cmspage
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
     * get the url to the cmspage details page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getCmspageUrl()
    {
        if ($this->getUrlKey()) {
            $urlKey = '';
            if ($prefix = Mage::getStoreConfig('ibrams_cmsextended/cmspage/url_prefix')) {
                $urlKey .= $prefix.'/';
            }
            $urlKey .= $this->getUrlKey();
            if ($suffix = Mage::getStoreConfig('ibrams_cmsextended/cmspage/url_suffix')) {
                $urlKey .= '.'.$suffix;
            }
            return Mage::getUrl('', array('_direct'=>$urlKey));
        }
        return Mage::getUrl('ibrams_cmsextended/cmspage/view', array('id'=>$this->getId()));
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
     * get the cmspage htmlcontent
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHtmlcontent()
    {
        $htmlcontent = $this->getData('htmlcontent');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($htmlcontent);
        return $html;
    }

    /**
     * save cmspage relation
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Cmspage
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        $this->getProductInstance()->saveCmspageRelation($this);
        $this->getCategoryInstance()->saveCmspageRelation($this);
        return parent::_afterSave();
    }

    /**
     * get product relation model
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Cmspage_Product
     * @author Ultimate Module Creator
     */
    public function getProductInstance()
    {
        if (!$this->_productInstance) {
            $this->_productInstance = Mage::getSingleton('ibrams_cmsextended/cmspage_product');
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
     * @return Ibrams_CmsExtended_Resource_Cmspage_Product_Collection
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
     * @return Ibrams_CmsExtended_Model_Cmspage_Category
     * @author Ultimate Module Creator
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('ibrams_cmsextended/cmspage_category');
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
     * @return Ibrams_CmsExtended_Resource_Cmspage_Category_Collection
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
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Tree
     * @author Ultimate Module Creator
     */
    public function getTreeModel()
    {
        return Mage::getResourceModel('ibrams_cmsextended/cmspage_tree');
    }

    /**
     * get tree model instance
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Tree
     * @author Ultimate Module Creator
     */
    public function getTreeModelInstance()
    {
        if (is_null($this->_treeModel)) {
            $this->_treeModel = Mage::getResourceSingleton('ibrams_cmsextended/cmspage_tree');
        }
        return $this->_treeModel;
    }

    /**
     * Move cmspage
     *
     * @access public
     * @param   int $parentId new parent cmspage id
     * @param   int $afterCmspageId cmspage id after which we have put current cmspage
     * @return  Ibrams_CmsExtended_Model_Cmspage
     * @author Ultimate Module Creator
     */
    public function move($parentId, $afterCmspageId)
    {
        $parent = Mage::getModel('ibrams_cmsextended/cmspage')->load($parentId);
        if (!$parent->getId()) {
            Mage::throwException(
                Mage::helper('ibrams_cmsextended')->__(
                    'Cmspage move operation is not possible: the new parent cmspage was not found.'
                )
            );
        }
        if (!$this->getId()) {
            Mage::throwException(
                Mage::helper('ibrams_cmsextended')->__(
                    'Cmspage move operation is not possible: the current cmspage was not found.'
                )
            );
        } elseif ($parent->getId() == $this->getId()) {
            Mage::throwException(
                Mage::helper('ibrams_cmsextended')->__(
                    'Cmspage move operation is not possible: parent cmspage is equal to child cmspage.'
                )
            );
        }
        $this->setMovedCmspageId($this->getId());
        $eventParams = array(
            $this->_eventObject => $this,
            'parent'            => $parent,
            'cmspage_id'     => $this->getId(),
            'prev_parent_id'    => $this->getParentId(),
            'parent_id'         => $parentId
        );
        $moveComplete = false;
        $this->_getResource()->beginTransaction();
        try {
            $this->getResource()->changeParent($this, $parent, $afterCmspageId);
            $this->_getResource()->commit();
            $this->setAffectedCmspageIds(array($this->getId(), $this->getParentId(), $parentId));
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
     * Get the parent cmspage
     *
     * @access public
     * @return  Ibrams_CmsExtended_Model_Cmspage
     * @author Ultimate Module Creator
     */
    public function getParentCmspage()
    {
        if (!$this->hasData('parent_cmspage')) {
            $this->setData(
                'parent_cmspage',
                Mage::getModel('ibrams_cmsextended/cmspage')->load($this->getParentId())
            );
        }
        return $this->_getData('parent_cmspage');
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
     * Get all parent cmspages ids
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
     * Get all cmspages children
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
     * Get all cmspages children
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getChildCmspages()
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
     * Get array cmspages ids which are part of cmspage path
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
     * Verify cmspage ids
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
     * check if cmspage has children
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
     * check if cmspage can be deleted
     *
     * @access protected
     * @return Ibrams_CmsExtended_Model_Cmspage
     * @author Ultimate Module Creator
     */
    protected function _beforeDelete()
    {
        if ($this->getResource()->isForbiddenToDelete($this->getId())) {
            Mage::throwException(Mage::helper('ibrams_cmsextended')->__("Can't delete root cmspage."));
        }
        return parent::_beforeDelete();
    }

    /**
     * get the cmspages
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage $parent
     * @param int $recursionLevel
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @author Ultimate Module Creator
     */
    public function getCmspages($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true)
    {
        return $this->getResource()->getCmspages($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
    }

    /**
     * Return parent cmspages of current cmspage
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getParentCmspages()
    {
        return $this->getResource()->getParentCmspages($this);
    }

    /**
     * Return children cmspages of current cmspage
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getChildrenCmspages()
    {
        return $this->getResource()->getChildrenCmspages($this);
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
        $parents = $this->getParentCmspages();
        $rootId = Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId();
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
        return Mage::getStoreConfigFlag('ibrams_cmsextended/cmspage/allow_comment');
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
        $values['htmlcontent'] = 'Place your content here';
        $values['layout'] = '2';

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

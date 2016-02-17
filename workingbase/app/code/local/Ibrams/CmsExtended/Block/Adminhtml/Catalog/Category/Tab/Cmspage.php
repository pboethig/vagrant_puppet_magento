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
 * categories - cmspage relation edit block
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Adminhtml_Catalog_Category_Tab_Cmspage extends Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Tree
{
    protected $_cmspageIds = null;
    protected $_selectedNodes = null;

    /**
     * constructor
     * Specify template to use
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ibrams_cmsextended/catalog/category/tab/cmspages.phtml');
    }

    /**
     * Retrieve currently edited category
     *
     * @access public
     * @return Mage_Catalog_Model_Entity
     * @author Ultimate Module Creator
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * Return array with cmspages IDs which the category is linked to
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getCmspagesIds()
    {
        if (is_null($this->_cmspagesIds)) {
            $cmspages = Mage::helper('ibrams_cmsextended/category')
                ->getSelectedCmspages($this->getCategory());
            $ids = array();
            foreach ($cmspages as $cmspage) {
                $ids[] = $cmspage->getId();
            }
            $this->_cmspageIds = $ids;
        }
        return $this->_cmspageIds;
    }

    /**
     * Forms string out of getCmspageIds()
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getIdsString()
    {
        return implode(',', $this->getCmspagesIds());
    }

    /**
     * Returns root node and sets 'checked' flag (if necessary)
     *
     * @access public
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getRootNode()
    {
        $root = $this->getRoot();
        if ($root && in_array($root->getId(), $this->getCmspagesIds())) {
            $root->setChecked(true);
        }
        return $root;
    }

    /**
     * Returns root node
     *
     * @param Ibrams_CmsExtended_Model_Cmspage|null $parentNodeCmspage
     * @param int  $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getRoot($parentNodeCmspage = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeCmspage) && $parentNodeCmspage->getId()) {
            return $this->getNode($parentNodeCmspage, $recursionLevel);
        }
        $root = Mage::registry('cmspage_root');
        if (is_null($root)) {
            $rootId = Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId();
            $ids = $this->getSelectedCmspagePathIds($rootId);
            $tree = Mage::getResourceSingleton('ibrams_cmsextended/cmspage_tree')
                ->loadByIds($ids, false, false);
            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getCmspageCollection());
            $root = $tree->getNodeById($rootId);
            Mage::register('cmspage_root', $root);
        }
        return $root;
    }

    /**
     * Returns array with configuration of current node
     *
     * @access public
     * @param Varien_Data_Tree_Node $node
     * @param int $level How deep is the node in the tree
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _getNodeJson($node, $level = 1)
    {
        $item = parent::_getNodeJson($node, $level);
        if ($this->_isParentSelectedCmspage($node)) {
            $item['expanded'] = true;
        }
        if (in_array($node->getId(), $this->getCmspagesIds())) {
            $item['checked'] = true;
        }
        return $item;
    }

    /**
     * Returns whether $node is a parent (not exactly direct) of a selected node
     *
     * @access public
     * @param Varien_Data_Tree_Node $node
     * @return bool
     * @author Ultimate Module Creator
     */
    protected function _isParentSelectedCmspage($node)
    {
        $result = false;
        // Contains string with all cmspage IDs of children (not exactly direct) of the node
        $allChildren = $node->getAllChildren();
        if ($allChildren) {
            $selectedCmspageIds = $this->getCmspageIds();
            $allChildrenArr = explode(',', $allChildren);
            for ($i = 0, $cnt = count($selectedCmspageIds); $i < $cnt; $i++) {
                $isSelf = $node->getId() == $selectedCmspageIds[$i];
                if (!$isSelf && in_array($selectedCmspageIds[$i], $allChildrenArr)) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Returns array with nodes those are selected (contain current cmspage)
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _getSelectedNodes()
    {
        if ($this->_selectedNodes === null) {
            $this->_selectedNodes = array();
            $root = $this->getRoot();
            foreach ($this->getCmspageIds() as $cmspageId) {
                if ($root) {
                    $this->_selectedNodes[] = $root->getTree()->getNodeById($cmspageId);
                }
            }
        }
        return $this->_selectedNodes;
    }

    /**
     * Returns JSON-encoded array of cmspage children
     *
     * @access public
     * @param int $cmspageId
     * @return string
     * @author Ultimate Module Creator
     */
    public function getCmspageChildrenJson($cmspageId)
    {
        $cmspage = Mage::getModel('ibrams_cmsextended/cmspage')->load($cmspageId);
        $node = $this->getRoot($cmspage, 1)->getTree()->getNodeById($cmspageId);
        if (!$node || !$node->hasChildren()) {
            return '[]';
        }
        $children = array();
        foreach ($node->getChildren() as $child) {
            $children[] = $this->_getNodeJson($child);
        }
        return Mage::helper('core')->jsonEncode($children);
    }

    /**
     * Returns URL for loading tree
     *
     * @access public
     * @param null $expanded
     * @return string
     * @author Ultimate Module Creator
     */
    public function getLoadTreeUrl($expanded = null)
    {
        return $this->getUrl(
            '*/cmsextended_cmspage_catalog_category/cmspagesJson',
            array('_current' => true)
        );
    }

    /**
     * Return distinct path ids of selected cmspage
     *
     * @access public
     * @param mixed $rootId Root cmspage Id for context
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedCmspagePathIds($rootId = false)
    {
        $ids = array();
        $cmspageIds = $this->getCmspageIds();
        if (empty($cmspageIds)) {
            return array();
        }
        $collection = Mage::getResourceModel('ibrams_cmsextended/cmspage_collection');
        if ($rootId) {
            $collection->addFieldToFilter('parent_id', $rootId);
        } else {
            $collection->addFieldToFilter('entity_id', array('in'=>$cmspageIds));
        }

        foreach ($collection as $item) {
            if ($rootId && !in_array($rootId, $item->getPathIds())) {
                continue;
            }
            foreach ($item->getPathIds() as $id) {
                if (!in_array($id, $ids)) {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }

    /**
     * Get node label
     *
     * @access public
     * @param Varien_Object $node
     * @return string
     * @author Ultimate Module Creator
     */
    public function buildNodeName($node)
    {
        $result = parent::buildNodeName($node);
        $result .= '<a target="_blank" href="'.
            $this->getUrl('adminhtml/cmsextended_cmspage/index', array('id'=>$node->getId(), 'clear'=>1)).
            '"><em>'.$this->__(' - Edit').'</em></a>';
        return $result;
    }
}

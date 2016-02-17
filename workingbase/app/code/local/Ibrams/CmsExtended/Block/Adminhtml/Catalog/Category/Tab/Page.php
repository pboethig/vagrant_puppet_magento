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
 * categories - page relation edit block
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Adminhtml_Catalog_Category_Tab_Page extends Ibrams_CmsExtended_Block_Adminhtml_Page_Tree
{
    protected $_pageIds = null;
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
        $this->setTemplate('ibrams_cmsextended/catalog/category/tab/pages.phtml');
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
     * Return array with pages IDs which the category is linked to
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getPagesIds()
    {
        if (is_null($this->_pagesIds)) {
            $pages = Mage::helper('ibrams_cmsextended/category')
                ->getSelectedPages($this->getCategory());
            $ids = array();
            foreach ($pages as $page) {
                $ids[] = $page->getId();
            }
            $this->_pageIds = $ids;
        }
        return $this->_pageIds;
    }

    /**
     * Forms string out of getPageIds()
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getIdsString()
    {
        return implode(',', $this->getPagesIds());
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
        if ($root && in_array($root->getId(), $this->getPagesIds())) {
            $root->setChecked(true);
        }
        return $root;
    }

    /**
     * Returns root node
     *
     * @param Ibrams_CmsExtended_Model_Page|null $parentNodePage
     * @param int  $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getRoot($parentNodePage = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodePage) && $parentNodePage->getId()) {
            return $this->getNode($parentNodePage, $recursionLevel);
        }
        $root = Mage::registry('page_root');
        if (is_null($root)) {
            $rootId = Mage::helper('ibrams_cmsextended/page')->getRootPageId();
            $ids = $this->getSelectedPagePathIds($rootId);
            $tree = Mage::getResourceSingleton('ibrams_cmsextended/page_tree')
                ->loadByIds($ids, false, false);
            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getPageCollection());
            $root = $tree->getNodeById($rootId);
            Mage::register('page_root', $root);
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
        if ($this->_isParentSelectedPage($node)) {
            $item['expanded'] = true;
        }
        if (in_array($node->getId(), $this->getPagesIds())) {
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
    protected function _isParentSelectedPage($node)
    {
        $result = false;
        // Contains string with all page IDs of children (not exactly direct) of the node
        $allChildren = $node->getAllChildren();
        if ($allChildren) {
            $selectedPageIds = $this->getPageIds();
            $allChildrenArr = explode(',', $allChildren);
            for ($i = 0, $cnt = count($selectedPageIds); $i < $cnt; $i++) {
                $isSelf = $node->getId() == $selectedPageIds[$i];
                if (!$isSelf && in_array($selectedPageIds[$i], $allChildrenArr)) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Returns array with nodes those are selected (contain current page)
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
            foreach ($this->getPageIds() as $pageId) {
                if ($root) {
                    $this->_selectedNodes[] = $root->getTree()->getNodeById($pageId);
                }
            }
        }
        return $this->_selectedNodes;
    }

    /**
     * Returns JSON-encoded array of page children
     *
     * @access public
     * @param int $pageId
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPageChildrenJson($pageId)
    {
        $page = Mage::getModel('ibrams_cmsextended/page')->load($pageId);
        $node = $this->getRoot($page, 1)->getTree()->getNodeById($pageId);
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
            '*/cmsextended_page_catalog_category/pagesJson',
            array('_current' => true)
        );
    }

    /**
     * Return distinct path ids of selected page
     *
     * @access public
     * @param mixed $rootId Root page Id for context
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedPagePathIds($rootId = false)
    {
        $ids = array();
        $pageIds = $this->getPageIds();
        if (empty($pageIds)) {
            return array();
        }
        $collection = Mage::getResourceModel('ibrams_cmsextended/page_collection');
        if ($rootId) {
            $collection->addFieldToFilter('parent_id', $rootId);
        } else {
            $collection->addFieldToFilter('entity_id', array('in'=>$pageIds));
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
            $this->getUrl('adminhtml/cmsextended_page/index', array('id'=>$node->getId(), 'clear'=>1)).
            '"><em>'.$this->__(' - Edit').'</em></a>';
        return $result;
    }
}

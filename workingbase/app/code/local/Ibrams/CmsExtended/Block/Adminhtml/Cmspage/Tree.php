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
 * Cmspage admin tree block
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Tree extends Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Abstract
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ibrams_cmsextended/cmspage/tree.phtml');
        $this->setUseAjax(true);
        $this->_withProductCount = true;
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Tree
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        $addUrl = $this->getUrl(
            "*/*/add",
            array(
                '_current'=>true,
                'id'=>null,
                '_query' => false
            )
        );

        $this->setChild(
            'add_sub_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('ibrams_cmsextended')->__('Add Child Cmspage'),
                        'onclick' => "addNew('".$addUrl."', false)",
                        'class'   => 'add',
                        'id'      => 'add_child_cmspage_button',
                        'style'   => $this->canAddChild() ? '' : 'display: none;'
                    )
                )
        );

        $this->setChild(
            'add_root_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('ibrams_cmsextended')->__('Add Root Cmspage'),
                        'onclick' => "addNew('".$addUrl."', true)",
                        'class'   => 'add',
                        'id'      => 'add_root_cmspage_button'
                    )
                )
        );
        return parent::_prepareLayout();
    }

    /**
     * get the cmspage collection
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Collection
     * @author Ultimate Module Creator
     */
    public function getCmspageCollection()
    {
        $collection = $this->getData('cmspage_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('ibrams_cmsextended/cmspage')->getCollection();
            $this->setData('cmspage_collection', $collection);
        }
        return $collection;
    }

    /**
     * get html for add root button
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getAddRootButtonHtml()
    {
        return $this->getChildHtml('add_root_button');
    }

    /**
     * get html for add child button
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getAddSubButtonHtml()
    {
        return $this->getChildHtml('add_sub_button');
    }

    /**
     * get html for expand button
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    /**
     * get html for add collapse button
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    /**
     * get url for tree load
     *
     * @access public
     * @param mxed $expanded
     * @return string
     * @author Ultimate Module Creator
     */
    public function getLoadTreeUrl($expanded=null)
    {
        $params = array('_current' => true, 'id' => null, 'store' => null);
        if ((is_null($expanded) &&
            Mage::getSingleton('admin/session')->getCmspageIsTreeWasExpanded()) ||
            $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/cmspagesJson', $params);
    }

    /**
     * get url for loading nodes
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getNodesUrl()
    {
        return $this->getUrl('*/cmsextended_cmspages/jsonTree');
    }

    /**
     * check if tree is expanded
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getIsWasExpanded()
    {
        return Mage::getSingleton('admin/session')->getCmspageIsTreeWasExpanded();
    }

    /**
     * get url for moving cmspage
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/cmsextended_cmspage/move');
    }

    /**
     * get the tree as json
     *
     * @access public
     * @param mixed $parentNodeCmspage
     * @return string
     * @author Ultimate Module Creator
     */
    public function getTree($parentNodeCmspage = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parentNodeCmspage));
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();
        return $tree;
    }

    /**
     * get the tree as json
     *
     * @access public
     * @param mixed $parentNodeCmspage
     * @return string
     * @author Ultimate Module Creator
     */
    public function getTreeJson($parentNodeCmspage = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parentNodeCmspage));
        $json = Mage::helper('core')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }

    /**
     * Get JSON of array of cmspages, that are breadcrumbs for specified cmspage path
     *
     * @access public
     * @param string $path
     * @param string $javascriptVarName
     * @return string
     * @author Ultimate Module Creator
     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }

        $cmspages = Mage::getResourceSingleton('ibrams_cmsextended/cmspage_tree')
            ->loadBreadcrumbsArray($path);
        if (empty($cmspages)) {
            return '';
        }
        foreach ($cmspages as $key => $cmspage) {
            $cmspages[$key] = $this->_getNodeJson($cmspage);
        }
        return
            '<script type="text/javascript">'
            . $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($cmspages) . ';'
            . ($this->canAddChild() ? '$("add_child_cmspage_button").show();' : '$("add_child_cmspage_button").hide();')
            . '</script>';
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @access protected
     * @param Varien_Data_Tree_Node|array $node
     * @param int $level
     * @return string
     * @author Ultimate Module Creator
     */
    protected function _getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Varien_Data_Tree_Node($node, 'entity_id', new Varien_Data_Tree);
        }
        $item = array();
        $item['text'] = $this->buildNodeName($node);
        $item['id']   = $node->getId();
        $item['path'] = $node->getData('path');
        $item['cls']  = 'folder';
        if ($node->getStatus()) {
            $item['cls'] .= ' active-category';
        } else {
            $item['cls'] .= ' no-active-category';
        }
        $item['allowDrop'] = true;
        $item['allowDrag'] = true;
        if ((int)$node->getChildrenCount()>0) {
            $item['children'] = array();
        }
        $isParent = $this->_isParentSelectedCmspage($node);
        if ($node->hasChildren()) {
            $item['children'] = array();
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level+1);
                }
            }
        }
        if ($isParent || $node->getLevel() < 1) {
            $item['expanded'] = true;
        }
        return $item;
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
        $result = $this->escapeHtml($node->getName());
        return $result;
    }

    /**
     * check if entity is movable
     *
     * @access protected
     * @param Varien_Object $node
     * @return bool
     * @author Ultimate Module Creator
     */
    protected function _isCmspageMoveable($node)
    {
        return true;
    }

    /**
     * check if parent is selected
     *
     * @access protected
     * @param Varien_Object $node
     * @return bool
     * @author Ultimate Module Creator
     */
    protected function _isParentSelectedCmspage($node)
    {
        if ($node && $this->getCmspage()) {
            $pathIds = $this->getCmspage()->getPathIds();
            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if page loaded by outside link to cmspage edit
     *
     * @access public
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function isClearEdit()
    {
        return (bool) $this->getRequest()->getParam('clear');
    }

    /**
     * Check availability of adding root cmspage
     *
     * @access public
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function canAddRootCmspage()
    {
        return true;
    }

    /**
     * Check availability of adding child cmspage
     *
     * @access public
     * @return boolean
     */
    public function canAddChild()
    {
        return true;
    }
}

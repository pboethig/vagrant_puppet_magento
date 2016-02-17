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
 * Cmspage admin block abstract
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * get current cmspage
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Entity
     * @author Ultimate Module Creator
     */
    public function getCmspage()
    {
        return Mage::registry('cmspage');
    }

    /**
     * get current cmspage id
     *
     * @access public
     * @return int
     * @author Ultimate Module Creator
     */
    public function getCmspageId()
    {
        if ($this->getCmspage()) {
            return $this->getCmspage()->getId();
        }
        return null;
    }

    /**
     * get current cmspage name
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getCmspageName()
    {
        return $this->getCmspage()->getName();
    }

    /**
     * get current cmspage path
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getCmspagePath()
    {
        if ($this->getCmspage()) {
            return $this->getCmspage()->getPath();
        }
        return Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId();
    }

    /**
     * check if there is a root cmspage
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function hasRootCmspage()
    {
        $root = $this->getRoot();
        if ($root && $root->getId()) {
            return true;
        }
        return false;
    }

    /**
     * get the root
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage|null $parentNodeCmspage
     * @param int $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getRoot($parentNodeCmspage = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeCmspage) && $parentNodeCmspage->getId()) {
            return $this->getNode($parentNodeCmspage, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) {
            $rootId = Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId();
            $tree = Mage::getResourceSingleton('ibrams_cmsextended/cmspage_tree')
                ->load(null, $recursionLevel);
            if ($this->getCmspage()) {
                $tree->loadEnsuredNodes($this->getCmspage(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getCmspageCollection());
            $root = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId()) {
                $root->setName(Mage::helper('ibrams_cmsextended')->__('Root'));
            }
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * Get and register cmspages root by specified cmspages IDs
     *
     * @accsess public
     * @param array $ids
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getRootByIds($ids)
    {
        $root = Mage::registry('root');
        if (null === $root) {
            $cmspageTreeResource = Mage::getResourceSingleton('ibrams_cmsextended/cmspage_tree');
            $ids     = $cmspageTreeResource->getExistingCmspageIdsBySpecifiedIds($ids);
            $tree   = $cmspageTreeResource->loadByIds($ids);
            $rootId = Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId();
            $root   = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId()) {
                $root->setName(Mage::helper('ibrams_cmsextended')->__('Root'));
            }
            $tree->addCollectionData($this->getCmspageCollection());
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * get specific node
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage $parentNodeCmspage
     * @param $int $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getNode($parentNodeCmspage, $recursionLevel = 2)
    {
        $tree = Mage::getResourceModel('ibrams_cmsextended/cmspage_tree');
        $nodeId     = $parentNodeCmspage->getId();
        $parentId   = $parentNodeCmspage->getParentId();
        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);
        if ($node && $nodeId != Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId()) {
            $node->setIsVisible(true);
        } elseif ($node && $node->getId() == Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId()) {
            $node->setName(Mage::helper('ibrams_cmsextended')->__('Root'));
        }
        $tree->addCollectionData($this->getCmspageCollection());
        return $node;
    }

    /**
     * get url for saving data
     *
     * @access public
     * @param array $args
     * @return string
     * @author Ultimate Module Creator
     */
    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    /**
     * get url for edit
     *
     * @access public
     * @param array $args
     * @return string
     * @author Ultimate Module Creator
     */
    public function getEditUrl()
    {
        return $this->getUrl(
            "*/cmsextended_cmspage/edit",
            array('_current' => true, '_query'=>false, 'id' => null, 'parent' => null)
        );
    }

    /**
     * Return root ids
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getRootIds()
    {
        return array(Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId());
    }
}

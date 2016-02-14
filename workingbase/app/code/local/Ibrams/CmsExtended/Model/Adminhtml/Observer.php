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
 * Adminhtml observer
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Adminhtml_Observer
{
    /**
     * check if tab can be added
     *
     * @access protected
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     * @author Ultimate Module Creator
     */
    protected function _canAddTab($product)
    {
        if ($product->getId()) {
            return true;
        }
        if (!$product->getAttributeSetId()) {
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable') {
            if ($request->getParam('attributes')) {
                return true;
            }
        }
        return false;
    }

    /**
     * add the cmspage tab to products
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Ibrams_CmsExtended_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function addProductCmspageBlock($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $product = Mage::registry('product');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs && $this->_canAddTab($product)) {
            $block->addTab(
                'cmspages',
                array(
                    'label' => Mage::helper('ibrams_cmsextended')->__('Cmspages'),
                    'url'   => Mage::helper('adminhtml')->getUrl(
                        'adminhtml/cmsextended_cmspage_catalog_product/cmspages',
                        array('_current' => true)
                    ),
                    'class' => 'ajax',
                )
            );
        }
        return $this;
    }

    /**
     * save cmspage - product relation
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Ibrams_CmsExtended_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function saveProductCmspageData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('cmspage_ids', -1);
        if ($post != '-1') {
            $post = explode(',', $post);
            $post = array_unique($post);
            $product = $observer->getEvent()->getProduct();
            Mage::getResourceSingleton('ibrams_cmsextended/cmspage_product')
                ->saveProductRelation($product, $post);
        }
        return $this;
    }

    /**
     * add the cmspage tab to categories
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Ibrams_CmsExtended_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function addCategoryCmspageBlock($observer)
    {
        $tabs = $observer->getEvent()->getTabs();
        $content = $tabs->getLayout()->createBlock(
            'ibrams_cmsextended/adminhtml_catalog_category_tab_cmspage',
            'category.cmspage.grid'
        )->toHtml();
        $tabs->addTab(
            'cmspage',
            array(
                'label'   => Mage::helper('ibrams_cmsextended')->__('Cmspages'),
                'content' => $content,
            )
        );
        return $this;
    }

    /**
     * save cmspage - category relation
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Ibrams_CmsExtended_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function saveCategoryCmspageData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('cmspage_ids', -1);
        if ($post != '-1') {
            $post = explode(',', $post);
            $post = array_unique($post);
            $category = $observer->getEvent()->getCategory();
            Mage::getResourceSingleton('ibrams_cmsextended/cmspage_category')
                ->saveCategoryRelation($category, $post);
        }
        return $this;
    }
}

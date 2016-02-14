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
 * Cmspage - product controller
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
require_once ("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class Ibrams_CmsExtended_Adminhtml_Cmsextended_Cmspage_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{

    /**
     * cmspages action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function cmspagesAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * cmspages json action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function cmspagesJsonAction()
    {
        $product = $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'ibrams_cmsextended/adminhtml_catalog_product_edit_tab_cmspage'
            )
            ->getCmspageChildrenJson($this->getRequest()->getParam('cmspage'))
        );
    }
}

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
 * Cmspage admin edit tabs
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        $this->setId('cmspage_info_tabs');
        $this->setDestElementId('cmspage_tab_content');
        $this->setTitle(Mage::helper('ibrams_cmsextended')->__('Cmspage'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    /**
     * Prepare Layout Content
     *
     * @access public
     * @return Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Edit_Tabs
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'content_cmspage',
            array(
                'label'   => Mage::helper('ibrams_cmsextended')->__('Content'),
                'title'   => Mage::helper('ibrams_cmsextended')->__('Content'),
                'content' => $this->getLayout()->createBlock(
                    'ibrams_cmsextended/adminhtml_cmspage_edit_tab_content'
                )
                    ->toHtml(),
            )
        );

        $this->addTab(
            'form_cmspage',
            array(
                'label'   => Mage::helper('ibrams_cmsextended')->__('Configs'),
                'title'   => Mage::helper('ibrams_cmsextended')->__('Configs'),
                'content' => $this->getLayout()->createBlock(
                    'ibrams_cmsextended/adminhtml_cmspage_edit_tab_form'
                )
                ->toHtml(),
            )
        );

        $this->addTab(
            'layout_cmspage',
            array(
                'label'   => Mage::helper('ibrams_cmsextended')->__('Layout'),
                'title'   => Mage::helper('ibrams_cmsextended')->__('Layout'),
                'content' => $this->getLayout()->createBlock(
                    'ibrams_cmsextended/adminhtml_cmspage_edit_tab_layout'
                )
                    ->toHtml(),
            )
        );

        $this->addTab(
            'permissions_cmspage',
            array(
                'label'   => Mage::helper('ibrams_cmsextended')->__('Permissions'),
                'title'   => Mage::helper('ibrams_cmsextended')->__('Permissions'),
                'content' => $this->getLayout()->createBlock(
                    'ibrams_cmsextended/adminhtml_cmspage_edit_tab_permissions'
                )
                    ->toHtml(),
            )
        );


        $this->addTab(
            'form_meta_cmspage',
            array(
                'label'   => Mage::helper('ibrams_cmsextended')->__('Meta'),
                'title'   => Mage::helper('ibrams_cmsextended')->__('Meta'),
                'content' => $this->getLayout()->createBlock(
                    'ibrams_cmsextended/adminhtml_cmspage_edit_tab_meta'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_cmspage',
                array(
                    'label'   => Mage::helper('ibrams_cmsextended')->__('Store views'),
                    'title'   => Mage::helper('ibrams_cmsextended')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'ibrams_cmsextended/adminhtml_cmspage_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        $this->addTab(
            'products',
            array(
                'label'   => Mage::helper('ibrams_cmsextended')->__('Associated Products'),
                'content' => $this->getLayout()->createBlock(
                    'ibrams_cmsextended/adminhtml_cmspage_edit_tab_product',
                    'cmspage.product.grid'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'categories',
            array(
                'label'   => Mage::helper('ibrams_cmsextended')->__('Associated Categories'),
                'content' => $this->getLayout()->createBlock(
                    'ibrams_cmsextended/adminhtml_cmspage_edit_tab_categories',
                    'cmspage.category.tree'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve cmspage entity
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Cmspage
     * @author Ultimate Module Creator
     */
    public function getCmspage()
    {
        return Mage::registry('current_cmspage');
    }
}

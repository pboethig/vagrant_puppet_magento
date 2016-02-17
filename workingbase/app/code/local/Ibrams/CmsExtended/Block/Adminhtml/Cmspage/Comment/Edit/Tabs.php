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
 * Cmspage comment admin edit tabs
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Comment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('cmspage_comment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ibrams_cmsextended')->__('Cmspage Comment'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_cmspage_comment',
            array(
                'label'   => Mage::helper('ibrams_cmsextended')->__('Cmspage comment'),
                'title'   => Mage::helper('ibrams_cmsextended')->__('Cmspage comment'),
                'content' => $this->getLayout()->createBlock(
                    'ibrams_cmsextended/adminhtml_cmspage_comment_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_cmspage_comment',
                array(
                    'label'   => Mage::helper('ibrams_cmsextended')->__('Store views'),
                    'title'   => Mage::helper('ibrams_cmsextended')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'ibrams_cmsextended/adminhtml_cmspage_comment_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve comment
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Cmspage_Comment
     * @author Ultimate Module Creator
     */
    public function getComment()
    {
        return Mage::registry('current_comment');
    }
}

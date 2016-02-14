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
 * Cmspage comment admin edit form
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Comment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
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
        $this->_blockGroup = 'ibrams_cmsextended';
        $this->_controller = 'adminhtml_cmspage_comment';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('ibrams_cmsextended')->__('Save Cmspage comment')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('ibrams_cmsextended')->__('Delete Cmspage comment')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'        => Mage::helper('ibrams_cmsextended')->__('Save And Continue Edit'),
                'onclick'    => 'saveAndContinueEdit()',
                'class'        => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeaderText()
    {
        if (Mage::registry('comment_data') && Mage::registry('comment_data')->getId()) {
            return Mage::helper('ibrams_cmsextended')->__(
                "Edit Cmspage comment '%s'",
                $this->escapeHtml(Mage::registry('comment_data')->getTitle())
            );
        }
        return '';
    }
}

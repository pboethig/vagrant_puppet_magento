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
 * Cmspage edit form tab
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Edit_Tab_Layout extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('cmspage_');
        $form->setFieldNameSuffix('cmspage');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'cmspage_layout',
            array('legend' => Mage::helper('ibrams_cmsextended')->__('Layout'))
        );

        $fieldset->addField(
            'layout',
            'select',
            array(
                'label' => Mage::helper('ibrams_cmsextended')->__('layout'),
                'name'  => 'layout',
                'required'  => true,
                'class' => 'required-entry',

                'values'=> Mage::getModel('ibrams_cmsextended/cmspage_attribute_source_layout')->getAllOptions(true),
            )
        );

        $fieldset->addField(
            'layoutupdate',
            'textarea',
            array(
                'label' => Mage::helper('ibrams_cmsextended')->__('layoutupdate'),
                'name'  => 'layoutupdate',

            )
        );

        $form->addValues($this->getCmspage()->getData());
        return parent::_prepareForm();
    }

    /**
     * get the current cmspage
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Cmspage
     */
    public function getCmspage()
    {
        return Mage::registry('cmspage');
    }
}

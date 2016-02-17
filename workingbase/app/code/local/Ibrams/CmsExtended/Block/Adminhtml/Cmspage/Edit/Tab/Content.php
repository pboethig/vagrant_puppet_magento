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
class Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Edit_Tab_Content
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('cmspage_');
        $form->setFieldNameSuffix('cmspage');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'cmspage_content',
            array('legend' => Mage::helper('ibrams_cmsextended')->__('Content'),'class'=>'fieldset-wide')
        );

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array('tab_id' => $this->getTabId())
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('ibrams_cmsextended')->__('name'),
                'name'  => 'name',
                'required'  => true,
                'class' => 'required-entry',

            )
        );

        $fieldset->addField(
            'version',
            'text',
            array(
                'label' => Mage::helper('ibrams_cmsextended')->__('version'),
                'name'  => 'version',
                'note'	=> $this->__('version of the page'),
                'required'  => true,
                'class' => 'required-entry',

            )
        );


        $fieldset->addField('content_heading', 'text', array(
            'name'      => 'content_heading',
            'label'     => Mage::helper('cms')->__('Content Heading'),
            'title'     => Mage::helper('cms')->__('Content Heading'),
            'disabled'  => $isElementDisabled
        ));


        $fieldset->addField(
            'showassociatedproducts',
            'select',
            array(
                'label' => Mage::helper('ibrams_cmsextended')->__('Show associated products'),
                'name'  => 'showassociatedproducts',
                'note'	=> $this->__('on true assiciated products will be displayed'),
                'required'  => true,
                'class' => 'required-entry',

                'values'=> array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('ibrams_cmsextended')->__('Yes'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('ibrams_cmsextended')->__('No'),
                    ),
                ),
            )
        );

        $fieldset->addField(
            'showassociatecategories',
            'select',
            array(
                'label' => Mage::helper('ibrams_cmsextended')->__('Show associated categories'),
                'name'  => 'showassociatecategories',
                'note'	=> $this->__('on true assiciated categories will be displayed'),
                'required'  => true,
                'class' => 'required-entry',

                'values'=> array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('ibrams_cmsextended')->__('Yes'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('ibrams_cmsextended')->__('No'),
                    ),
                ),
            )
        );


        $contentField = $fieldset->addField('htmlcontent', 'editor', array(
            'name'      => 'htmlcontent',
            'style'     => 'height:36em;',
            'required'  => true,
            'disabled'  => $isElementDisabled,
            'config'    => $wysiwygConfig
        ));

        // Setting custom renderer for content field to remove label column
        $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
            ->setTemplate('cms/page/edit/form/renderer/content.phtml');
        $contentField->setRenderer($renderer);


        $form->addValues($this->getCmspage()->getData());

        $this->setForm($form);

        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_content_prepare_form', array('form' => $form));

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

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('cms')->__('Content');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('cms')->__('Content');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
}

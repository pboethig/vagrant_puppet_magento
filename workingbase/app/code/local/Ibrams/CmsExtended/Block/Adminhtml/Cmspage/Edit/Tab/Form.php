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
class Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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

        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('cmspage_');
        $form->setFieldNameSuffix('cmspage');
        $this->setForm($form);



        $fieldset = $form->addFieldset(
            'cmspage_form',
            array('legend' => Mage::helper('ibrams_cmsextended')->__('Configuration'))
        );
        $fieldset->addType(
            'editor',
            Mage::getConfig()->getBlockClassName('ibrams_cmsextended/adminhtml_helper_wysiwyg')
        );
        if (!$this->getCmspage()->getId()) {
            $parentId = $this->getRequest()->getParam('parent');
            if (!$parentId) {
                $parentId = Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId();
            }
            $fieldset->addField(
                'path',
                'hidden',
                array(
                    'name'  => 'path',
                    'value' => $parentId
                )
            );
        } else {
            $fieldset->addField(
                'id',
                'hidden',
                array(
                    'name'  => 'id',
                    'value' => $this->getCmspage()->getId()
                )
            );
            $fieldset->addField(
                'path',
                'hidden',
                array(
                    'name'  => 'path',
                    'value' => $this->getCmspage()->getPath()
                )
            );
        }



        $fieldset->addField(
            'startdate',
            'date',
            array(
                'label' => Mage::helper('ibrams_cmsextended')->__('startdate'),
                'name'  => 'startdate',
                'note'	=> $this->__('start date of visibility'),

                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'format'  => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            )
        );

        $fieldset->addField(
            'enddate',
            'date',
            array(
                'label' => Mage::helper('ibrams_cmsextended')->__('enddate'),
                'name'  => 'enddate',
                'note'	=> $this->__('eddate of visibility'),

                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'format'  => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            )
        );

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array('tab_id' => $this->getTabId())
        );


        $fieldset->addField(
            'author',
            'text',
            array(
                'label' => Mage::helper('ibrams_cmsextended')->__('author'),
                'name'  => 'author',
                'required'  => true,
                'class' => 'required-entry',

            )
        );
        $fieldset->addField(
            'url_key',
            'text',
            array(
                'label' => Mage::helper('ibrams_cmsextended')->__('Url key'),
                'name'  => 'url_key',
                'note'  => Mage::helper('ibrams_cmsextended')->__('Relative to Website Base URL')
            )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('ibrams_cmsextended')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('ibrams_cmsextended')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('ibrams_cmsextended')->__('Disabled'),
                    ),
                ),
            )
        );
        $fieldset->addField(
            'in_rss',
            'select',
            array(
                'label'  => Mage::helper('ibrams_cmsextended')->__('Show in rss'),
                'name'   => 'in_rss',
                'values' => array(
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
        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                )
            );
            Mage::registry('current_cmspage')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $fieldset->addField(
            'allow_comment',
            'select',
            array(
                'label' => Mage::helper('ibrams_cmsextended')->__('Allow Comments'),
                'name'  => 'allow_comment',
                'values'=> Mage::getModel('ibrams_cmsextended/adminhtml_source_yesnodefault')->toOptionArray()
            )
        );

        $form->addValues($this->getCmspage()->getData());


        $this->setForm($form);


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

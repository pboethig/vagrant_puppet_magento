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
 * Cmspage edit form
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Edit_Form extends Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Abstract
{
    /**
     * Additional buttons on cmspage page
     * @var array
     */
    protected $_additionalButtons = array();
    /**
     * constructor
     *
     * set template
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ibrams_cmsextended/cmspage/edit/form.phtml');
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Edit_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        $cmspage = $this->getCmspage();
        $cmspageId = (int)$cmspage->getId();
        $this->setChild(
            'tabs',
            $this->getLayout()->createBlock('ibrams_cmsextended/adminhtml_cmspage_edit_tabs', 'tabs')
        );
        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('ibrams_cmsextended')->__('Save Cmspage'),
                        'onclick' => "cmspageSubmit('" . $this->getSaveUrl() . "', true)",
                        'class'   => 'save'
                    )
                )
        );
        // Delete button
        if (!in_array($cmspageId, $this->getRootIds())) {

            $this->setChild(
                'delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(
                        array(
                            'label'   => Mage::helper('ibrams_cmsextended')->__('Delete Cmspage'),
                            'onclick' => "cmspageDelete('" . $this->getUrl(
                                '*/*/delete',
                                array('_current' => true)
                            )
                            . "', true, {$cmspageId})",
                            'class'   => 'delete'
                        )
                    )
            );


            $this->addAdditionalButton("preview", array(
                'label'   => Mage::helper('ibrams_cmsextended')->__('Preview Cmspage'),
                'onclick' => "cmspagePreview('" . $this->getUrl(
                        '*/*/preview',
                        array('_current' => true)
                    )
                    . "', true, {$cmspageId})",
                'class'   => 'save'
            ));

        }

        // Reset button
        $resetPath = $cmspage ? '*/*/edit' : '*/*/add';
        $this->setChild(
            'reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label' => Mage::helper('ibrams_cmsextended')->__('Reset'),
                        'onclick'   => "cmspageReset('".$this->getUrl(
                            $resetPath,
                            array('_current'=>true)
                        )
                        ."',true)"
                    )
                )
        );
        return parent::_prepareLayout();
    }

    /**
     * get html for delete button
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * get html for save button
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * get html for reset button
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve additional buttons html
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getAdditionalButtonsHtml()
    {
        $html = '';
        foreach ($this->_additionalButtons as $childName) {
            $html .= $this->getChildHtml($childName);
        }
        return $html;
    }

    /**
     * Add additional button
     *
     * @param string $alias
     * @param array $config
     * @return Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Edit_Form
     * @author Ultimate Module Creator
     */
    public function addAdditionalButton($alias, $config)
    {
        if (isset($config['name'])) {
            $config['element_name'] = $config['name'];
        }
        $this->setChild(
            $alias . '_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->addData($config)
        );
        $this->_additionalButtons[$alias] = $alias . '_button';
        return $this;
    }

    /**
     * Remove additional button
     *
     * @access public
     * @param string $alias
     * @return Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Edit_Form
     * @author Ultimate Module Creator
     */
    public function removeAdditionalButton($alias)
    {
        if (isset($this->_additionalButtons[$alias])) {
            $this->unsetChild($this->_additionalButtons[$alias]);
            unset($this->_additionalButtons[$alias]);
        }
        return $this;
    }

    /**
     * get html for tabs
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    /**
     * get the form header
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeader()
    {
        if ($this->getCmspageId()) {
            return $this->getCmspageName();
        } else {
            return Mage::helper('ibrams_cmsextended')->__('New Root Cmspage');
        }
    }

    /**
     * get the delete url
     *
     * @access public
     * @param array $args
     * @return string
     * @author Ultimate Module Creator
     */
    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }

    /**
     * Return URL for refresh input element 'path' in form
     *
     * @access public
     * @param array $args
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRefreshPathUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/refreshPath', $params);
    }

    /**
     * check if request is ajax
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }

    /**
     * get products json
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getProductsJson()
    {
        $products = $this->getCmspage()->getSelectedProducts();
        if (!empty($products)) {
            $positions = array();
            foreach ($products as $product) {
                $positions[$product->getId()] = $product->getPosition();
            }
            return Mage::helper('core')->jsonEncode($positions);
        }
        return '{}';
    }
}

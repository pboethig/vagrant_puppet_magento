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
 * Cmspage admin widget chooser
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */

class Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Widget_Chooser extends Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Tree
{
    protected $_selectedCmspages = array();

    /**
     * Block construction
     * Defines tree template and init tree params
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ibrams_cmsextended/cmspage/widget/tree.phtml');
    }

    /**
     * Setter
     *
     * @access public
     * @param array $selectedCmspages
     * @return Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Widget_Chooser
     * @author Ultimate Module Creator
     */
    public function setSelectedCmspages($selectedCmspages)
    {
        $this->_selectedCmspages = $selectedCmspages;
        return $this;
    }

    /**
     * Getter
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedCmspages()
    {
        return $this->_selectedCmspages;
    }

    /**
     * Prepare chooser element HTML
     *
     * @access public
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     * @author Ultimate Module Creator
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl(
            '*/cmsextended_cmspage_widget/chooser',
            array('uniq_id' => $uniqId, 'use_massaction' => false)
        );
        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);
        $value = $element->getValue();
        $cmspageId = false;
        if ($value) {
            $cmspageId = $value;
        }
        if ($cmspageId) {
            $label = Mage::getSingleton('ibrams_cmsextended/cmspage')->load($cmspageId)
                ->getName();
            $chooser->setLabel($label);
        }
        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * onClick listener js function
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getNodeClickListener()
    {
        if ($this->getData('node_click_listener')) {
            return $this->getData('node_click_listener');
        }
        if ($this->getUseMassaction()) {
            $js = '
                function (node, e) {
                    if (node.ui.toggleCheck) {
                        node.ui.toggleCheck(true);
                    }
                }
            ';
        } else {
            $chooserJsObject = $this->getId();
            $js = '
                function (node, e) {
                    '.$chooserJsObject.'.setElementValue(node.attributes.id);
                    '.$chooserJsObject.'.setElementLabel(node.text);
                    '.$chooserJsObject.'.close();
                }
            ';
        }
        return $js;
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @access protected
     * @param Varien_Data_Tree_Node|array $node
     * @param int $level
     * @return string
     * @author Ultimate Module Creator
     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = parent::_getNodeJson($node, $level);
        if (in_array($node->getId(), $this->getSelectedCmspages())) {
            $item['checked'] = true;
        }
        return $item;
    }

    /**
     * Tree JSON source URL
     *
     * @access public
     * @param mixed $expanded
     * @return string
     * @author Ultimate Module Creator
     */
    public function getLoadTreeUrl($expanded=null)
    {
        return $this->getUrl(
            '*/cmsextended_cmspage_widget/cmspagesJson',
            array(
                '_current'=>true,
                'uniq_id' => $this->getId(),
                'use_massaction' => $this->getUseMassaction()
            )
        );
    }
}

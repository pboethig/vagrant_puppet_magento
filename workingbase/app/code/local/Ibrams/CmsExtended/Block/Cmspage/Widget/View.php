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
 * Cmspage widget block
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Cmspage_Widget_View extends Mage_Core_Block_Template implements
    Mage_Widget_Block_Interface
{
    protected $_htmlTemplate = 'ibrams_cmsextended/cmspage/widget/view.phtml';

    /**
     * Prepare a for widget
     *
     * @access protected
     * @return Ibrams_CmsExtended_Block_Cmspage_Widget_View
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $cmspageId = $this->getData('cmspage_id');
        if ($cmspageId) {
            $cmspage = Mage::getModel('ibrams_cmsextended/cmspage')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($cmspageId);
            if ($cmspage->getStatusPath()) {
                $this->setCurrentCmspage($cmspage);
                $this->setTemplate($this->_htmlTemplate);
            }
        }
        return $this;
    }
}

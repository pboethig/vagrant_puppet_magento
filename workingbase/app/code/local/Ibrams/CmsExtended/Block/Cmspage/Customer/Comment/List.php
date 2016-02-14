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
 * Cmspage customer comments list
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Cmspage_Customer_Comment_List extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * Cmspage comments collection
     *
     * @var Ibrams_CmsExtended_Model_Resource_Cmspage_Comment_Cmspage_Collection
     */
    protected $_collection;

    /**
     * Initializes collection
     *
     * @access public
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        $this->_collection = Mage::getResourceModel(
            'ibrams_cmsextended/cmspage_comment_cmspage_collection'
        );
        $this->_collection
            ->setStoreFilter(Mage::app()->getStore()->getId(), true)
            ->addFieldToFilter('main_table.status', 1) //only active

            ->addStatusFilter(Ibrams_CmsExtended_Model_Cmspage_Comment::STATUS_APPROVED) //only approved comments
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId()) //only my comments
            ->setDateOrder();
    }

    /**
     * Gets collection items count
     *
     * @access public
     * @return int
     * @author Ultimate Module Creator
     */
    public function count()
    {
        return $this->_collection->getSize();
    }

    /**
     * Get html code for toolbar
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @access protected
     * @return Mage_Core_Block_Abstract
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'customer_cmspage_comments.toolbar')
            ->setCollection($this->getCollection());

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    /**
     * Get collection
     *
     * @access protected
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Comment_Cmspage_Collection
     * @author Ultimate Module Creator
     */
    protected function _getCollection()
    {
        return $this->_collection;
    }

    /**
     * Get collection
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Comment_Cmspage_Collection
     * @author Ultimate Module Creator
     */
    public function getCollection()
    {
        return $this->_getCollection();
    }

    /**
     * Get review link
     *
     * @access public
     * @param mixed $comment
     * @return string
     * @author Ultimate Module Creator
     */
    public function getCommentLink($comment)
    {
        if ($comment instanceof Varien_Object) {
            $comment = $comment->getCtCommentId();
        }
        return Mage::getUrl(
            'ibrams_cmsextended/cmspage_customer_comment/view/',
            array('id' => $comment)
        );
    }

    /**
     * Get product link
     *
     * @access public
     * @param mixed $comment
     * @return string
     * @author Ultimate Module Creator
     */
    public function getCmspageLink($comment)
    {
        return $comment->getCmspageUrl();
    }

    /**
     * Format date in short format
     *
     * @access public
     * @param $date
     * @return string
     * @author Ultimate Module Creator
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }
}

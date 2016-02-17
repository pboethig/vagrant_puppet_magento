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
 * Cmspage comment model
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Cmspage_Comment_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * get cmspages comments
     *
     * @access public
     * @param mixed $filters
     * @return array
     * @author Ultimate Module Creator
     */
    public function items($filters = null)
    {
        $collection = Mage::getModel('ibrams_cmsextended/cmspage_comment')->getCollection();
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $result = array();
        foreach ($collection as $cmspage) {
            $result[] = $cmspage->getData();
        }
        return $result;
    }

    /**
     * update comment status
     *
     * @access public
     * @param mixed $filters
     * @return bool
     * @author Ultimate Module Creator
     */
    public function updateStatus($commentId, $status)
    {
        $comment = Mage::getModel('ibrams_cmsextended/cmspage_comment')->load($commentId);
        if (!$comment->getId()) {
            $this->_fault('not_exists');
        }
        try {
            $comment->setStatus($status)->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }
}

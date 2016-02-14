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
 * Page category model
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Page_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        $this->_init('ibrams_cmsextended/page_category');
    }

    /**
     * Save data for page-category relation
     *
     * @access public
     * @param  Ibrams_CmsExtended_Model_Page $page
     * @return Ibrams_CmsExtended_Model_Page_Category
     * @author Ultimate Module Creator
     */
    public function savePageRelation($page)
    {
        $data = $page->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->savePageRelation($page, $data);
        }
        return $this;
    }

    /**
     * get categories for page
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Page $page
     * @return Ibrams_CmsExtended_Model_Resource_Page_Category_Collection
     * @author Ultimate Module Creator
     */
    public function getCategoryCollection($page)
    {
        $collection = Mage::getResourceModel('ibrams_cmsextended/page_category_collection')
            ->addPageFilter($page);
        return $collection;
    }
}

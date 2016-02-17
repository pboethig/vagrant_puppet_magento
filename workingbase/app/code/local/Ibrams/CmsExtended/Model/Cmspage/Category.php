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
 * Cmspage category model
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Cmspage_Category extends Mage_Core_Model_Abstract
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
        $this->_init('ibrams_cmsextended/cmspage_category');
    }

    /**
     * Save data for cmspage-category relation
     *
     * @access public
     * @param  Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @return Ibrams_CmsExtended_Model_Cmspage_Category
     * @author Ultimate Module Creator
     */
    public function saveCmspageRelation($cmspage)
    {
        $data = $cmspage->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->saveCmspageRelation($cmspage, $data);
        }
        return $this;
    }

    /**
     * get categories for cmspage
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Category_Collection
     * @author Ultimate Module Creator
     */
    public function getCategoryCollection($cmspage)
    {
        $collection = Mage::getResourceModel('ibrams_cmsextended/cmspage_category_collection')
            ->addCmspageFilter($cmspage);
        return $collection;
    }
}

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
 * Cmspage list on category page block
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Catalog_Category_List_Cmspage extends Mage_Core_Block_Template
{
    /**
     * get the list of cmspages
     *
     * @access protected
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Collection
     * @author Ultimate Module Creator
     */
    public function getCmspageCollection()
    {
        if (!$this->hasData('cmspage_collection')) {
            $category = Mage::registry('current_category');
            $collection = Mage::getResourceSingleton('ibrams_cmsextended/cmspage_collection')
                ->addStoreFilter(Mage::app()->getStore())
                ->addFieldToFilter('status', 1)
                ->addCategoryFilter($category);
            $collection->getSelect()->order('related_category.position', 'ASC');
            $this->setData('cmspage_collection', $collection);
        }
        return $this->getData('cmspage_collection');
    }
}

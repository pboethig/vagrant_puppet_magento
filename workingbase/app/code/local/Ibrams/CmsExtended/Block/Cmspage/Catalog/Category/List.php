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
 * Cmspage category list
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Cmspage_Catalog_Category_List extends Mage_Core_Block_Template
{
    /**
     * get the list of products
     *
     * @access public
     * @return Mage_Catalog_Model_Resource_Category_Collection
     * @author Ultimate Module Creator
     */
    public function getCategoryCollection()
    {
        $collection = $this->getCmspage()->getSelectedCategoriesCollection();
        $collection->addAttributeToSelect('name');
        $collection->getSelect()->order('related.position');
        $collection->addAttributeToFilter('is_active', 1);
        return $collection;
    }

    /**
     * get current cmspage
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Cmspage
     * @author Ultimate Module Creator
     */
    public function getCmspage()
    {
        return Mage::registry('current_cmspage');
    }
}

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
 * Category helper
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Helper_Category extends Ibrams_CmsExtended_Helper_Data
{

    /**
     * get the selected cmspages for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return array()
     * @author Ultimate Module Creator
     */
    public function getSelectedCmspages(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasSelectedCmspages()) {
            $cmspages = array();
            foreach ($this->getSelectedCmspagesCollection($category) as $cmspage) {
                $cmspages[] = $cmspage;
            }
            $category->setSelectedCmspages($cmspages);
        }
        return $category->getData('selected_cmspages');
    }

    /**
     * get cmspage collection for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedCmspagesCollection(Mage_Catalog_Model_Category $category)
    {
        $collection = Mage::getResourceSingleton('ibrams_cmsextended/cmspage_collection')
            ->addCategoryFilter($category);
        return $collection;
    }
}

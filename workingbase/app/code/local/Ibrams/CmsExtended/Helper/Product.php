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
 * Product helper
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Helper_Product extends Ibrams_CmsExtended_Helper_Data
{

    /**
     * get the selected cmspages for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return array()
     * @author Ultimate Module Creator
     */
    public function getSelectedCmspages(Mage_Catalog_Model_Product $product)
    {
        if (!$product->hasSelectedCmspages()) {
            $cmspages = array();
            foreach ($this->getSelectedCmspagesCollection($product) as $cmspage) {
                $cmspages[] = $cmspage;
            }
            $product->setSelectedCmspages($cmspages);
        }
        return $product->getData('selected_cmspages');
    }

    /**
     * get cmspage collection for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedCmspagesCollection(Mage_Catalog_Model_Product $product)
    {
        $collection = Mage::getResourceSingleton('ibrams_cmsextended/cmspage_collection')
            ->addProductFilter($product);
        return $collection;
    }
}

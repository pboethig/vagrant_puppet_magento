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
 * Cmspage product list
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Cmspage_Catalog_Product_List extends Mage_Catalog_Block_Product_List
{
    /**
     * get the list of products
     *
     * @access public
     * @return Mage_Catalog_Model_Resource_Product_Collection
     * @author Ultimate Module Creator
     */
    public function getProductCollection()
    {
        $collection = $this->getCmspage()->getSelectedProductsCollection();
        $collection->addAttributeToSelect('name');
        $collection->addUrlRewrite();
        $collection->getSelect()->order('related.position');
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
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

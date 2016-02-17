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
 * Cmspage product model
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Cmspage_Product extends Mage_Core_Model_Abstract
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
        $this->_init('ibrams_cmsextended/cmspage_product');
    }

    /**
     * Save data for cmspage-product relation
     * @access public
     * @param  Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @return Ibrams_CmsExtended_Model_Cmspage_Product
     * @author Ultimate Module Creator
     */
    public function saveCmspageRelation($cmspage)
    {
        $data = $cmspage->getProductsData();
        if (!is_null($data)) {
            $this->_getResource()->saveCmspageRelation($cmspage, $data);
        }
        return $this;
    }

    /**
     * get products for cmspage
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Product_Collection
     * @author Ultimate Module Creator
     */
    public function getProductCollection($cmspage)
    {
        $collection = Mage::getResourceModel('ibrams_cmsextended/cmspage_product_collection')
            ->addCmspageFilter($cmspage);
        return $collection;
    }
}

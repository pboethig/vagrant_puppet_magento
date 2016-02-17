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
 * Cmspage - product relation model
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Resource_Cmspage_Product extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     * @author Ultimate Module Creator
     */
    protected function  _construct()
    {
        $this->_init('ibrams_cmsextended/cmspage_product', 'rel_id');
    }

    /**
     * Save cmspage - product relations
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @param array $data
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Product
     * @author Ultimate Module Creator
     */
    public function saveCmspageRelation($cmspage, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('cmspage_id=?', $cmspage->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $productId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'cmspage_id' => $cmspage->getId(),
                    'product_id'    => $productId,
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }

    /**
     * Save  product - cmspage relations
     *
     * @access public
     * @param Mage_Catalog_Model_Product $prooduct
     * @param array $data
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Product
     * @author Ultimate Module Creator
     */
    public function saveProductRelation($product, $cmspageIds)
    {
        $oldCmspages = Mage::helper('ibrams_cmsextended/product')->getSelectedCmspages($product);
        $oldCmspageIds = array();
        foreach ($oldCmspages as $cmspage) {
            $oldCmspageIds[] = $cmspage->getId();
        }
        $insert = array_diff($cmspageIds, $oldCmspageIds);
        $delete = array_diff($oldCmspageIds, $cmspageIds);
        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $cmspageId) {
                if (empty($cmspageId)) {
                    continue;
                }
                $data[] = array(
                    'cmspage_id' => (int)$cmspageId,
                    'product_id'  => (int)$product->getId(),
                    'position'=> 1
                );
            }
            if ($data) {
                $write->insertMultiple($this->getMainTable(), $data);
            }
        }
        if (!empty($delete)) {
            foreach ($delete as $cmspageId) {
                $where = array(
                    'product_id = ?'  => (int)$product->getId(),
                    'cmspage_id = ?' => (int)$cmspageId,
                );
                $write->delete($this->getMainTable(), $where);
            }
        }
        return $this;
    }
}

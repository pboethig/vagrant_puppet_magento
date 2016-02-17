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
 * Cmspage - Categories relation model
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Resource_Cmspage_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @return void
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     * @author Ultimate Module Creator
     */
    protected function  _construct()
    {
        $this->_init('ibrams_cmsextended/cmspage_category', 'rel_id');
    }

    /**
     * Save cmspage - category relations
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @param array $data
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Category
     * @author Ultimate Module Creator
     */
    public function saveCmspageRelation($cmspage, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('cmspage_id=?', $cmspage->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $categoryId) {
            if (!empty($categoryId)) {
                $insert = array(
                    'cmspage_id' => $cmspage->getId(),
                    'category_id'   => $categoryId,
                    'position'      => 1
                );
                $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), $insert, array_keys($insert));
            }
        }
        return $this;
    }

    /**
     * Save  category - cmspage relations
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @param array $data
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Category
     * @author Ultimate Module Creator
     */
    public function saveCategoryRelation($category, $cmspageIds)
    {

        $oldCmspages = Mage::helper('ibrams_cmsextended/category')->getSelectedCmspages($category);
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
                    'category_id'  => (int)$category->getId(),
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
                    'category_id = ?'   => (int)$category->getId(),
                    'cmspage_id = ?' => (int)$cmspageId,
                );
                $write->delete($this->getMainTable(), $where);
            }
        }
        return $this;
    }
}

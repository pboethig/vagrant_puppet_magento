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
 * Page - Categories relation model
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Resource_Page_Category extends Mage_Core_Model_Resource_Db_Abstract
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
        $this->_init('ibrams_cmsextended/page_category', 'rel_id');
    }

    /**
     * Save page - category relations
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Page $page
     * @param array $data
     * @return Ibrams_CmsExtended_Model_Resource_Page_Category
     * @author Ultimate Module Creator
     */
    public function savePageRelation($page, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('page_id=?', $page->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $categoryId) {
            if (!empty($categoryId)) {
                $insert = array(
                    'page_id' => $page->getId(),
                    'category_id'   => $categoryId,
                    'position'      => 1
                );
                $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), $insert, array_keys($insert));
            }
        }
        return $this;
    }

    /**
     * Save  category - page relations
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @param array $data
     * @return Ibrams_CmsExtended_Model_Resource_Page_Category
     * @author Ultimate Module Creator
     */
    public function saveCategoryRelation($category, $pageIds)
    {

        $oldPages = Mage::helper('ibrams_cmsextended/category')->getSelectedPages($category);
        $oldPageIds = array();
        foreach ($oldPages as $page) {
            $oldPageIds[] = $page->getId();
        }
        $insert = array_diff($pageIds, $oldPageIds);
        $delete = array_diff($oldPageIds, $pageIds);
        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $pageId) {
                if (empty($pageId)) {
                    continue;
                }
                $data[] = array(
                    'page_id' => (int)$pageId,
                    'category_id'  => (int)$category->getId(),
                    'position'=> 1
                );
            }
            if ($data) {
                $write->insertMultiple($this->getMainTable(), $data);
            }
        }
        if (!empty($delete)) {
            foreach ($delete as $pageId) {
                $where = array(
                    'category_id = ?'   => (int)$category->getId(),
                    'page_id = ?' => (int)$pageId,
                );
                $write->delete($this->getMainTable(), $where);
            }
        }
        return $this;
    }
}

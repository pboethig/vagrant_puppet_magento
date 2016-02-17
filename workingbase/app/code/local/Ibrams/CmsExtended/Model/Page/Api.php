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
class Ibrams_CmsExtended_Model_Page_Api extends Mage_Api_Model_Resource_Abstract
{


    /**
     * init page
     *
     * @access protected
     * @param $pageId
     * @return Ibrams_CmsExtended_Model_Page
     * @author      Ultimate Module Creator
     */
    protected function _initPage($pageId)
    {
        $page = Mage::getModel('ibrams_cmsextended/page')->load($pageId);
        if (!$page->getId()) {
            $this->_fault('page_not_exists');
        }
        return $page;
    }

    /**
     * get pages
     *
     * @access public
     * @param mixed $filters
     * @return array
     * @author Ultimate Module Creator
     */
    public function items($filters = null)
    {
        $collection = Mage::getModel('ibrams_cmsextended/page')->getCollection()
            ->addFieldToFilter(
                'entity_id',
                array(
                    'neq'=>Mage::helper('ibrams_cmsextended/page')->getRootPageId()
                )
            );
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $result = array();
        foreach ($collection as $page) {
            $result[] = $this->_getApiData($page);
        }
        return $result;
    }

    /**
     * Add page
     *
     * @access public
     * @param array $data
     * @return array
     * @author Ultimate Module Creator
     */
    public function add($data)
    {
        try {
            if (is_null($data)) {
                throw new Exception(Mage::helper('ibrams_cmsextended')->__("Data cannot be null"));
            }
            $data = (array)$data;
            $page = Mage::getModel('ibrams_cmsextended/page')
                ->setData((array)$data)
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $page->getId();
    }

    /**
     * Change existing page information
     *
     * @access public
     * @param int $pageId
     * @param array $data
     * @return bool
     * @author Ultimate Module Creator
     */
    public function update($pageId, $data)
    {
        $page = $this->_initPage($pageId);
        try {
            $data = (array)$data;
            $page->addData($data);
            $page->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * remove page
     *
     * @access public
     * @param int $pageId
     * @return bool
     * @author Ultimate Module Creator
     */
    public function remove($pageId)
    {
        $page = $this->_initPage($pageId);
        try {
            $page->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('remove_error', $e->getMessage());
        }
        return true;
    }

    /**
     * get info
     *
     * @access public
     * @param int $pageId
     * @return array
     * @author Ultimate Module Creator
     */
    public function info($pageId)
    {
        $result = array();
        $page = $this->_initPage($pageId);
        $result = $this->_getApiData($page);
        //related products
        $result['products'] = array();
        $relatedProductsCollection = $page->getSelectedProductsCollection();
        foreach ($relatedProductsCollection as $product) {
            $result['products'][$product->getId()] = $product->getPosition();
        }
        //related categories
        $result['categories'] = array();
        $relatedCategoriesCollection = $page->getSelectedCategoriesCollection();
        foreach ($relatedCategoriesCollection as $category) {
            $result['categories'][$category->getId()] = $category->getPosition();
        }
        return $result;
    }

    /**
     * Move page in tree
     *
     * @param int $pageId
     * @param int $parentId
     * @param int $afterId
     * @return boolean
     */
    public function move($pageId, $parentId, $afterId = null)
    {
        $page = $this->_initPage($pageId);
        $parentPage = $this->_initPage($parentId);
        if ($afterId === null && $parentPage->hasChildren()) {
            $parentChildren = $parentPage->getChildPages();
            $afterId = array_pop(explode(',', $parentChildren));
        }
        if ( strpos($parentPage->getPath(), $page->getPath()) === 0) {
            $this->_fault(
                'not_moved',
                Mage::helper('ibrams_cmsextended')->__("Cannot move parent inside page")
            );
        }
        try {
            $page->move($parentId, $afterId);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_moved', $e->getMessage());
        }
        return true;
    }
    /**
     * Assign product to page
     *
     * @access public
     * @param int $pageId
     * @param int $productId
     * @param int $position
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function assignProduct($pageId, $productId, $position = null)
    {
        $page = $this->_initPage($pageId);
        $positions    = array();
        $products     = $page->getSelectedProducts();
        foreach ($products as $product) {
            $positions[$product->getId()] = array('position'=>$product->getPosition());
        }
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }
        $positions[$productId]['position'] = $position;
        $page->setProductsData($positions);
        try {
            $page->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove product from page
     *
     * @access public
     * @param int $pageId
     * @param int $productId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function unassignProduct($pageId, $productId)
    {
        $page = $this->_initPage($pageId);
        $positions    = array();
        $products     = $page->getSelectedProducts();
        foreach ($products as $product) {
            $positions[$product->getId()] = array('position'=>$product->getPosition());
        }
        unset($positions[$productId]);
        $page->setProductsData($positions);
        try {
            $page->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * Assign category to page
     *
     * @access public
     * @param int $pageId
     * @param int $categoryId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function assignCategory($pageId, $categoryId)
    {
        $page = $this->_initPage($pageId);
        $category   = Mage::getModel('catalog/category')->load($categoryId);
        if (!$category->getId()) {
            $this->_fault('category_not_exists');
        }
        $categories = $page->getSelectedCategories();
        $categoryIds = array();
        foreach ($categories as $category) {
            $categoryIds[] = $category->getId();
        }
        $categoryIds[] = $categoryId;
        $page->setCategoriesData($categoryIds);
        try {
            $page->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove category from page
     *
     * @access public
     * @param int $pageId
     * @param int $categoryId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function unassignCategory($pageId, $categoryId)
    {
        $page    = $this->_initPage($pageId);
        $categories    = $page->getSelectedCategories();
        $categoryIds   = array();
        foreach ($categories as $key=>$category) {
            if ($category->getId() != $categoryId) {
                $categoryIds[] = $category->getId();
            }
        }
        $page->setCategoriesData($categoryIds);
        try {
            $page->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * get data for api
     *
     * @access protected
     * @param Ibrams_CmsExtended_Model_Page $page
     * @return array()
     * @author Ultimate Module Creator
     */
    protected function _getApiData(Ibrams_CmsExtended_Model_Page $page)
    {
        return $page->getData();
    }
}

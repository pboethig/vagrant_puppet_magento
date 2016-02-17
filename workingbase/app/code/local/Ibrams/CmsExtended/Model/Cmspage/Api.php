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
class Ibrams_CmsExtended_Model_Cmspage_Api extends Mage_Api_Model_Resource_Abstract
{


    /**
     * init cmspage
     *
     * @access protected
     * @param $cmspageId
     * @return Ibrams_CmsExtended_Model_Cmspage
     * @author      Ultimate Module Creator
     */
    protected function _initCmspage($cmspageId)
    {
        $cmspage = Mage::getModel('ibrams_cmsextended/cmspage')->load($cmspageId);
        if (!$cmspage->getId()) {
            $this->_fault('cmspage_not_exists');
        }
        return $cmspage;
    }

    /**
     * get cmspages
     *
     * @access public
     * @param mixed $filters
     * @return array
     * @author Ultimate Module Creator
     */
    public function items($filters = null)
    {
        $collection = Mage::getModel('ibrams_cmsextended/cmspage')->getCollection()
            ->addFieldToFilter(
                'entity_id',
                array(
                    'neq'=>Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId()
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
        foreach ($collection as $cmspage) {
            $result[] = $this->_getApiData($cmspage);
        }
        return $result;
    }

    /**
     * Add cmspage
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
            $cmspage = Mage::getModel('ibrams_cmsextended/cmspage')
                ->setData((array)$data)
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $cmspage->getId();
    }

    /**
     * Change existing cmspage information
     *
     * @access public
     * @param int $cmspageId
     * @param array $data
     * @return bool
     * @author Ultimate Module Creator
     */
    public function update($cmspageId, $data)
    {
        $cmspage = $this->_initCmspage($cmspageId);
        try {
            $data = (array)$data;
            $cmspage->addData($data);
            $cmspage->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * remove cmspage
     *
     * @access public
     * @param int $cmspageId
     * @return bool
     * @author Ultimate Module Creator
     */
    public function remove($cmspageId)
    {
        $cmspage = $this->_initCmspage($cmspageId);
        try {
            $cmspage->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('remove_error', $e->getMessage());
        }
        return true;
    }

    /**
     * get info
     *
     * @access public
     * @param int $cmspageId
     * @return array
     * @author Ultimate Module Creator
     */
    public function info($cmspageId)
    {
        $result = array();
        $cmspage = $this->_initCmspage($cmspageId);
        $result = $this->_getApiData($cmspage);
        //related products
        $result['products'] = array();
        $relatedProductsCollection = $cmspage->getSelectedProductsCollection();
        foreach ($relatedProductsCollection as $product) {
            $result['products'][$product->getId()] = $product->getPosition();
        }
        //related categories
        $result['categories'] = array();
        $relatedCategoriesCollection = $cmspage->getSelectedCategoriesCollection();
        foreach ($relatedCategoriesCollection as $category) {
            $result['categories'][$category->getId()] = $category->getPosition();
        }
        return $result;
    }

    /**
     * Move cmspage in tree
     *
     * @param int $cmspageId
     * @param int $parentId
     * @param int $afterId
     * @return boolean
     */
    public function move($cmspageId, $parentId, $afterId = null)
    {
        $cmspage = $this->_initCmspage($cmspageId);
        $parentCmspage = $this->_initCmspage($parentId);
        if ($afterId === null && $parentCmspage->hasChildren()) {
            $parentChildren = $parentCmspage->getChildCmspages();
            $afterId = array_pop(explode(',', $parentChildren));
        }
        if ( strpos($parentCmspage->getPath(), $cmspage->getPath()) === 0) {
            $this->_fault(
                'not_moved',
                Mage::helper('ibrams_cmsextended')->__("Cannot move parent inside cmspage")
            );
        }
        try {
            $cmspage->move($parentId, $afterId);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_moved', $e->getMessage());
        }
        return true;
    }
    /**
     * Assign product to cmspage
     *
     * @access public
     * @param int $cmspageId
     * @param int $productId
     * @param int $position
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function assignProduct($cmspageId, $productId, $position = null)
    {
        $cmspage = $this->_initCmspage($cmspageId);
        $positions    = array();
        $products     = $cmspage->getSelectedProducts();
        foreach ($products as $product) {
            $positions[$product->getId()] = array('position'=>$product->getPosition());
        }
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }
        $positions[$productId]['position'] = $position;
        $cmspage->setProductsData($positions);
        try {
            $cmspage->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove product from cmspage
     *
     * @access public
     * @param int $cmspageId
     * @param int $productId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function unassignProduct($cmspageId, $productId)
    {
        $cmspage = $this->_initCmspage($cmspageId);
        $positions    = array();
        $products     = $cmspage->getSelectedProducts();
        foreach ($products as $product) {
            $positions[$product->getId()] = array('position'=>$product->getPosition());
        }
        unset($positions[$productId]);
        $cmspage->setProductsData($positions);
        try {
            $cmspage->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * Assign category to cmspage
     *
     * @access public
     * @param int $cmspageId
     * @param int $categoryId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function assignCategory($cmspageId, $categoryId)
    {
        $cmspage = $this->_initCmspage($cmspageId);
        $category   = Mage::getModel('catalog/category')->load($categoryId);
        if (!$category->getId()) {
            $this->_fault('category_not_exists');
        }
        $categories = $cmspage->getSelectedCategories();
        $categoryIds = array();
        foreach ($categories as $category) {
            $categoryIds[] = $category->getId();
        }
        $categoryIds[] = $categoryId;
        $cmspage->setCategoriesData($categoryIds);
        try {
            $cmspage->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove category from cmspage
     *
     * @access public
     * @param int $cmspageId
     * @param int $categoryId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function unassignCategory($cmspageId, $categoryId)
    {
        $cmspage    = $this->_initCmspage($cmspageId);
        $categories    = $cmspage->getSelectedCategories();
        $categoryIds   = array();
        foreach ($categories as $key=>$category) {
            if ($category->getId() != $categoryId) {
                $categoryIds[] = $category->getId();
            }
        }
        $cmspage->setCategoriesData($categoryIds);
        try {
            $cmspage->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * get data for api
     *
     * @access protected
     * @param Ibrams_CmsExtended_Model_Cmspage $cmspage
     * @return array()
     * @author Ultimate Module Creator
     */
    protected function _getApiData(Ibrams_CmsExtended_Model_Cmspage $cmspage)
    {
        return $cmspage->getData();
    }
}

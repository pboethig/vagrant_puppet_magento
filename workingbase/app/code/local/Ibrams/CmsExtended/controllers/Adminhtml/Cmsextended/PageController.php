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
 * Page admin controller
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Adminhtml_Cmsextended_PageController extends Ibrams_CmsExtended_Controller_Adminhtml_CmsExtended
{
    /**
     * init page
     *
     * @access protected
     * @return Ibrams_CmsExtended_Model_Page
     * @author Ultimate Module Creator
     */
    protected function _initPage()
    {
        $pageId = (int) $this->getRequest()->getParam('id', false);
        $page = Mage::getModel('ibrams_cmsextended/page');
        if ($pageId) {
            $page->load($pageId);
        } else {
            $page->setData($page->getDefaultValues());
        }
        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setPageActiveTabId($activeTabId);
        }
        Mage::register('page', $page);
        Mage::register('current_page', $page);
        return $page;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function indexAction()
    {
        $this->_forward('edit');
    }

    /**
     * Add new page form
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function addAction()
    {
        Mage::getSingleton('admin/session')->unsPageActiveTabId();
        $this->_forward('edit');
    }

    /**
     * Edit page page
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $params['_current'] = true;
        $redirect = false;
        $parentId = (int) $this->getRequest()->getParam('parent');
        $pageId = (int) $this->getRequest()->getParam('id');
        $_prevPageId = Mage::getSingleton('admin/session')->getLastEditedPage(true);
        if ($_prevPageId &&
            !$this->getRequest()->getQuery('isAjax') &&
            !$this->getRequest()->getParam('clear')) {
            $this->getRequest()->setParam('id', $_prevPageId);
        }
        if ($redirect) {
            $this->_redirect('*/*/edit', $params);
            return;
        }
        if (!($page = $this->_initPage())) {
            return;
        }
        $this->_title($pageId ? $page->getName() : $this->__('New Page'));
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (isset($data['page'])) {
            $page->addData($data['page']);
        }
        if ($this->getRequest()->getQuery('isAjax')) {
            $breadcrumbsPath = $page->getPath();
            if (empty($breadcrumbsPath)) {
                $breadcrumbsPath = Mage::getSingleton('admin/session')->getPageDeletedPath(true);
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    } else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }
            Mage::getSingleton('admin/session')->setLastEditedPage($page->getId());
            $this->loadLayout();
            $eventResponse = new Varien_Object(
                array(
                    'content' => $this->getLayout()->getBlock('page.edit')->getFormHtml().
                        $this->getLayout()->getBlock('page.tree')->getBreadcrumbsJavascript(
                            $breadcrumbsPath,
                            'editingPageBreadcrumbs'
                        ),
                    'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
                )
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($eventResponse->getData()));
            return;
        }
        $this->loadLayout();
        $this->_title(Mage::helper('ibrams_cmsextended')->__('ExtendedPages'))
             ->_title(Mage::helper('ibrams_cmsextended')->__('Pages'));
        $this->_setActiveMenu('ibrams_cmsextended/page');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
            ->setContainerCssClass('page');

        $this->_addBreadcrumb(
            Mage::helper('ibrams_cmsextended')->__('Manage Pages'),
            Mage::helper('ibrams_cmsextended')->__('Manage Pages')
        );
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * Get tree node (Ajax version)
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function pagesJsonAction()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            Mage::getSingleton('admin/session')->setPageIsTreeWasExpanded(true);
        } else {
            Mage::getSingleton('admin/session')->setPageIsTreeWasExpanded(false);
        }
        if ($pageId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $pageId);
            if (!$page = $this->_initPage()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('ibrams_cmsextended/adminhtml_page_tree')
                    ->getTreeJson($page)
            );
        }
    }

    /**
     * Move page action
     * @access public
     * @author Ultimate Module Creator
     */
    public function moveAction()
    {
        $page = $this->_initPage();
        if (!$page) {
            $this->getResponse()->setBody(
                Mage::helper('ibrams_cmsextended')->__('Page move error')
            );
            return;
        }
        $parentNodeId   = $this->getRequest()->getPost('pid', false);
        $prevNodeId = $this->getRequest()->getPost('aid', false);
        try {
            $page->move($parentNodeId, $prevNodeId);
            $this->getResponse()->setBody("SUCCESS");
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        } catch (Exception $e) {
            $this->getResponse()->setBody(
                Mage::helper('ibrams_cmsextended')->__('Page move error')
            );
            Mage::logException($e);
        }
    }

    /**
     * Tree Action
     * Retrieve page tree
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function treeAction()
    {
        $pageId = (int) $this->getRequest()->getParam('id');
        $page = $this->_initPage();
        $block = $this->getLayout()->createBlock('ibrams_cmsextended/adminhtml_page_tree');
        $root  = $block->getRoot();
        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode(
                array(
                    'data' => $block->getTree(),
                    'parameters' => array(
                        'text'          => $block->buildNodeName($root),
                        'draggable'     => false,
                        'allowDrop'     => ($root->getIsVisible()) ? true : false,
                        'id'            => (int) $root->getId(),
                        'expanded'      => (int) $block->getIsWasExpanded(),
                        'page_id' => (int) $page->getId(),
                        'root_visible'  => (int) $root->getIsVisible()
                    )
                )
            )
        );
    }

    /**
     * Build response for refresh input element 'path' in form
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function refreshPathAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            $page = Mage::getModel('ibrams_cmsextended/page')->load($id);
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(
                    array(
                       'id' => $id,
                       'path' => $page->getPath(),
                    )
                )
            );
        }
    }

    /**
     * Delete page action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            try {
                $page = Mage::getModel('ibrams_cmsextended/page')->load($id);
                Mage::getSingleton('admin/session')->setPageDeletedPath($page->getPath());

                $page->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('ibrams_cmsextended')->__('The page has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('ibrams_cmsextended')->__('An error occurred while trying to delete the page.')
                );
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                Mage::logException($e);
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true, 'id'=>null)));
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('ibrams_cmsextended/page');
    }

    /**
     * Page save action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if (!$page = $this->_initPage()) {
            return;
        }
        $refreshTree = 'false';
        if ($data = $this->getRequest()->getPost('page')) {

            Zend_Debug::dump($data);

            die();

            $page->addData($data);
            if (!$page->getId()) {
                $parentId = $this->getRequest()->getParam('parent');
                if (!$parentId) {
                    $parentId = Mage::helper('ibrams_cmsextended/page')->getRootPageId();
                }
                $parentPage = Mage::getModel('ibrams_cmsextended/page')->load($parentId);
                $page->setPath($parentPage->getPath());
            }
            try {
                $products = $this->getRequest()->getPost('page_products', -1);
                if ($products != -1) {
                    $productData = array();
                    parse_str($products, $productData);
                    $products = array();
                    foreach ($productData as $id => $position) {
                        $products[$id]['position'] = $position;
                    }
                    $page->setProductsData($productData);
                }

                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $page->setCategoriesData($categories);
                }
                $page->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('ibrams_cmsextended')->__('The page has been saved.')
                );
                $refreshTree = 'true';
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage())->setPageData($data);
                Mage::logException($e);
                $refreshTree = 'false';
            }
        }
        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $page->getId()));
        $this->getResponse()->setBody(
            '<script type="text/javascript">parent.updateContent("' . $url . '", {}, '.$refreshTree.');</script>'
        );
    }

    /**
     * get the products grid
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function productsgridAction()
    {
        if (!$page = $this->_initPage()) {
            return;
        }
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'ibrams_cmsextended/adminhtml_page_edit_tab_product',
                'page.product.grid'
            )
            ->toHtml()
        );
    }

    /**
     * get child categories action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function categoriesJsonAction()
    {
        $this->_initPage();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ibrams_cmsextended/adminhtml_page_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}

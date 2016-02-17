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
 * Cmspage admin controller
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Adminhtml_Cmsextended_CmspageController extends Ibrams_CmsExtended_Controller_Adminhtml_CmsExtended
{
    /**
     * init cmspage
     *
     * @access protected
     * @return Ibrams_CmsExtended_Model_Cmspage
     * @author Ultimate Module Creator
     */
    protected function _initCmspage()
    {
        $cmspageId = (int) $this->getRequest()->getParam('id', false);
        $cmspage = Mage::getModel('ibrams_cmsextended/cmspage');
        if ($cmspageId) {
            $cmspage->load($cmspageId);
        } else {
            $cmspage->setData($cmspage->getDefaultValues());
        }
        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setCmspageActiveTabId($activeTabId);
        }
        Mage::register('cmspage', $cmspage);
        Mage::register('current_cmspage', $cmspage);
        return $cmspage;
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
     * Add new cmspage form
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function addAction()
    {
        Mage::getSingleton('admin/session')->unsCmspageActiveTabId();
        $this->_forward('edit');
    }

    /**
     * Edit cmspage page
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
        $cmspageId = (int) $this->getRequest()->getParam('id');
        $_prevCmspageId = Mage::getSingleton('admin/session')->getLastEditedCmspage(true);
        if ($_prevCmspageId &&
            !$this->getRequest()->getQuery('isAjax') &&
            !$this->getRequest()->getParam('clear')) {
            $this->getRequest()->setParam('id', $_prevCmspageId);
        }
        if ($redirect) {
            $this->_redirect('*/*/edit', $params);
            return;
        }
        if (!($cmspage = $this->_initCmspage())) {
            return;
        }
        $this->_title($cmspageId ? $cmspage->getName() : $this->__('New Cmspage'));
        $data = Mage::getSingleton('adminhtml/session')->getCmspageData(true);
        if (isset($data['cmspage'])) {
            $cmspage->addData($data['cmspage']);
        }
        if ($this->getRequest()->getQuery('isAjax')) {
            $breadcrumbsPath = $cmspage->getPath();
            if (empty($breadcrumbsPath)) {
                $breadcrumbsPath = Mage::getSingleton('admin/session')->getCmspageDeletedPath(true);
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
            Mage::getSingleton('admin/session')->setLastEditedCmspage($cmspage->getId());
            $this->loadLayout();
            $eventResponse = new Varien_Object(
                array(
                    'content' => $this->getLayout()->getBlock('cmspage.edit')->getFormHtml().
                        $this->getLayout()->getBlock('cmspage.tree')->getBreadcrumbsJavascript(
                            $breadcrumbsPath,
                            'editingCmspageBreadcrumbs'
                        ),
                    'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
                )
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($eventResponse->getData()));
            return;
        }
        $this->loadLayout();
        $this->_title(Mage::helper('ibrams_cmsextended')->__('ExtendedPages'))
             ->_title(Mage::helper('ibrams_cmsextended')->__('Cmspages'));
        $this->_setActiveMenu('ibrams_cmsextended/cmspage');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
            ->setContainerCssClass('cmspage');

        $this->_addBreadcrumb(
            Mage::helper('ibrams_cmsextended')->__('Manage Cmspages'),
            Mage::helper('ibrams_cmsextended')->__('Manage Cmspages')
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
    public function cmspagesJsonAction()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            Mage::getSingleton('admin/session')->setCmspageIsTreeWasExpanded(true);
        } else {
            Mage::getSingleton('admin/session')->setCmspageIsTreeWasExpanded(false);
        }
        if ($cmspageId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $cmspageId);
            if (!$cmspage = $this->_initCmspage()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('ibrams_cmsextended/adminhtml_cmspage_tree')
                    ->getTreeJson($cmspage)
            );
        }
    }

    /**
     * Move cmspage action
     * @access public
     * @author Ultimate Module Creator
     */
    public function moveAction()
    {
        $cmspage = $this->_initCmspage();
        if (!$cmspage) {
            $this->getResponse()->setBody(
                Mage::helper('ibrams_cmsextended')->__('Cmspage move error')
            );
            return;
        }
        $parentNodeId   = $this->getRequest()->getPost('pid', false);
        $prevNodeId = $this->getRequest()->getPost('aid', false);
        try {
            $cmspage->move($parentNodeId, $prevNodeId);
            $this->getResponse()->setBody("SUCCESS");
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        } catch (Exception $e) {
            $this->getResponse()->setBody(
                Mage::helper('ibrams_cmsextended')->__('Cmspage move error')
            );
            Mage::logException($e);
        }
    }

    /**
     * Tree Action
     * Retrieve cmspage tree
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function treeAction()
    {
        $cmspageId = (int) $this->getRequest()->getParam('id');
        $cmspage = $this->_initCmspage();
        $block = $this->getLayout()->createBlock('ibrams_cmsextended/adminhtml_cmspage_tree');
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
                        'cmspage_id' => (int) $cmspage->getId(),
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
            $cmspage = Mage::getModel('ibrams_cmsextended/cmspage')->load($id);
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(
                    array(
                       'id' => $id,
                       'path' => $cmspage->getPath(),
                    )
                )
            );
        }
    }

    /**
     * Delete cmspage action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            try {
                $cmspage = Mage::getModel('ibrams_cmsextended/cmspage')->load($id);
                Mage::getSingleton('admin/session')->setCmspageDeletedPath($cmspage->getPath());

                $cmspage->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('ibrams_cmsextended')->__('The cmspage has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('ibrams_cmsextended')->__('An error occurred while trying to delete the cmspage.')
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
        return Mage::getSingleton('admin/session')->isAllowed('ibrams_cmsextended/cmspage');
    }

    /**
     * wyisiwyg action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeMediaUrl = Mage::app()->getStore(0)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock(
            'adminhtml/catalog_helper_form_wysiwyg_content',
            '',
            array(
                'editor_element_id' => $elementId,
                'store_id'          => 0,
                'store_media_url'   => $storeMediaUrl,
            )
        );
        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * Cmspage save action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if (!$cmspage = $this->_initCmspage()) {
            return;
        }
        $refreshTree = 'false';
        if ($data = $this->getRequest()->getPost('cmspage')) {
            $data = $this->_filterDates($data, array('startdate' ,'enddate'));
            $cmspage->addData($data);
            if (!$cmspage->getId()) {
                $parentId = $this->getRequest()->getParam('parent');
                if (!$parentId) {
                    $parentId = Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId();
                }
                $parentCmspage = Mage::getModel('ibrams_cmsextended/cmspage')->load($parentId);
                $cmspage->setPath($parentCmspage->getPath());
            }
            try {
                $products = $this->getRequest()->getPost('cmspage_products', -1);
                if ($products != -1) {
                    $productData = array();
                    parse_str($products, $productData);
                    $products = array();
                    foreach ($productData as $id => $position) {
                        $products[$id]['position'] = $position;
                    }
                    $cmspage->setProductsData($productData);
                }

                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $cmspage->setCategoriesData($categories);
                }
                $cmspage->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('ibrams_cmsextended')->__('The cmspage has been saved.')
                );
                $refreshTree = 'true';
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage())->setCmspageData($data);
                Mage::logException($e);
                $refreshTree = 'false';
            }
        }
        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $cmspage->getId()));
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
        if (!$cmspage = $this->_initCmspage()) {
            return;
        }
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'ibrams_cmsextended/adminhtml_cmspage_edit_tab_product',
                'cmspage.product.grid'
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
        $this->_initCmspage();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ibrams_cmsextended/adminhtml_cmspage_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}

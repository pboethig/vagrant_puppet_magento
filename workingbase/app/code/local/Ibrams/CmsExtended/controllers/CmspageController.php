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
 * Cmspage front contrller
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_CmspageController extends Mage_Core_Controller_Front_Action
{

    /**
      * default action
      *
      * @access public
      * @return void
      * @author Ultimate Module Creator
      */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('ibrams_cmsextended/cmspage')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('ibrams_cmsextended')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'cmspages',
                    array(
                        'label' => Mage::helper('ibrams_cmsextended')->__('Cmspages'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('ibrams_cmsextended/cmspage')->getCmspagesUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('ibrams_cmsextended/cmspage/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('ibrams_cmsextended/cmspage/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('ibrams_cmsextended/cmspage/meta_description'));
        }


        $this->renderLayout();
    }

    /**
     * init Cmspage
     *
     * @access protected
     * @return Ibrams_CmsExtended_Model_Cmspage
     * @author Ultimate Module Creator
     */
    protected function _initCmspage()
    {
        $cmspageId   = $this->getRequest()->getParam('id', 0);
        $cmspage     = Mage::getModel('ibrams_cmsextended/cmspage')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($cmspageId);
        if (!$cmspage->getId()) {
            return false;
        } elseif (!$cmspage->getStatus()) {
            return false;
        }
        return $cmspage;
    }

    /**
     * view cmspage action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function viewAction()
    {
        $cmspage = $this->_initCmspage();
        if (!$cmspage) {
            $this->_forward('no-route');
            return;
        }
        if (!$cmspage->getStatusPath()) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_cmspage', $cmspage);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('cmsextended-cmspage cmsextended-cmspage' . $cmspage->getId());

            $template = Mage::getModel('ibrams_cmsextended/cmspage_attribute_source_layout')->getByOptionId($cmspage->layout);

            $root->setTemplate($template);

        }




        if (Mage::helper('ibrams_cmsextended/cmspage')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('ibrams_cmsextended')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'cmspages',
                    array(
                        'label' => Mage::helper('ibrams_cmsextended')->__('Cmspages'),
                        'link'  => Mage::helper('ibrams_cmsextended/cmspage')->getCmspagesUrl(),
                    )
                );
                $parents = $cmspage->getParentCmspages();
                foreach ($parents as $parent) {
                    if ($parent->getId() != Mage::helper('ibrams_cmsextended/cmspage')->getRootCmspageId() &&
                        $parent->getId() != $cmspage->getId()) {
                        $breadcrumbBlock->addCrumb(
                            'cmspage-'.$parent->getId(),
                            array(
                                'label'    => $parent->getName(),
                                'link'    => $link = $parent->getCmspageUrl(),
                            )
                        );
                    }
                }
                $breadcrumbBlock->addCrumb(
                    'cmspage',
                    array(
                        'label' => $cmspage->getName(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $cmspage->getCmspageUrl());
        }
        if ($headBlock) {
            if ($cmspage->getMetaTitle()) {
                $headBlock->setTitle($cmspage->getMetaTitle());
            } else {
                $headBlock->setTitle($cmspage->getName());
            }
            $headBlock->setKeywords($cmspage->getMetaKeywords());
            $headBlock->setDescription($cmspage->getMetaDescription());
        }
        $this->renderLayout();
    }

    /**
     * cmspages rss list action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function rssAction()
    {
        if (Mage::helper('ibrams_cmsextended/cmspage')->isRssEnabled()) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
        } else {
            $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            $this->getResponse()->setHeader('Status', '404 File not found');
            $this->_forward('nofeed', 'index', 'rss');
        }
    }

    /**
     * Submit new comment action
     * @access public
     * @author Ultimate Module Creator
     */
    public function commentpostAction()
    {
        $data   = $this->getRequest()->getPost();
        $cmspage = $this->_initCmspage();
        $session    = Mage::getSingleton('core/session');
        if ($cmspage) {
            if ($cmspage->getAllowComments()) {
                if ((Mage::getSingleton('customer/session')->isLoggedIn() ||
                    Mage::getStoreConfigFlag('ibrams_cmsextended/cmspage/allow_guest_comment'))) {
                    $comment  = Mage::getModel('ibrams_cmsextended/cmspage_comment')->setData($data);
                    $validate = $comment->validate();
                    if ($validate === true) {
                        try {
                            $comment->setCmspageId($cmspage->getId())
                                ->setStatus(Ibrams_CmsExtended_Model_Cmspage_Comment::STATUS_PENDING)
                                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                                ->setStores(array(Mage::app()->getStore()->getId()))
                                ->save();
                            $session->addSuccess($this->__('Your comment has been accepted for moderation.'));
                        } catch (Exception $e) {
                            $session->setCmspageCommentData($data);
                            $session->addError($this->__('Unable to post the comment.'));
                        }
                    } else {
                        $session->setCmspageCommentData($data);
                        if (is_array($validate)) {
                            foreach ($validate as $errorMessage) {
                                $session->addError($errorMessage);
                            }
                        } else {
                            $session->addError($this->__('Unable to post the comment.'));
                        }
                    }
                } else {
                    $session->addError($this->__('Guest comments are not allowed'));
                }
            } else {
                $session->addError($this->__('This cmspage does not allow comments'));
            }
        }
        $this->_redirectReferer();
    }
}

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
 * Cmspage admin widget controller
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Adminhtml_Cmsextended_Cmspage_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $grid = $this->getLayout()->createBlock(
            'ibrams_cmsextended/adminhtml_cmspage_widget_chooser',
            '',
            array(
                'id' => $uniqId,
            )
        );
        $this->getResponse()->setBody($grid->toHtml());
    }

    /**
     * cmspages json action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function cmspagesJsonAction()
    {
        if ($cmspageId = (int) $this->getRequest()->getPost('id')) {
            $cmspage = Mage::getModel('ibrams_cmsextended/cmspage')->load($cmspageId);
            if ($cmspage->getId()) {
                Mage::register('cmspage', $cmspage);
                Mage::register('current_cmspage', $cmspage);
            }
            $this->getResponse()->setBody(
                $this->_getCmspageTreeBlock()->getTreeJson($cmspage)
            );
        }
    }

    /**
     * get cmspage tree block
     *
     * @access protected
     * @return Ibrams_CmsExtended_Block_Adminhtml_Cmspage_Widget_Chooser
     * @author Ultimate Module Creator
     */
    protected function _getCmspageTreeBlock()
    {
        return $this->getLayout()->createBlock(
            'ibrams_cmsextended/adminhtml_cmspage_widget_chooser',
            '',
            array(
                'id' => $this->getRequest()->getParam('uniq_id'),
                'use_massaction' => $this->getRequest()->getParam('use_massaction', false)
            )
        );
    }
}

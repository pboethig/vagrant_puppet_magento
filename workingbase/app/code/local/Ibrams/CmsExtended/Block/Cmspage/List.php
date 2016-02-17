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
 * Cmspage list block
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Cmspage_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $cmspages = Mage::getResourceModel('ibrams_cmsextended/cmspage_collection')
                         ->addStoreFilter(Mage::app()->getStore())
                         ->addFieldToFilter('status', 1);
        ;
        $cmspages->getSelect()->order('main_table.position');
        $this->setCmspages($cmspages);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Ibrams_CmsExtended_Block_Cmspage_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getCmspages()->addFieldToFilter('level', 1);
        if ($this->_getDisplayMode() == 0) {
            $pager = $this->getLayout()->createBlock(
                'page/html_pager',
                'ibrams_cmsextended.cmspages.html.pager'
            )
            ->setCollection($this->getCmspages());
            $this->setChild('pager', $pager);
            $this->getCmspages()->load();
        }
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * get the display mode
     *
     * @access protected
     * @return int
     * @author Ultimate Module Creator
     */
    protected function _getDisplayMode()
    {
        return Mage::getStoreConfigFlag('ibrams_cmsextended/cmspage/tree');
    }

    /**
     * draw cmspage
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage
     * @param int $level
     * @return int
     * @author Ultimate Module Creator
     */
    public function drawCmspage($cmspage, $level = 0)
    {
        $html = '';
        $recursion = $this->getRecursion();
        if ($recursion !== '0' && $level >= $recursion) {
            return '';
        }
        $storeIds = Mage::getResourceSingleton(
            'ibrams_cmsextended/cmspage'
        )
        ->lookupStoreIds($cmspage->getId());
        $validStoreIds = array(0, Mage::app()->getStore()->getId());
        if (!array_intersect($storeIds, $validStoreIds)) {
            return '';
        }
        if (!$cmspage->getStatus()) {
            return '';
        }
        $children = $cmspage->getChildrenCmspages();
        $activeChildren = array();
        if ($recursion == 0 || $level < $recursion-1) {
            foreach ($children as $child) {
                $childStoreIds = Mage::getResourceSingleton(
                    'ibrams_cmsextended/cmspage'
                )
                ->lookupStoreIds($child->getId());
                $validStoreIds = array(0, Mage::app()->getStore()->getId());
                if (!array_intersect($childStoreIds, $validStoreIds)) {
                    continue;
                }
                if ($child->getStatus()) {
                    $activeChildren[] = $child;
                }
            }
        }
        $html .= '<li>';
        $html .= '<a href="'.$cmspage->getCmspageUrl().'">'.$cmspage->getName().'</a>';
        if (count($activeChildren) > 0) {
            $html .= '<ul>';
            foreach ($children as $child) {
                $html .= $this->drawCmspage($child, $level+1);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        return $html;
    }

    /**
     * get recursion
     *
     * @access public
     * @return int
     * @author Ultimate Module Creator
     */
    public function getRecursion()
    {
        if (!$this->hasData('recursion')) {
            $this->setData('recursion', Mage::getStoreConfig('ibrams_cmsextended/cmspage/recursion'));
        }
        return $this->getData('recursion');
    }
}

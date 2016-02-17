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
 * Cmspage subtree block
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Cmspage_Widget_Subtree extends Ibrams_CmsExtended_Block_Cmspage_List implements
    Mage_Widget_Block_Interface
{
    protected $_template = 'ibrams_cmsextended/cmspage/widget/subtree.phtml';
    /**
     * prepare the layout
     *
     * @access protected
     * @return Ibrams_CmsExtended_Block_Cmspage_Widget_Subtree
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        $this->getCmspages()->addFieldToFilter('entity_id', $this->getCmspageId());
        return $this;
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
        return 1;
    }

    /**
     * get the element id
     *
     * @access protected
     * @return int
     * @author Ultimate Module Creator
     */
    public function getUniqueId()
    {
        if (!$this->getData('uniq_id')) {
            $this->setData('uniq_id', uniqid('subtree'));
        }
        return $this->getData('uniq_id');
    }
}

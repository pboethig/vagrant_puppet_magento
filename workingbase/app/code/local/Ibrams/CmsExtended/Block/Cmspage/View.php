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
 * Cmspage view block
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Cmspage_View extends Mage_Core_Block_Template
{
    /**
     * get the current cmspage
     *
     * @access public
     * @return mixed (Ibrams_CmsExtended_Model_Cmspage|null)
     * @author Ultimate Module Creator
     */
    public function getCurrentCmspage()
    {
        return Mage::registry('current_cmspage');
    }
}

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
 * Cmspage helper
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Helper_Cmspage extends Mage_Core_Helper_Abstract
{

    /**
     * get the url to the cmspages list page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getCmspagesUrl()
    {
        if ($listKey = Mage::getStoreConfig('ibrams_cmsextended/cmspage/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('ibrams_cmsextended/cmspage/index');
    }

    /**
     * check if breadcrumbs can be used
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function getUseBreadcrumbs()
    {
        return Mage::getStoreConfigFlag('ibrams_cmsextended/cmspage/breadcrumbs');
    }
    const CMSPAGE_ROOT_ID = 1;
    /**
     * get the root id
     *
     * @access public
     * @return int
     * @author Ultimate Module Creator
     */
    public function getRootCmspageId()
    {
        return self::CMSPAGE_ROOT_ID;
    }

    /**
     * check if the rss for cmspage is enabled
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function isRssEnabled()
    {
        return  Mage::getStoreConfigFlag('rss/config/active') &&
            Mage::getStoreConfigFlag('ibrams_cmsextended/cmspage/rss');
    }

    /**
     * get the link to the cmspage rss list
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRssUrl()
    {
        return Mage::getUrl('ibrams_cmsextended/cmspage/rss');
    }
}

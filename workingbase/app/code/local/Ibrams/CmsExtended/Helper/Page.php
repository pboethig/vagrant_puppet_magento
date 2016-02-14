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
 * Page helper
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Helper_Page extends Mage_Core_Helper_Abstract
{

    /**
     * get the url to the pages list page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPagesUrl()
    {
        if ($listKey = Mage::getStoreConfig('ibrams_cmsextended/page/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('ibrams_cmsextended/page/index');
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
        return Mage::getStoreConfigFlag('ibrams_cmsextended/page/breadcrumbs');
    }
    const PAGE_ROOT_ID = 1;
    /**
     * get the root id
     *
     * @access public
     * @return int
     * @author Ultimate Module Creator
     */
    public function getRootPageId()
    {
        return self::PAGE_ROOT_ID;
    }

    /**
     * check if the rss for page is enabled
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function isRssEnabled()
    {
        return  Mage::getStoreConfigFlag('rss/config/active') &&
            Mage::getStoreConfigFlag('ibrams_cmsextended/page/rss');
    }

    /**
     * get the link to the page rss list
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRssUrl()
    {
        return Mage::getUrl('ibrams_cmsextended/page/rss');
    }
}

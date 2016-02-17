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
 * Cmspage RSS block
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Block_Cmspage_Rss extends Mage_Rss_Block_Abstract
{
    /**
     * Cache tag constant for feed reviews
     *
     * @var string
     */
    const CACHE_TAG = 'block_html_cmsextended_cmspage_rss';

    /**
     * constructor
     *
     * @access protected
     * @return void
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        $this->setCacheTags(array(self::CACHE_TAG));
        /*
         * setting cache to save the rss for 10 minutes
         */
        $this->setCacheKey('ibrams_cmsextended_cmspage_rss');
        $this->setCacheLifetime(600);
    }

    /**
     * toHtml method
     *
     * @access protected
     * @return string
     * @author Ultimate Module Creator
     */
    protected function _toHtml()
    {
        $url    = Mage::helper('ibrams_cmsextended/cmspage')->getCmspagesUrl();
        $title  = Mage::helper('ibrams_cmsextended')->__('Cmspages');
        $rssObj = Mage::getModel('rss/rss');
        $data  = array(
            'title'       => $title,
            'description' => $title,
            'link'        => $url,
            'charset'     => 'UTF-8',
        );
        $rssObj->_addHeader($data);
        $collection = Mage::getModel('ibrams_cmsextended/cmspage')->getCollection()
            ->addFieldToFilter('status', 1)
            ->addStoreFilter(Mage::app()->getStore())
            ->addFieldToFilter('in_rss', 1)
            ->setOrder('created_at');
        $collection->load();
        foreach ($collection as $item) {
            $description = '<p>';
            if (!$item->getStatusPath()) {
                continue;
            }            $description .= '<div>'.
                Mage::helper('ibrams_cmsextended')->__('name').': 
                '.$item->getName().
                '</div>';
            $description .= '<div>'.
                Mage::helper('ibrams_cmsextended')->__('version').': 
                '.$item->getVersion().
                '</div>';
            $description .= '<div>'.
                Mage::helper('ibrams_cmsextended')->__("accessroles").': '
                .Mage::getSingleton('ibrams_cmsextended/cmspage_attribute_source_accessroles')->getOptionText($item->getAccessroles()).
                '</div>';
            $description .= '<div>'.Mage::helper('ibrams_cmsextended')->__('startdate').': '.Mage::helper('core')->formatDate($item->getStartdate(), 'full').'</div>';
            $description .= '<div>'.Mage::helper('ibrams_cmsextended')->__('enddate').': '.Mage::helper('core')->formatDate($item->getEnddate(), 'full').'</div>';
            $description .= '<div>'.
                Mage::helper('ibrams_cmsextended')->__('type').': 
                '.$item->getType().
                '</div>';
            $description .= '<div>'.Mage::helper('ibrams_cmsextended')->__("showassociatedproducts").':'.(($item->getShowassociatedproducts() == 1) ? Mage::helper('ibrams_cmsextended')->__('Yes') : Mage::helper('ibrams_cmsextended')->__('No')).'</div>';
            $description .= '<div>'.Mage::helper('ibrams_cmsextended')->__("showassociatedcategories").':'.(($item->getShowassociatecategories() == 1) ? Mage::helper('ibrams_cmsextended')->__('Yes') : Mage::helper('ibrams_cmsextended')->__('No')).'</div>';
            $description .= '<div>'.
                Mage::helper('ibrams_cmsextended')->__("permittedroleactions").': '
                .Mage::getSingleton('ibrams_cmsextended/cmspage_attribute_source_permittedroleactions')->getOptionText($item->getPermittedroleactions()).
                '</div>';
            $description .= '<div>'.
                Mage::helper('ibrams_cmsextended')->__('htmlcontent').': 
                '.$item->getHtmlcontent().
                '</div>';
            $description .= '<div>'.
                Mage::helper('ibrams_cmsextended')->__("layout").': '
                .Mage::getSingleton('ibrams_cmsextended/cmspage_attribute_source_layout')->getOptionText($item->getLayout()).
                '</div>';
            $description .= '<div>'.
                Mage::helper('ibrams_cmsextended')->__('layoutupdate').': 
                '.$item->getLayoutupdate().
                '</div>';
            $description .= '<div>'.
                Mage::helper('ibrams_cmsextended')->__('author').': 
                '.$item->getAuthor().
                '</div>';
            $description .= '</p>';
            $data = array(
                'title'       => $item->getName(),
                'link'        => $item->getCmspageUrl(),
                'description' => $description
            );
            $rssObj->_addEntry($data);
        }
        return $rssObj->createRssXml();
    }
}

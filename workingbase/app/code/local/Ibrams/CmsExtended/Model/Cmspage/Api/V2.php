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
class Ibrams_CmsExtended_Model_Cmspage_Api_V2 extends Ibrams_CmsExtended_Model_Cmspage_Api
{
    /**
     * Cmspage info
     *
     * @access public
     * @param int $cmspageId
     * @return object
     * @author Ultimate Module Creator
     */
    public function info($cmspageId)
    {
        $result = parent::info($cmspageId);
        $result = Mage::helper('api')->wsiArrayPacker($result);
        foreach ($result->products as $key => $value) {
            $result->products[$key] = array('key' => $key, 'value' => $value);
        }
        foreach ($result->categories as $key => $value) {
            $result->categories[$key] = array('key' => $key, 'value' => $value);
        }
        return $result;
    }
}

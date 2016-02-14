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
 * Cmspage - Category relation resource model collection
 *
 * @category    Ibrams
 * @package     Ibrams_CmsExtended
 * @author      Ultimate Module Creator
 */
class Ibrams_CmsExtended_Model_Resource_Cmspage_Category_Collection extends Mage_Catalog_Model_Resource_Category_Collection
{
    /**
     * remember if fields have been joined
     *
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * join the link table
     *
     * @access public
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Category_Collection
     * @author Ultimate Module Creator
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('ibrams_cmsextended/cmspage_category')),
                'related.category_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add cmspage filter
     *
     * @access public
     * @param Ibrams_CmsExtended_Model_Cmspage | int $cmspage
     * @return Ibrams_CmsExtended_Model_Resource_Cmspage_Category_Collection
     * @author Ultimate Module Creator
     */
    public function addCmspageFilter($cmspage)
    {
        if ($cmspage instanceof Ibrams_CmsExtended_Model_Cmspage) {
            $cmspage = $cmspage->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.cmspage_id = ?', $cmspage);
        return $this;
    }
}

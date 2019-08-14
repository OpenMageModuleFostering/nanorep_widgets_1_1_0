<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */
class NanoRep_Widgets_Model_Resource_Answer_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'nanorep_answer_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'answer_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('nanorepwidgets/answer');
        $this
            ->addFilterToMap('entity_id', 'main_table.entity_id')
            ->addFilterToMap('product_id', 'main_table.product_id');
    }
	
	public function setSelect($select){
		$this->_select = $select;
	}
	
	/**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        $countSelect->columns('COUNT(*)');
		$countSelect = 'SELECT COUNT(*) FROM ('.$countSelect.') AS A';	
        return $countSelect;
    }
		
}


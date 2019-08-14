<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */
 
class NanoRep_Widgets_Block_Adminhtml_Answer_Grid_Export extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('nanorep_answer_grid');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
	
	protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	
	public function getAttributeId($name)	{
		$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
		$code = $eavAttribute->getIdByCode('catalog_product', $name);
		return $code;
	}
	
    protected function _prepareCollection()
    {
		$store = $this->_getStore();
		$collection = Mage::getResourceModel('nanorepwidgets/answer_collection');
		
		$name_id = $this->getAttributeId('name');
		$price_id = $this->getAttributeId('price');
		
		$adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
		// $adapter is your Zend_Db_Adapter...
		$joinConditionName = 'at_name.entity_id = e.entity_id' 
		    . $adapter->quoteInto(' AND at_name.attribute_id = ?', $name_id)
		    . $adapter->quoteInto(' AND at_name.store_id = ?', $store->getId());
		$subQueryName = new Varien_Db_Select($adapter);
		$subQueryName->from(array('e' => 'catalog_product_entity'), 
			array('e.entity_id'))
		    ->join(
		         array('at_name' => 'catalog_product_entity_varchar'), 
		         $joinConditionName,
		         array('name' => 'at_name.value')
		    );
			
		$joinConditionPrice = 'at_price.entity_id = e.entity_id' 
		    . $adapter->quoteInto(' AND at_price.attribute_id = ?', $price_id)
		    . $adapter->quoteInto(' AND at_price.store_id = ?', $store->getId());
		$subQueryPrice = new Varien_Db_Select($adapter);
		$subQueryPrice->from(array('e' => 'catalog_product_entity'), 
			array('e.entity_id'))
		    ->join(
		         array('at_price' => 'catalog_product_entity_decimal'), 
		         $joinConditionPrice,
		         array('price' => 'at_price.value')
		    );
		
		
		// and then the main query
		$query = new Varien_Db_Select($adapter);
		$answerWithQuerySql = 'CONCAT(`answer_title`, ":::", GROUP_CONCAT(DISTINCT `query` SEPARATOR "###"))';
		$query->from(array('main_table' => 'nanorepwidgets_answer'), 
			array('main_table.*', 'answer_count' => 'COUNT(`answer_id`)', 'answer_title_with_query' => $answerWithQuerySql))
		    ->joinLeft(array('product_name_table' => $subQueryName),
		        'main_table.product_id = product_name_table.entity_id',
				array('product_name' => 'product_name_table.name'))
			->joinLeft(array('product_price_table' => $subQueryPrice),
		        'main_table.product_id = product_price_table.entity_id',
				array('product_price' => 'product_price_table.price'));
		$query->group(array('main_table.answer_id', 'main_table.product_id'));
		$query->order(array('answer_id ASC', 'date DESC'));
		
		$collection->setSelect($query);
		
		$filter = $this->getParam($this->getVarNameFilter(), null);

		if (is_string($filter)) {
		    $data = $this->helper('adminhtml')->prepareFilterString($filter);
		    $this->_setFilterValues($data);
		}
		else if ($filter && is_array($filter)) {
		    $this->_setFilterValues($filter);
		}
		
        // echo $collection->getSelect();
		$this->setCollection($collection);
		parent::_prepareCollection();
		
        return $this;
    }
    protected function _prepareColumns()
    {
        $helper = Mage::helper('nanorepwidgets');
        $this->addColumn('product_id', array(
            'header' => $helper->__('Product #'),
            'index'  => 'product_id',
            'order_callback' => array($this, '_sort')
            // 'sortable'  => false
        ));
		$this->addColumn('product_name', array(
            'header' => $helper->__('Product Name'),
            'index'  => 'product_name',
            'filter_condition_callback' => array($this, '_productNameFilter'),
            'order_callback' => array($this, '_sort')
        ));
		$this->addColumn('product_price', array(
            'header' => $helper->__('Product Price'),
            'type' => 'price',
            'index'  => 'product_price',
            'filter_condition_callback' => array($this, '_productPriceFilter'),
            'order_callback' => array($this, '_sort')
        ));
        $this->addColumn('answer_id', array(
            'header'       => $helper->__('Answer Id'),
            'index'        => 'answer_id',
            'order_callback' => array($this, '_sort')
        ));
		$this->addColumn('answer_title_with_query', array(
            'header'       => $helper->__('Answer Title'),
            'index'        => 'answer_title_with_query',
            'filter_condition_callback' => array($this, '_answerTitleFilter'),
            'filter_index' => 'CONCAT(`answer_title`, ":::", GROUP_CONCAT(DISTINCT `query` SEPARATOR "###"))',
            'renderer'  => 'NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Answer_Title_Pretty',
            'order_callback' => array($this, '_sort')
        ));
		$this->addColumn('answer_body', array(
            'header'       => $helper->__('Answer Body'),
            'index'        => 'answer_body',
            'renderer'  => 'NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Answer_Body_Pretty',
            'sortable'  => false
        ));
        $this->addColumn('answer_count', array(
            'header'       => $helper->__('Count'),
            'index'        => 'answer_count',
            'filter' => false,
            'order_callback' => array($this, '_sort')
        ));
		$this->addColumn('date', array(
            'header' => Mage::helper('sales')->__('Date'),
            'index' => 'date',
            'type' => 'datetime',
            'width' => '100px',
            'sortable'  => false
        ));
		
        $this->addExportType('*/*/exportCsv', $helper->__('CSV'));
        $this->addExportType('*/*/exportExcel', $helper->__('Excel XLS'));
        return parent::_prepareColumns();
    }
    /**
	 * Sets sorting order by some column
	 *
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
	 *
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	protected function _setCollectionOrder($column)
	{
	    if ($column->getOrderCallback()) {
	        call_user_func($column->getOrderCallback(), $this->getCollection(), $column);
	
	        return $this;
	    }
	
	    return parent::_setCollectionOrder($column);
	}

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    
    protected function _productNameFilter($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $collection->getSelect()->where("`product_name_table`.`name` LIKE '%".$value."%'");
	    return $this;
	}
	
	protected function _productPriceFilter($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $collection->getSelect()->where("`product_price_table`.`price` < ".$value["to"]." AND `product_price_table`.`price` > ".$value["from"]);
	    return $this;
	}
	
	protected function _answerTitleFilter($collection, $column){
		if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $collection->getSelect()->where("`query` LIKE '%".$value."%'");;
	    return $this;
	}
	
	protected function _sort($collection, $column){
		$collection->getSelect()->reset( Zend_Db_Select::ORDER );
		$collection->getSelect()->order(array($column->getIndex() . ' ' . strtoupper($column->getDir()), 'date DESC'));
		return $this;
	}
}
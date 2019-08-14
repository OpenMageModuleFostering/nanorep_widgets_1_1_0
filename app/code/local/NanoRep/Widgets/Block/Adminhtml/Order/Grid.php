<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */
 
class NanoRep_Widgets_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('nanorep_order_grid');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('nanorepwidgets/query_collection')
            ->join(array('o' => 'sales/order'), 'main_table.order_id = o.entity_id')
			->join(array('s' => 'sales/order_address'), 'main_table.order_id = s.parent_id', array(
				'shipping_firstname' => 'firstname',
				'shipping_lastname' => 'lastname'
			))
			->join(array('p' => 'sales/order_item'), 'main_table.order_id = p.order_id AND main_table.product_id = p.product_id', array(
                'product_name'  => 'name',
                'product_price' => 'row_total_incl_tax',
                'qty_ordered'       => 'qty_ordered'
            ))
            ->addExpressionFieldToSelect(
                'fullname',
                'CONCAT({{customer_firstname}}, \' \', {{customer_lastname}})',
                array('customer_firstname' => 'o.customer_firstname', 'customer_lastname' => 'o.customer_lastname'))
            ->addExpressionFieldToSelect(
                'shipping_fullname',
                'CONCAT({{shipping_customer_firstname}}, \' \', {{shipping_customer_lastname}})',
                	array('shipping_customer_firstname' => 's.firstname', 'shipping_customer_lastname' => 's.lastname'))
			->addExpressionFieldToSelect(
                'questions',
                'GROUP_CONCAT(DISTINCT {{questions}} ORDER BY date ASC SEPARATOR ", ")',
				array('questions' => 'main_table.query'))
			->addExpressionFieldToSelect(
                'grouped_results',
                'GROUP_CONCAT(DISTINCT {{grouped_results}} ORDER BY date ASC SEPARATOR ":::")',
				array('grouped_results' => 'main_table.results'))
        ;
		$collection->getSelect()->where('s.address_type = "shipping"');
		$collection->getSelect()->where('p.row_total_incl_tax IS NOT NULL');
        $this->setCollection($collection);
		$collection->getSelect()->group(array('main_table.product_id', 'main_table.order_id'));
		$collection->getSelect()->order(array('main_table.product_id DESC'));
        parent::_prepareCollection();
        return $this;
    }
    protected function _prepareColumns()
    {
        $helper = Mage::helper('nanorepwidgets');
        $currency = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        $this->addColumn('product_id', array(
            'header' => $helper->__('Product #'),
            'index'  => 'product_id',
            'order_callback' => array($this, '_sort')
        ));
        $this->addColumn('product_name', array(
            'header'       => $helper->__('Products Purchased'),
            'index'        => 'product_name',
            'filter_condition_callback' => array($this, '_productNameFilter'),
            'order_callback' => array($this, '_sort')
            // 'filter' =>	false
        ));
		$this->addColumn('qty_ordered', array(
            'header'       => $helper->__('Quantity'),
            'index'        => 'qty_ordered',
            'order_callback' => array($this, '_sort')
        ));
        $this->addColumn('fullname', array(
            'header'       => $helper->__('Bill to Name'),
            'index'        => 'fullname',
            'filter_index' => 'CONCAT(customer_firstname, \' \', customer_lastname)',
            'order_callback' => array($this, '_sort')
        ));
        $this->addColumn('shipping_fullname', array(
            'header'       => $helper->__('Ship to Name'),
            'index'        => 'shipping_fullname',
            'filter_condition_callback' => array($this, '_shippingNameFilter'),
            'order_callback' => array($this, '_sort')
            // 'sortable'  => false
        ));
        $this->addColumn('product_price', array(
            'header'        => $helper->__('Purchased Price'),
            'index'         => 'product_price',
            'type'          => 'currency',
            'currency_code' => $currency,
            'order_callback' => array($this, '_sort')
        ));
		
		//Question(s) asked prior the submission
		$this->addColumn('questions', array(
            'header'       => $helper->__('Question(s) asked prior the submission'),
            'index'        => 'questions',
            'renderer'  => 'NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Questions',
            'filter_condition_callback' => array($this, '_queryFilter'),
            'sortable'  => false
        ));
		
		$this->addColumn('grouped_results', array(
            'header'       => $helper->__('Result(s) provided by nanoRep (Respectively)'),
            'index'        => 'grouped_results',
            'renderer'  => 'NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Results',
            'filter_condition_callback' => array($this, '_resultFilter'),
            'sortable'  => false
        ));
		
		$this->addColumn('purchased_on', array(
            'header' => $helper->__('Purchased On'),
            'type'   => 'datetime',
            'index'  => 'created_at',
            'filter_condition_callback' => array($this, '_createdAtFilter'),
            // 'sortable'  => false
            
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
        $collection->getSelect()->where("`p`.`name` LIKE '%".$value."%'");
	    return $this;
	}
	
	protected function _shippingNameFilter($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $collection->getSelect()->where("CONCAT(s.firstname, ' ', s.lastname) LIKE '%".$value."%'");
	    return $this;
	}
	
	protected function _resultFilter($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $collection->getSelect()->where("results LIKE '%".$value."%'");
	    return $this;
	}
	
	protected function _queryFilter($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $collection->getSelect()->where("query LIKE '%".$value."%'");
	    return $this;
	}
	
	protected function _createdAtFilter($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
		$to = Mage::getModel('core/date')->timestamp(strtotime($value["to"])); //Magento's timestamp function makes a usage of timezone and converts it to timestamp
		$to = date('Y-m-d', $to);
		$from = Mage::getModel('core/date')->timestamp(strtotime($value["from"])); //Magento's timestamp function makes a usage of timezone and converts it to timestamp
		$from = date('Y-m-d', $from);
        $collection->getSelect()->where("o.created_at < '".$to."' AND o.created_at > '".$from."'");
	    return $this;
	}
	
	protected function _sort($collection, $column){
		$collection->getSelect()->reset( Zend_Db_Select::ORDER );
		$collection->getSelect()->order(array($column->getIndex() . ' ' . strtoupper($column->getDir())));
		return $this;
	}
}
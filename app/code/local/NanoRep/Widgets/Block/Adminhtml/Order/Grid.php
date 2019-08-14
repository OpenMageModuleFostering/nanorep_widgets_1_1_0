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
			->join(array('p' => 'sales/order_item'), 'main_table.order_id = p.order_id', array(
                'product_name'  => 'name',
                'qty_ordered'       => 'qty_ordered'
            ))
            ->addExpressionFieldToSelect(
                'fullname',
                'CONCAT({{customer_firstname}}, \' \', {{customer_lastname}})',
                array('customer_firstname' => 'o.customer_firstname', 'customer_lastname' => 'o.customer_lastname'))
            ->addExpressionFieldToSelect(
                'shipping_fullname',
                '(SELECT CONCAT({{shipping_customer_firstname}}, \' \', {{shipping_customer_lastname}})
                	FROM sales_flat_order_address a
                	WHERE o.shipping_address_id = a.entity_id)',
                	array('shipping_customer_firstname' => 'a.firstname', 'shipping_customer_lastname' => 'a.lastname'))
			->addExpressionFieldToSelect(
                'questions',
                '(SELECT GROUP_CONCAT(DISTINCT {{questions}} ORDER BY date ASC SEPARATOR ", "))',
				array('questions' => 'main_table.query'))
			->addExpressionFieldToSelect(
                'grouped_results',
                '(SELECT GROUP_CONCAT(DISTINCT {{grouped_results}} ORDER BY date ASC SEPARATOR ":::"))',
				array('grouped_results' => 'main_table.results'))
        ;
        $this->setCollection($collection);
		$collection->getSelect()->group(array('main_table.product_id', 'main_table.order_id'));
		$collection->getSelect()->order('main_table.product_id');
		// echo $collection->getSelect();
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
        ));
        $this->addColumn('product_name', array(
            'header'       => $helper->__('Products Purchased'),
            'index'        => 'product_name',
            'filter' =>	false
        ));
		$this->addColumn('qty_ordered', array(
            'header'       => $helper->__('Quantity'),
            'index'        => 'qty_ordered',
        ));
        $this->addColumn('fullname', array(
            'header'       => $helper->__('Bill to Name'),
            'index'        => 'fullname',
            'filter_index' => 'CONCAT(customer_firstname, \' \', customer_lastname)',
        ));
        $this->addColumn('shipping_fullname', array(
            'header'       => $helper->__('Ship to Name'),
            'index'        => 'shipping_fullname',
            'filter_index' => 'CONCAT(shipping_customer_firstname, \' \', shipping_customer_lastname)',
            'filter' => false,
            'sortable'  => false
        ));
        $this->addColumn('grand_total', array(
            'header'        => $helper->__('Purchased Price'),
            'index'         => 'grand_total',
            'type'          => 'currency',
            'currency_code' => $currency
        ));
		
		//Question(s) asked prior the submission
		$this->addColumn('questions', array(
            'header'       => $helper->__('Question(s) asked prior the submission'),
            'index'        => 'questions',
            'renderer'  => 'NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Questions',
            'filter' => false,
            'sortable'  => false
        ));
		
		$this->addColumn('grouped_results', array(
            'header'       => $helper->__('Result(s) provided by nanoRep (Respectively)'),
            'index'        => 'grouped_results',
            'renderer'  => 'NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Results',
            'filter' => false,
            'sortable'  => false
        ));
		
		$this->addColumn('purchased_on', array(
            'header' => $helper->__('Purchased On'),
            'type'   => 'datetime',
            'index'  => 'created_at',
            'filter' => false,
            // 'sortable'  => false
            
        ));
        $this->addExportType('*/*/exportCsv', $helper->__('CSV'));
        $this->addExportType('*/*/exportExcel', $helper->__('Excel XLS'));
        return parent::_prepareColumns();
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
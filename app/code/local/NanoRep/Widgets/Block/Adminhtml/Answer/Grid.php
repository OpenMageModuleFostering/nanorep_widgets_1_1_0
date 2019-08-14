<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */
 
class NanoRep_Widgets_Block_Adminhtml_Answer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	private $_productNameSql, $_productCollection;
	private $_productPriceSql;
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
	
    protected function _prepareCollection()
    {
		$store = $this->_getStore();
        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name');
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $productCollection->addStoreFilter($store);
            $productCollection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $productCollection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
        );
		$productNameSql = $productCollection->getSelect();
		$productNameSql = str_replace('`e`.*, ', '', $productNameSql);
		$productNameSql = str_replace(', `at_price`.`value` AS `price`', '', $productNameSql);
		$productNameSql = '('.$productNameSql.' WHERE main_table.product_id = e.entity_id)';
		$this->_productNameSql = $productNameSql;
		$this->_productCollection = $productCollection;
		$collection = Mage::getResourceModel('nanorepwidgets/answer_collection')
			->addExpressionFieldToSelect(
                'product_name',
                $productNameSql, array('product_name' => 'main_table.product_name'))
		;
		
		$productPriceSql = $productCollection->getSelect();
		$productPriceSql = str_replace('`e`.*, ', '', $productPriceSql);
		$productPriceSql = str_replace('`at_name`.`value` AS `name`, ', '', $productPriceSql);
		$productPriceSql = '('.$productPriceSql.' WHERE main_table.product_id = e.entity_id)';
		$this->_productPriceSql = $productNameSql;
		$this->_productCollection = $productCollection;
		
		$collection->addExpressionFieldToSelect(
                'product_price',
                $productPriceSql, array('product_price' => 'main_table.product_price'))
		;
		
		$collection->addExpressionFieldToSelect(
                'answer_count',
                'COUNT({{answer_count}})'
                , array('answer_count' => 'main_table.answer_id'))
		;
		$collection->addExpressionFieldToSelect(
                'answer_title_with_query',
                '(SELECT CONCAT({{answer_title}}, ":::", GROUP_CONCAT(DISTINCT {{query}} SEPARATOR "###")))'
                , array('answer_title_with_query' => 'main_table.answer_title_with_query', 'answer_title' => 'main_table.answer_title', 'query' => 'main_table.query'))
		;
		$collection->getSelect()->order('answer_id', 'ASC');
		$collection->getSelect()->group(array('main_table.answer_id'));
		
		$filter   = $this->getParam($this->getVarNameFilter(), null);

		if (is_string($filter)) {
		    $data = $this->helper('adminhtml')->prepareFilterString($filter);
		    $this->_setFilterValues($data);
		}
		else if ($filter && is_array($filter)) {
		    $this->_setFilterValues($filter);
		}
		
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
            'sortable'  => false
        ));
		$this->addColumn('product_name', array(
            'header' => $helper->__('Product Name'),
            'index'  => 'product_name',
            // 'filter_condition_callback' => array($this, '_productNameFilter')
            'filter' => false,
            'sortable'  => false
            
        ));
		$this->addColumn('product_price', array(
            'header' => $helper->__('Product Price'),
            'index'  => 'product_price',
            'filter' => false,
            'sortable'  => false
        ));
		// $this->addColumn('query', array(
            // 'header'       => $helper->__('Query'),
            // 'index'        => 'query',
            // 'renderer'  => 'NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Answer_Query'
        // ));
        $this->addColumn('answer_id', array(
            'header'       => $helper->__('Answer Id'),
            'index'        => 'answer_id',
            'sortable'  => false
        ));
		$this->addColumn('answer_title_with_query', array(
            'header'       => $helper->__('Answer Title'),
            'index'        => 'answer_title_with_query',
            'filter' => false,
            'renderer'  => 'NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Answer_Title',
            'sortable'  => false
        ));
		$this->addColumn('answer_body', array(
            'header'       => $helper->__('Answer Body'),
            'index'        => 'answer_body',
            'renderer'  => 'NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Answer_Body',
            'sortable'  => false
        ));
        $this->addColumn('answer_count', array(
            'header'       => $helper->__('Count'),
            'index'        => 'answer_count',
            'filter' => false,
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
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    
    // protected function _productNameFilter($collection, $column)
	// {
		// if (!$value = $column->getFilter()->getValue()) {
            // return $this;
        // }
		// $collection->addFieldToFilter('product_name', array('like' => '%'.$value.'%'));
		// echo $collection->getSelect();//->where("main_table.product_name LIKE \"%".$value."\%");
// 	    
	    // return $this;
	// }
}
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
		
		$collection = Mage::getResourceModel('nanorepwidgets/answer_collection')
			->addExpressionFieldToSelect(
                'product_name',
                $productNameSql, array('product_name' => 'main_table.product_name'))
		;
		
		$productPriceSql = $productCollection->getSelect();
		$productPriceSql = str_replace('`e`.*, ', '', $productPriceSql);
		$productPriceSql = str_replace('`at_name`.`value` AS `name`, ', '', $productPriceSql);
		$productPriceSql = '('.$productPriceSql.' WHERE main_table.product_id = e.entity_id)';
		
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
		$collection->getSelect()->group('answer_id');
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
        ));
		$this->addColumn('product_name', array(
            'header' => $helper->__('Product Name'),
            'index'  => 'product_name',
        ));
		$this->addColumn('product_price', array(
            'header' => $helper->__('Product Price'),
            'index'  => 'product_price',
        ));
		// $this->addColumn('query', array(
            // 'header'       => $helper->__('Query'),
            // 'index'        => 'query',
            // 'renderer'  => 'NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Answer_Query'
        // ));
        $this->addColumn('answer_id', array(
            'header'       => $helper->__('Answer Id'),
            'index'        => 'answer_id',
        ));
		$this->addColumn('answer_title_with_query', array(
            'header'       => $helper->__('Answer Title'),
            'index'        => 'answer_title_with_query',
            'renderer'  => 'NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Answer_Title_Pretty'
        ));
		$this->addColumn('answer_body', array(
            'header'       => $helper->__('Answer Body'),
            'index'        => 'answer_body',
            'renderer'  => 'NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Answer_Body_Pretty'
        ));
        $this->addColumn('answer_count', array(
            'header'       => $helper->__('Count'),
            'index'        => 'answer_count',
        ));
		$this->addColumn('date', array(
            'header' => Mage::helper('sales')->__('Date'),
            'index' => 'date',
            'type' => 'datetime',
            'width' => '100px',
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
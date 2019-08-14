<?php

/**
 * NanoRep Widgets Extension.
 *
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 *
 * @author		Dan Aharon-Shalom
 */
class Nanorep_Widgets_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return true;
    }

    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Purchases Assisted by nanoRep'));
        $this->loadLayout();
        $this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('nanorepwidgets/adminhtml_order'));
        $this->renderLayout();
    }
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('nanorepwidgets/adminhtml_order_grid')->toHtml()
        );
    }
    public function exportCsvAction()
    {
        $fileName = 'nanorep_conversion_report.csv';
        $grid = $this->getLayout()->createBlock('nanorepwidgets/adminhtml_order_grid_export');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    public function exportExcelAction()
    {
        $fileName = 'nanorep_conversion_report.xls';
        $grid = $this->getLayout()->createBlock('nanorepwidgets/adminhtml_order_grid_export');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}

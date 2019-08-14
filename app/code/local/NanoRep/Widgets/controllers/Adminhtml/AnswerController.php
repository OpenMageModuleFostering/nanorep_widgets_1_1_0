<?php

/**
 * NanoRep Widgets Extension.
 *
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 *
 * @author		Dan Aharon-Shalom
 */
class Nanorep_Widgets_Adminhtml_AnswerController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return true;
    }

    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('nanoRep search report'));
        $this->loadLayout();
        $this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('nanorepwidgets/adminhtml_answer'));
        $this->renderLayout();
    }
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('nanorepwidgets/adminhtml_answer_grid')->toHtml()
        );
    }
    public function exportCsvAction()
    {
        $fileName = 'nanorep_search_report.csv';
        $grid = $this->getLayout()->createBlock('nanorepwidgets/adminhtml_answer_grid_export');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    public function exportExcelAction()
    {
        $fileName = 'nanorep_search_report.xls';
        $grid = $this->getLayout()->createBlock('nanorepwidgets/adminhtml_answer_grid_export');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}

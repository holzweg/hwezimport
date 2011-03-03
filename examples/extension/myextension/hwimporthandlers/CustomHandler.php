<?php
// load a custom library, e.g. PHPExcel to read Excel files
require_once '/path/to/library/PHPExcel.php');

/**
 * Sample handler class which imports contact data from an Excel file.
 * Uses PHPExcel (http://phpexcel.codeplex.com/) to access Excel data
 */
class MyExtension_Handler_CustomHandlerExcel extends HwImport_Handler_Abstract
{
    /**
     * Determines the content class to use for new nodes
     * @var string
     */
    protected $_contentClassIdentifier = 'my_content_class_identifier';

    /**
     * Row to start from
     * Assumes row 1 is a heading row, so let's start at row 2
     * @var int
     */
    protected $_startRow = 2;

    public function run()
    {
        // check prerequisites
        parent::run();

        // counter while iterating through rows
        $counter = 0;

        // starting row + offset set on command line
        $row = $this->getStartRow() + $this->getOffset();

        $hasContent = true;
        while($hasContent == true) {
            // run until we hit an empty cell in column A
            $name = trim($this->_getCellValue($row, 0));
            if(empty($name)) {
                $hasContent = false;
                continue;
            }

            // check if we are over limit and abort otherwise
            if($this->getLimit() > 0 && $counter == $this->getLimit()) {
                $hasContent = false;
                continue;
            }

            // object data
            // assumes all your attributes in your content class are text lines
            // except the country attribute which is set to country
            $data = array();
            $data['name'] = $name;
            $data['street'] = trim($this->_getCellValue($row, 1));
            $data['zip'] = trim($this->_getCellValue($row, 2));
            $data['city'] = trim($this->_getCellValue($row, 3));

            // must be an Alpha2-ISO-Code in Excel file (e.g. DE or US) for the country attribute
            // depending on the attribute you must pass a custom formatted string
            // see https://github.com/ezsystems/ezpublish/blob/master/doc/features/3.9/to_from_string_datatype_functionality.txt
            // for details on different data types
            $data['country'] = trim($this->_getCellValue($row, 4));

            $data['telephone'] = trim($this->_getCellValue($row, 5));
            $data['mobile'] = trim($this->_getCellValue($row, 6));
            $data['email'] = trim($this->_getCellValue($row, 7));

            // generate record
            // @see HwImport_Handler_Abstract::_generateBaseRecord()
            $record = $this->_generateBaseRecord();
            $record['attributes'] = $data;

            // publish object
            $contentObject = eZContentFunctions::createAndPublishObject($record);

            // some output
            $this->getCli()->output($counter + 1 . '# - Imported ' . $name);

            // increase our iterators
            ++$row;
            ++$counter;
        }

        // clean up
        if(isset($this->_excel)) {
            $this->_excel->disconnectWorksheets();
            unset($this->_excel);
        }
    }

    /**
     * Get a PHPExcel instance with our datafile loaded
     * 
     * @return PHPExcel
     */
    protected function _getExcel()
    {
        if($this->_excel === null) {
            $this->_excel = PHPExcel_IOFactory::load($this->getDatafile());
        }

        return $this->_excel;
    }

    /**
     * Get a cell value by row and column
     *
     * @param  int $row
     * @param  int $column
     * @return string
     */
    protected function _getCellValue($row, $column)
    {
        return $this->_getExcel()->getActiveSheet()->getCellByColumnAndRow($column, $row)->getValue();
    }
}

// register the handler to HwImport
// HwImport::registerHandler($identifier, $className)
HwImport::registerHandler('MyCustomHandler', 'MyExtension_Handler_CustomHandlerExcel');
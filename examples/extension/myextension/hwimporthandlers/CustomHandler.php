<?php
// load a custom library, e.g. PHPExcel to read Excel files
require_once '/path/to/library/PHPExcel.php');

/**
 * Sample handler class which imports contact data from an Excel 2007 table
 * Uses PHPExcel (http://phpexcel.codeplex.com/) to access Excel data
 */
class MyExtension_Handler_CustomHandlerExcel2k7 extends HwImport_Handler_Abstract
{
    /**
     * Determines the content class to use for new nodes
     * @var string
     */
    protected $_contentClassIdentifier = 'my_content_class_identifier';

    public function run()
    {
        // check prerequisites
        parent::run();

        // assumes row 1 is a heading row, so let's start at row 2
        $row = 2;

        $hasContent = true;
        while($hasContent == true) {
            // run until we hit an empty cell
            $name = trim($this->_getCellValue($row, 0));
            if(empty($name)) {
                $hasContent = false;
                continue;
            }

            // basic data
            $data = array();
            $data['name'] = $name;
            $data['street'] = trim($this->_getCellValue($row, 1));
            $data['zip'] = trim($this->_getCellValue($row, 2));
            $data['city'] = trim($this->_getCellValue($row, 3));
            $data['telephone'] = trim($this->_getCellValue($row, 5));
            $data['mobile'] = trim($this->_getCellValue($row, 6));
            $data['email'] = trim($this->_getCellValue($row, 7));

            // generate record
            // @see HwImport_Handler_Abstract::_generateBaseRecord()
            $record = $this->_generateBaseRecord();
            $record['attributes'] = $data;

            // publish object
            $contentObject = eZContentFunctions::createAndPublishObject($record);
            $dataMap = $contentObject->dataMap();

            // set the country attribute with type's fromString() method
            // see https://github.com/ezsystems/ezpublish/blob/master/doc/features/3.9/to_from_string_datatype_functionality.txt
            $country = $dataMap['country'];
            $countryType = new eZCountryType();
            $countryType->fromString($country, trim($this->_getCellValue($row, 4)));
            $country->sync();

            // some output
            $this->getCli()->output('Imported ' . $name);

            ++$row;
        }

        // clean up
        $this->_excel->disconnectWorksheets();
        unset($this->_excel);
    }

    protected function _getExcel()
    {
        if($this->_excel === null) {
            $reader = new PHPExcel_Reader_Excel2007();
            $this->_excel = $reader->load($this->getDatafile());
        }

        return $this->_excel;
    }

    protected function _getCellValue($row, $column)
    {
        return $this->_getExcel()->getActiveSheet()->getCellByColumnAndRow($column, $row)->getValue();
    }
}

// register the handler to HwImport
// HwImport::registerHandler($identifier, $className)
HwImport::registerHandler('MyCustomHandler', 'MyExtension_Handler_CustomHandlerExcel2k7');
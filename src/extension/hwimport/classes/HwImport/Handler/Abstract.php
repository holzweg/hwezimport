<?php
/**
 * HwImport
 *
 * Copyright (c) 2011 holzweg e-commerce solutions - http://www.holzweg.com
 * All rights reserved.
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   HwImport
 * @package    HwImport
 * @author     Mathias Geat <mathias.geat@holzweg.com>
 * @copyright  2011 holzweg e-commerce solutions - http://www.holzweg.com
 * @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL v3
 */

/**
 * HwImport handler abstract implements basic helper methods
 *
 * @category   HwImport
 * @package    HwImport
 * @author     Mathias Geat <mathias.geat@holzweg.com>
 * @copyright  2011 holzweg e-commerce solutions - http://www.holzweg.com
 * @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL v3
 */
abstract class HwImport_Handler_Abstract implements HwImport_Handler_Interface
{
    /**
     * Determines the content class to use for new nodes
     * @var string
     */
    protected $_contentClassIdentifier;

    /**
     * Data file location
     * @var string
     */
    protected $_datafile;

    /**
     * CLI object
     * @var eZCli
     */
    protected $_cli;

    /**
     * Importing user
     * @var eZUser
     */
    protected $_user;

    /**
     * Parent node for new nodes
     * @var eZContentObjectTreeNode
     */
    protected $_parentNode;

    /**
     * Starting row
     *
     * Basically the same as offset, but should be used by handler classes to
     * store a general start row for the data definition while offset should be
     * used on the command line while importing.
     *
     * @var int
     */
    protected $_startRow = 0;

    /**
     * Offset from the starting row
     * @var int
     */
    protected $_offset = 0;

    /**
     * Import limit
     * @var int
     */
    protected $_limit = 0;

    /**
     * Set the data file location
     *
     * @param string $datafile
     */
    public function setDatafile($datafile)
    {
        if(!(file_exists($datafile) && is_readable($datafile))) {
            throw new HwImport_Exception('Data file is not readable: ' . $datafile);
        }

        $this->_datafile = $datafile;
    }

    /**
     * Get the data file location
     *
     * @return string
     */
    public function getDatafile()
    {
        return $this->_datafile;
    }

    /**
     * Set the CLI object
     *
     * @param eZCli $cli
     */
    public function setCli(eZCli $cli)
    {
        $this->_cli = $cli;
    }

    /**
     * Get the CLI object
     *
     * @return eZCli
     */
    public function getCli()
    {
        return $this->_cli;
    }

    /**
     * Set the importing user
     *
     * @param eZUser $user
     */
    public function setUser(eZUser $user)
    {
        $this->_user = $user;
    }

    /**
     * Get the importing user
     *
     * @return eZUser
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Set the parent node
     *
     * @param eZContentObjectTreeNode $parentNode
     */
    public function setParentNode(eZContentObjectTreeNode $parentNode)
    {
        $this->_parentNode = $parentNode;
    }

    /**
     * Get the parent node
     *
     * @return eZContentObjectTreeNode
     */
    public function getParentNode()
    {
        return $this->_parentNode;
    }

    /**
     * Set the starting row
     *
     * @param  int $startRow
     * @throws HwImport_Exception
     */
    public function setStartRow($startRow)
    {
        $startRow = (int) $startRow;
        if($startRow <= 0) {
            throw new HwImport_Exception('Invalid start row.');
        }

        $this->_startRow = $startRow;
    }

    /**
     * Get the starting row
     *
     * @return int
     */
    public function getStartRow()
    {
        return $this->_startRow;
    }

    /**
     * Offset from the starting row.
     *
     * @param  int $offset
     * @throws HwImport_Exception
     */
    public function setOffset($offset)
    {
        $offset = (int) $offset;
        if($offset < 0) {
            throw new HwImport_Exception('Invalid offset.');
        }

        $this->_offset = $offset;
    }

    /**
     * Get the offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * Limit import to $limit records
     *
     * @param  int $limit
     * @throws HwImport_Exception
     */
    public function setLimit($limit)
    {
        $limit = (int) $limit;
        if($limit < 0) {
            throw new HwImport_Exception('Invalid limit.');
        }

        $this->_limit = $limit;
    }

    /**
     * Get the limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * Get the used content class identifier for new nodes
     * @return string
     */
    public function getContentClassIdentifier()
    {
        return $this->_contentClassIdentifier;
    }

    /**
     * Get an instance of the used content class
     *
     * @return eZContentClass
     */
    public function getContentClass()
    {
        $contentClass = eZContentClass::fetchByIdentifier($this->getContentClassIdentifier());
        if(!$contentClass instanceof eZContentClass) {
            throw new HwImport_Exception('Invalid content class: ' . $this->getContentClassIdentifier());
        }

        return $contentClass;
    }

    /**
     * Do the import (to be overwritten by handler implementations)
     *
     * @return void
     */
    public function run()
    {
        if($this->getDatafile() === null) {
            throw new HwImport_Exception('Missing data file');
        }

        if($this->getCli() === null) {
            throw new HwImport_Exception('Missing CLI');
        }

        if($this->getUser() === null) {
            throw new HwImport_Exception('Missing user');
        }

        if($this->getParentNode() === null) {
            throw new HwImport_Exception('Missing parent node');
        }

        if($this->getContentClassIdentifier() === null) {
            throw new HwImport_Exception('Missing content class identifier');
        }
    }

    /**
     * Generate the base record data (creator_id, class_identifier, ...)
     *
     * @return array
     */
    protected function _generateBaseRecord()
    {
        return array(
            'class_identifier'  => $this->getContentClassIdentifier(),
            'creator_id'        => $this->getUser()->attribute('contentobject_id'),
            'parent_node_id'    => $this->getParentNode()->attribute('node_id'),
            'section_id'        => $this->getParentNode()->attribute('object')->attribute('section_id')
        );
    }
}
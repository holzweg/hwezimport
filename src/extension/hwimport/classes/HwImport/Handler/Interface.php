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
 * HwImport handler interface
 *
 * @category   HwImport
 * @package    HwImport
 * @author     Mathias Geat <mathias.geat@holzweg.com>
 * @copyright  2011 holzweg e-commerce solutions - http://www.holzweg.com
 * @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL v3
 */
interface HwImport_Handler_Interface
{
    /**
     * Set the data file location
     *
     * @param string $datafile
     */
    public function setDatafile($datafile);

    /**
     * Get the data file location
     *
     * @return string
     */
    public function getDatafile();

    /**
     * Set the CLI object
     *
     * @param eZCli $cli
     */
    public function setCli(eZCli $cli);

    /**
     * Get the CLI object
     *
     * @return eZCli
     */
    public function getCli();

    /**
     * Set the importing user
     *
     * @param eZUser $user
     */
    public function setUser(eZUser $user);

    /**
     * Get the importing user
     *
     * @return eZUser
     */
    public function getUser();

    /**
     * Set the parent node
     *
     * @param eZContentObjectTreeNode $parentNode
     */
    public function setParentNode(eZContentObjectTreeNode $parentNode);

    /**
     * Get the parent node
     *
     * @return eZContentObjectTreeNode
     */
    public function getParentNode();

    /**
     * Set the starting row
     *
     * @param  int $startRow
     * @throws HwImport_Exception
     */
    public function setStartRow($startRow);

    /**
     * Get the starting row
     *
     * @return int
     */
    public function getStartRow();

    /**
     * Set the offset from the starting row.
     *
     * @param  int $offset
     * @throws HwImport_Exception
     */
    public function setOffset($offset);

    /**
     * Get the offset
     *
     * @return int
     */
    public function getOffset();

    /**
     * Set the limit
     *
     * @param  int $limit
     * @throws HwImport_Exception
     */
    public function setLimit($limit);

    /**
     * Get the limit
     *
     * @return int
     */
    public function getLimit();

    /**
     * Get an instance of the used content class
     *
     * @return eZContentClass
     * @throws HwImport_Exception
     */
    public function getContentClass();

    /**
     * Get the used content class identifier for new nodes
     * @return string
     */
    public function getContentClassIdentifier();

    /**
     * Do the import
     *
     * @return void
     * @throws HwImport_Exception
     */
    public function run();
}
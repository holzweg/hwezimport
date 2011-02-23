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
 * HwImport factory
 *
 * @category   HwImport
 * @package    HwImport
 * @author     Mathias Geat <mathias.geat@holzweg.com>
 * @copyright  2011 holzweg e-commerce solutions - http://www.holzweg.com
 * @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL v3
 */
class HwImport
{
    /**
     * Did we already scan for handlers?
     * @var bool
     */
    protected static $_initialized = false;

    /**
     * Registered handlers
     * @var array
     */
    protected static $_handlers = array();

    /**
     * Get a handler instance based on the identifier
     *
     * @param  string $handler
     * @return HwImport_Handler_Interface
     */
    public static function factory($handler)
    {
        if(!self::$_initialized) {
            self::_initialize();
        }

        if(isset(self::$_handlers[$handler])) {
            $class = self::$_handlers[$handler];
            return new $class();
        } else {
            throw new HwImport_Exception('Handler ' . $handler . ' is not defined');
        }
    }

    /**
     * Scan for handler classes in defined directories
     *
     * @return void
     */
    protected static function _initialize()
    {
        $ini = eZINI::instance('hwimport.ini');
        $handlerDirs = $ini->variable('Handlers', 'HandlerDirs');

        require_once dirname(__FILE__) . '/HwImport/Handler/Interface.php';
        require_once dirname(__FILE__) . '/HwImport/Handler/Abstract.php';

        $baseDir = dirname(__FILE__) . '/../../../';
        foreach($handlerDirs as $handlerDir) {
            $dir = realpath($baseDir . $handlerDir);
            $list = scandir($dir);
            foreach($list as $file) {
                if($file != 'Interface.php' && $file != 'Abstract.php') {
                    if(substr($file, strlen($file) - 4) == '.php') {
                        require_once $dir . '/' . $file;
                    }
                }
            }
        }


        self::$_initialized = true;
    }

    /**
     * Register a new handler
     *
     * @param string $identifier
     * @param string $className
     */
    public static function registerHandler($identifier, $className)
    {
        $reflector = new ReflectionClass($className);
        if($reflector->isSubclassOf('HwImport_Handler_Interface')) {
            self::$_handlers[$identifier] = $className;
        }
    }

    /**
     * Static class needs no constructor
     */
    private final function __construct() {}

    /**
     * Static class can't be cloned
     */
    private final function __clone() {}
}

/**
 * HwImport Exception
 *
 * @category   HwImport
 * @package    HwImport
 * @uses       Exception
 * @author     Mathias Geat <mathias.geat@holzweg.com>
 * @copyright  2011 holzweg e-commerce solutions - http://www.holzweg.com
 * @license    http://www.gnu.org/licenses/gpl.txt  GNU GPL v3
 */
class HwImport_Exception extends Exception {}
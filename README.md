HwImport
========

HwImport is a generic handler-based import framework for eZPublish (tested on 4.3.0).

Installation
------------

Drop <code>extension/hwimport</code> to your extension directory and <code>bin/php/hwimport.php</code> to your <code>bin/php</code> directory. Then activate the extension for your site. E.g. in <code>settings/override/site.ini.append.php</code>:

    ActiveExtensions[]=hwimport

Usage
-----

    php bin/php/hwimport.php --handler=MyCustomHandler --data=/tmp/importdata.xlsx --parentnode=1234 --userid=15

    Options:
      --handler=VALUE     Handler identifier
      --data=VALUE        Data file
      --parentnode=VALUE  Parent node ID
      --userid=VALUE      ID of importing user


Writing handlers
----------------

Handlers are responsible for parsing the input data and mapping the data to an eZPublish content object. See the examples directory for an extension containing a sample handler which imports data from an Excel 2007 file.

Basic workflow is the following:

1. Write a handler implementing <code>HwImport_Handler_Interface</code> or extending <code>HwImport_Handler_Abstract</code> which is registering itself to <code>HwImport::register()</code> (see example).
2. Add your handler directory to <code>HandlerDirs[]</code> in a <code>hwimport.append.ini.php</code> file (e.g. in <code>settings/override</code> or in your custom extension).
3. Call the CLI script and pass your handler's identifier to the <code>--handler</code> option.
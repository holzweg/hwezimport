<?php
set_time_limit(0);
require_once 'autoload.php';
$cli = eZCLI::instance();
$db = eZDB::instance();

$script = eZScript::instance(array('description' => ("HwImport Universal data import\n" .
                                                     "Allows handler-based import of data\n" .
                                                     "\n" .
                                                     "(c) 2011 holzweg e-commerce solutions\n" .
                                                     "http://www.holzweg.com\n\n" .
                                                     "./bin/php/hwimport.php --handler=MyCustomHandler --data=/tmp/data.xlsx --parentnode=1234 --userid=15 --limit=10"),
                                     'use-session' => false,
                                     'use-modules' => true,
                                     'use-extensions' => true,
                                     'debug-output' => false,
                                     'debug-message' => false));

$script->startup();

$options = $script->getOptions('[handler:][data:][parentnode:][userid:][offset:][limit:]', '',
                                array( 'handler' => 'Handler identifier',
                                       'data' => 'Data file',
                                       'parentnode' => 'Parent node ID',
                                       'userid' => 'ID of importing user',
                                       'offset' => 'Offset from the starting row',
                                       'limit' => 'Limit number of imported records'));

$script->initialize();

if(empty($options['handler'])) {
    $cli->error("Missing handler option");
    $script->shutdown(1);
}

if(empty($options['data'])) {
    $cli->error("Missing data option");
    $script->shutdown(1);
}

if(empty($options['parentnode'])) {
    $cli->error("Missing parentnode option");
    $script->shutdown(1);
}

if(empty($options['userid'])) {
    $cli->error("Missing user option");
    $script->shutdown(1);
}

if(empty($options['offset'])) {
    $offset = 0;
} else {
    $offset = (int) $options['offset'];
}

if(empty($options['limit'])) {
    $limit = 0;
} else {
    $limit = (int) $options['limit'];
}

/** -------------------------------------------------------------------------- */

// Get user
$user = eZUser::fetch($options['userid']);
if(!$user) {
    $cli->error("Invalid user");
    $script->shutdown(1);
}

// Fetch parent node
$parentNode = eZContentObjectTreeNode::fetch($options['parentnode']);
if(!$parentNode instanceof eZContentObjectTreeNode) {
    $cli->error('Parent node not found');
    $script->shutdown(1);
}

// Init handler
if(substr($options['data'], 0, 1) == '/') {
    $datafile = $options['data'];
} else {
    $datafile = realpath(dirname(__FILE__) . '/../../' . $options['data']);
}

try {
    $handler = HwImport::factory($options['handler']);
    $handler->setDatafile($datafile);
    $handler->setCli($cli);
    $handler->setUser($user);
    $handler->setParentNode($parentNode);
    $handler->setOffset($offset);
    $handler->setLimit($limit);
    $handler->run();
} catch(HwImport_Exception $e) {
    $cli->error($e->getMessage());
    $script->shutdown(1);
}

$script->shutdown();
<?php

$FunctionList = array();

$FunctionList['list'] = array( 'name' => 'list',
                               'operation_types' => 'read',
                               'call_method' => array( 'include_file' => 'extension/ezprojects/modules/feeds/ezrssfunctioncollection.php',
                                                       'class' => 'eZFeedsFunctionCollection',
                                                       'method' => 'fetchList' ),
                               'parameter_type' => 'standard',
                               'parameters' => array() );

$FunctionList['subtree_list'] = array( 'name' => 'subtree_list',
                                       'operation_types' => 'read',
                                       'call_method' => array( 'include_file' => 'extension/ezprojects/modules/feeds/ezrssfunctioncollection.php',
                                                               'class' => 'eZFeedsFunctionCollection',
                                                               'method' => 'fetchSubtreeList' ),
                                       'parameter_type' => 'standard',
                                       'parameters' => array( array( 'name' => 'node_id',
                                                                     'type' => 'integer',
                                                                     'required' => true ),
                                                              array( 'name' => 'max_depth',
                                                                     'type' => 'integer',
                                                                     'required' => false ) ) );

?>

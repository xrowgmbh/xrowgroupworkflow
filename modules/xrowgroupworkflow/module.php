<?php

$Module = array('name' => 'xrow Groupworkflow');

$ViewList = array();
$ViewList['view'] = array( 'script' => 'view.php',
                           'functions' => array( 'view' ),
                           'default_navigation_part' => 'ezxgwnavigationpart',
                           'params' => array( 'GroupID' ) );

$FunctionList = array();
$FunctionList['view'] = array();
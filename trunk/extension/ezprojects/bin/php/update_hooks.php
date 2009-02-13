#!/usr/bin/env php
<?php

require 'autoload.php';

$cli = eZCLI::instance();

$scriptSettings = array();
$scriptSettings['description'] = 'your description of this script comes here';
$scriptSettings['use-session'] = true;
$scriptSettings['use-modules'] = true;
$scriptSettings['use-extensions'] = true;

$script = eZScript::instance( $scriptSettings );
$script->startup();

$config = '';
$argumentConfig = '';
$optionHelp = false;
$arguments = false;
$useStandardOptions = true;

$options = $script->getOptions( $config, $argumentConfig, $optionHelp, $arguments, $useStandardOptions );
$script->initialize();

$projectsIni = eZINI::instance( 'ezprojects.ini' );

$parentPath = $projectsIni->variable( 'Subversion', 'ParentPath' );

$hooks = $projectsIni->hasVariable( 'Subversion', 'Hooks' ) ? $projectsIni->variable( 'Subversion', 'Hooks' ) : array();
$hooksDir = eZDir::path( array( eZSys::rootDir(), 'extension', 'ezprojects', 'svn_hooks' ) );

$dir = new DirectoryIterator( $parentPath );
foreach ( $dir as $fileInfo )
{
    if ( !in_array( $fileInfo->getFileName(), array( '.', '..' ) ) && $fileInfo->isDir() )
    {
        $cli->output( $fileInfo->getFileName() );
        foreach ( $hooks as $hook )
        {
            $hookPath = eZDir::path( array( $hooksDir, $hook ) );
            copy( $hookPath, $fileInfo->getPathname() . '/hooks/' . $hook );
        }
    }
}

$script->shutdown( 0 );

?>
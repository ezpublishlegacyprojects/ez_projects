<?php

if ( !$isQuiet )
{
    $cli->output( "Processing pending Github commit log imports" );
}


// Identify the projects exposing a github.com URL in the "External URL" field :
$params = array( 'Limitation'       => array(),
                 'ClassFilterType'  => 'include',
                 'ClassFilterArray' => array( 'project' ),
                 'AttributeFilter'  => array( 'project/external_url', 'like', '*github.com*' )
);

$githubProjects = eZContentObjectTreeNode::subTreeByNodeID( $params, 2 );

foreach ( $githubProjects as $p )
{
    echo $p->attribute( 'name' ) . "\n";
}
?>

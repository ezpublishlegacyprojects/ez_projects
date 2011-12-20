<?php

if ( !$isQuiet )
{
    $cli->output( "Processing pending Github commit log imports" );
}


// Identify the projects exposing a github.com URL in the "External URL" field :
$params = array( 'Limitation'       => array(),
                 'ClassFilterType'  => 'include',
                 'ClassFilterArray' => array( 'project' ),
                 'AttributeFilter'  => array( array( 'project/external_url', 'like', '*github.com*' ) )
);

$githubProjects = eZContentObjectTreeNode::subTreeByNodeID( $params, 2 );


foreach ( $githubProjects as $githubProject )
{
    // spot the "Source" node
    $params = array( 'Limitation'       => array(),
                     'ClassFilterType'  => 'include',
                     'ClassFilterArray' => array( 'subversion' )
    );

    $sourceNode = eZContentObjectTreeNode::subTreeByNodeID( $params, $githubProject->attribute( 'node_id' ) );

    if ( $sourceNode == null and !$isQuiet )
    {
        $errorMessage = 'Unable to find Source node for project "' . $githubProject->attribute( 'name' )  .'"';
        $cli->error( $errorMessage . "\n" );
        eZDebug::writeError( $errorMessage , __METHOD__ );
        continue;
    }

    // Find the latest update :
    $params = array( 'Limitation'       => array(),
                     'ClassFilterType'  => 'include',
                     'ClassFilterArray' => array( 'subversion_log_message' ),
                     'SortBy'           => array( 'modified', false ),
                     'Limit'            => 1
    );

    $latestCommit = eZContentObjectTreeNode::subTreeByNodeID( $params, $sourceNode->attribute( 'node_id' ) );
    if ( $latestCommit !== null )
    {
        $latestCommitTime = $latestCommit[0]->attribute( 'published' );
    }
    else
        $latestCommitTime = null;


    // Start retrieval of the commit log
    try
    {
        $dm = $githubProject->attribute( 'data_map');
        $url = new githubFeedUrl( $dm['external_url']->attribute( 'content' ) );
        $consumer = new githubFeedConsumer( $url );
        $commitLog = $consumer->getCommitLog( $latestCommitTime );
    }
    catch ( Exception $e)
    {
        $cli->error( $e->getMessage() . "\n" );
        eZDebug::writeError( $e->getMessage(), __METHOD__ );
        continue;
    }

    // Transform commit log into content objects
var_dump( $commitLog );

}




?>

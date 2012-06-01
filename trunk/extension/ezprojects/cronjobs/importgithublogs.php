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
    $cli->output( "== Project : {$githubProject->attribute( 'name' )}" );

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

    // Find the latest update, used to filter out the already imported commits :
    $params = array( 'Limitation'       => array(),
                     'ClassFilterType'  => 'include',
                     'ClassFilterArray' => array( 'subversion_log_message' ),
                     'SortBy'           => array( 'modified', false ),
                     'Limit'            => 1
    );

    $latestCommit = eZContentObjectTreeNode::subTreeByNodeID( $params, $sourceNode[0]->attribute( 'node_id' ) );
    if ( $latestCommit !== null and
         !empty( $latestCommit ) and
         isset( $latestCommit[0] )
       )
    {
        $latestCommitTime = $latestCommit[0]->attribute( 'object' )->attribute( 'published' );
    }
    else
        $latestCommitTime = null;


    // Start retrieval of the commit log
    try
    {
        $dm = $githubProject->attribute( 'data_map' );
        $url = new githubFeedUrl( $dm['external_url']->attribute( 'content' ) );
        $consumer = new githubFeedConsumer( $url );
        $commitLog = $consumer->getCommitLog( $latestCommitTime );
    }
    catch ( Exception $e)
    {
        $cli->error( $e->getMessage() );
        eZDebug::writeError( $e->getMessage(), __METHOD__ );
        continue;
    }

    // Transform commit log into content objects
    foreach ( $commitLog as $commit )
    {
        $attributes = array(
        				'revision'      => $commit['commitSHA'],
                        'log'           => $commit['commitMessage'],
                        'date'          => $commit['published'],
                        'github_author' => $commit['author'],
                           );

        $createParams = array(
                        'attributes'       => $attributes,
                        'parent_node_id'   => $sourceNode[0]->attribute( 'node_id' ),
                        'creator_id'       => 14,
                        'class_identifier' => 'subversion_log_message',
                        'remote_id'        => $commit['id']
                             );

        // Check whether the commit was not already imported.
        // @note : added after the first months of trial of this RSS-based import : a duplicate entry
        // cause the import to fail from time to time ( Fatal Error ), halting the import.
        $existingCommitObjects = eZContentObject::fetchByRemoteID( $createParams['remote_id'], false );
        if ( !empty( $existingCommitObjects ) )
        {
            $message = "Skipping : commit already imported, found in eZ Publish's content base with the following remote ID : {$createParams['remote_id']}.";
            $cli->output( $message );
            continue;
        }

        try
        {
            $githubCommitObject = eZContentFunctions::createAndPublishObject( $createParams );
            $message = "Successfully imported commit {$commit['commitSHA']} in project '{$githubProject->attribute( 'name' )}'.";
            $cli->output( $message );
        }
        catch ( Exception $e)
        {
            $message = "Exception when importing commit {$commit['commitSHA']} in project '{$githubProject->attribute( 'name' )}' : {$e->getMessage()}.";
            $cli->output( $message );
        }
    }
    $cli->output( "\n" );
}

if ( !$isQuiet )
{
    $cli->output( "Finished importing pending Github commit log." );
}
?>
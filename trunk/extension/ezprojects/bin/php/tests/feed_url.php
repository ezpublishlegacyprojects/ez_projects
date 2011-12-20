<?php

// require './extension/ezprojects/classes/github_feed_url.php';

try
{
    // $feedUrl = new githubFeedUrl( "https://github.com/ezsystems/ezpublish" );
    $feedUrl = new githubFeedUrl( "http://github.com/ezsystems/ezpublish/blah.atom" );
    var_dump( $feedUrl->url );
}
catch ( Exception $e)
{
    var_dump( $e->getMessage() );
}





?>
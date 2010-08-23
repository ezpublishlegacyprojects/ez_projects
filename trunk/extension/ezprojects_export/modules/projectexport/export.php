<?php
//
// Created on: <19-Aug-2010 00:00:00 nfrp>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: projects module for eZ Publish
// SOFTWARE RELEASE: 1.x
// COPYRIGHT NOTICE: Copyright (C) 2010 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   GNU General Public License for more details.
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

$projectUnixName = $Params['projectUnixName'];
$withLogins = (bool) $Params['withLogins'];

if ( !$projectUnixName or $projectUnixName == '' )
{
    // No valid pointer given
    eZDB::checkTransactionCounter();
    eZExecution::cleanExit();
}

$projectUnixName = trim( $projectUnixName, '/' );

$project = eZContentObjectTreeNode::fetchByURLPath( $projectUnixName );
if ( !$project )
{
    // unexisting project
    eZDB::checkTransactionCounter();
    eZExecution::cleanExit();
}

header( 'Content-type: text/xml' );

$bootString = <<<XML
<?xml version='1.0' standalone='yes'?>
<project>
</project>
XML;

$xml = new SimpleXMLElement( $bootString );
$xml->addAttribute( 'name', $project->attribute( 'name' ) );

// Export description
$dm = $project->attribute( 'data_map' );
$descriptionXml = $xml->addChild( 'description', strip_tags( $dm['brief']->toString() ) );

// Fetch leaders :
$leadersGroup = eZContentObjectTreeNode::fetchByURLPath(  $projectUnixName . '/team/leaders' );
$leadersGroupXml = $xml->addChild( 'leaders' );

if ( $leadersGroup )
{
    $leaders = $leadersGroup->attribute( 'children' );
    foreach ( $leaders as $l )
    {
        $leaderXml = $leadersGroupXml->addChild( 'user', $l->attribute( 'name' ) );
        if ( $withLogins )
            $leaderXml->addAttribute( 'login', eZUser::fetch( $l->attribute( 'object' )->attribute( 'id' ) )->attribute( 'login' ) );
    }
}

// Fetch members :
$membersGroup = eZContentObjectTreeNode::fetchByURLPath(  $projectUnixName . '/team/members' );
$membersGroupXml = $xml->addChild( 'members' );

if ( $membersGroup )
{
    $members = $membersGroup->attribute( 'children' );
    foreach ( $members as $m )
    {
        $memberXml = $membersGroupXml->addChild( 'user', $m->attribute( 'name' ) );
        if ( $withLogins )
            $memberXml->addAttribute( 'login', eZUser::fetch( $m->attribute( 'object' )->attribute( 'id' ) )->attribute( 'login' ) );
    }
}

// Fetch latest forum activity
$forumsNode = eZContentObjectTreeNode::fetchByURLPath(  $projectUnixName . '/forum', false );
if ( $forumsNode )
{
    $latestForumActivityXml = $xml->addChild( 'latestForumActivity' );
    $params = array( 'Limit'  => 1,
                     'SortBy' => array( 'published', false ) );

    $latestForumMessage = eZContentObjectTreeNode::subTreeByNodeID( $params, $forumsNode['node_id'] );
    if ( $latestForumMessage[0] )
    {
        $forumMessage = $latestForumMessage[0];
        $dm = $forumMessage->attribute( 'data_map' );

        $forumMessageXml = $latestForumActivityXml->addChild( 'forumMessage' );

        // title
        $content = $dm['title'] ? $dm['title']->attribute( 'content' ) : '' ;
        $forumMessageXml->addChild( 'title',  $content  );

        // message
        $content = $dm['message'] ? $dm['message']->attribute( 'content' ) : '' ;
        $forumMessageXml->addChild( 'message',  $content  );

        // author
        $author = $forumMessage->attribute( 'object' )->attribute( 'owner' );
        if ( $withLogins )
            $forumMessageXml->addChild( 'author', $author->attribute( 'name' )  )->addAttribute( 'login', eZUser::fetch( $author->attribute( 'id' ) )->attribute( 'login' ) );
        else
            $forumMessageXml->addChild( 'author', $author->attribute( 'name' )  );
    }
}


// Fetch latest review & overall review mark
$reviewsNode = eZContentObjectTreeNode::fetchByURLPath(  $projectUnixName . '/reviews', false );
if ( $reviewsNode )
{
    $reviewsXml = $xml->addChild( 'reviews' );
    $params = array( 'SortBy' => array( 'published', false ) );

    $allReviews = eZContentObjectTreeNode::subTreeByNodeID( $params, $reviewsNode['node_id'] );
    if ( $allReviews )
    {
        // extract global mark
        $sum = 0;
        for ( $i = 0; $i < count( $allReviews ); $i++ )
        {
            $r = $allReviews[$i];
            $dm = $r->attribute( 'data_map' );
            $sum += $dm['rating']->attribute( 'content' );
        }
        $avgMark = $sum / $i;
        $reviewsXml->addChild( 'average', $avgMark );

        //extract latest review
        $latestActivityXml = $reviewsXml->addChild( 'latestReviewActivity' );
        $dm = $allReviews[0]->attribute( 'data_map' );
        $reviewXml = $latestActivityXml->addChild( 'review' );
        $reviewXml->addChild( 'summary', $dm['summary']->attribute( 'content' ) );
        $reviewXml->addChild( 'rating', $dm['rating']->attribute( 'content' ) );
        $reviewXml->addChild( 'body', $dm['review']->attribute( 'content' ) );
    }
}


echo $xml->asXML();
eZDB::checkTransactionCounter();
eZExecution::cleanExit();

?>
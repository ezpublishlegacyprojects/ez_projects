<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ publish projects extension
// SOFTWARE RELEASE: 0.x
// COPYRIGHT NOTICE: Copyright (C) 2006-2011 Nicolas Pastorino <nfrp@ez.no>
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

// require 'Base/src/ezc_bootstrap.php';


class githubFeedConsumer
{
    protected $feedUrl;

    protected $parsedFeed;

    protected $commitLog = array();

    public function __construct( githubFeedUrl $feedUrl )
    {
        $this->feedUrl = $feedUrl;
        $this->parsedFeed = ezcFeed::parse( $this->feedUrl->url );
    }

    public function getCommitLog( $sinceTimestamp = null )
    {
        $commitLog = array();

        foreach ( $this->parsedFeed->item as $item )
        {
            $itemTimestamp = $item->updated->date->getTimestamp();

            if ( isset( $sinceTimestamp ) and $itemTimestamp < $sinceTimestamp )
                break;

            // Extract commit SHA and ID
            $commitUri = $item->link[0]->href;
            $commitSHA = substr( $commitUri, strlen( $this->feedUrl->commitLogBaseUrl ) -1 );
            $author = $item->author[0]->name;
            $author .= isset( $item->author[0]->uri ) ? " ( " . $item->author[0]->uri . " )" : "" ;

            $commitLog[] =  array(
                'published'     => $item->updated->date->getTimestamp(),
                'author'        => $author,
                'id'            => $commitUri,
                'commitSHA'     => $commitSHA,
                'commitMessage' => $item->content->text
            );
        }

        return $commitLog;
    }
}











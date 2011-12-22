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


class githubFeedUrl
{
    const FEED_URL_PREFIX = 'https://github.com/';
    const FEED_URL_SUFFIX = '/commits/master.atom';
    const COMMIT_LOG_URL_SUFFIX = '/commits/';

    protected $candidateUrl = null;
    public $url;
    public $commitLogBaseUrl;

    public function __construct( $candidateUrl )
    {
        $this->candidateUrl = (string) $candidateUrl;
        $this->formFeedUrl();
    }

    protected function formFeedUrl()
    {
        if ( $this->candidateUrl === null  )
            throw new Exception( "No candidate URL passed" );

        // The URL passed is not a github one
        if ( strpos( $this->candidateUrl, "https://github.com/" ) !== 0 and
             strpos( $this->candidateUrl, "http://github.com/" )  !== 0
           )
        {
            throw new Exception( "The candidate URL passed is not a github one" );
        }

        // Are there an account and a repository name ?
        $pattern = '/^https?\:\/\/github\.com\/([a-zA-Z0-9-\_]+)\/([a-zA-Z0-9-\_]+)/i';
        preg_match( $pattern, $this->candidateUrl, $matches );

        if ( isset( $matches[1] ) )
        {
            $account = $matches[1];
        }
        else
            throw new Exception( "Malformed candidate URL. It is a github one, but no account name is present." );

        if ( isset( $matches[2] ) )
        {
            $repository = $matches[2];
        }
        else
            throw new Exception( "Malformed candidate URL. It is a github one, but no repository name is present." );

        $this->url = static::FEED_URL_PREFIX . $account . "/" . $repository . static::FEED_URL_SUFFIX;
        $this->commitLogBaseUrl = static::FEED_URL_PREFIX . $account . "/" . $repository . static::COMMIT_LOG_URL_SUFFIX;
    }
}











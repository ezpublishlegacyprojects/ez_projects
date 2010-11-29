<?php

class eZFeedsFunctionCollection
{
    /*!
     Constructor
    */
    function eZFeedsFunctionCollection()
    {
    }

    function fetchList()
    {
        $result = array( 'result' => eZRSSExport::fetchList() );
        return $result;
    }

    function fetchSubtreeList( $nodeID, $maxDepth = false )
    {
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        $pathString = $node->attribute( 'path_string' );

        $subQuery = "SELECT i.rssexport_id FROM ezrss_export_item i, ezcontentobject_tree n
                    WHERE
                        i.rssexport_id=e.id AND
                        i.source_node_id=n.node_id AND
                        n.path_string LIKE '$pathString%'";

        if ( is_numeric( $maxDepth ) )
        {
            $nodeDepth = $node->attribute( 'depth' );
            $maxQueryDepth = $nodeDepth + $maxDepth;
        }

        $subQuery .= " AND n.depth <= $maxQueryDepth";

        $query = "SELECT * FROM ezrss_export e
                WHERE EXISTS( $subQuery ) ORDER BY title";

        $db = eZDB::instance();
        $result = $db->arrayQuery( $query );

        $rssExports = array();
        foreach ( $result as $row )
        {
            $rssExports[] = new eZRSSExport( $row );
        }

        return array( 'result' => $rssExports );
    }
}

?>

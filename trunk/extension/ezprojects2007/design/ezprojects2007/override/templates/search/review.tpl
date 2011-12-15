<tr class="{$sequence}">

<td><a href={concat( $node.parent.url_alias, '#review-', $node.node_id )|ezurl}>{$node.name|shorten(60)|wash}</a></td>
<td>{$node.object.published|l10n( 'shortdatetime' )}</td>
<td>{$node.object.owner.name|wash}</td>
<td>{$node.object.class_name|wash}</td>

</tr>

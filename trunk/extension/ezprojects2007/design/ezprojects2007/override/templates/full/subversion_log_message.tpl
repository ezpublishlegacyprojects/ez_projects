{* Full view for subversion log message *}

{* Start main part *}
<div class="template-design area-main-normal">
<div class="template-module content-view-full">
<div class="template-object class-subversion_log_message">

{* Header *}
<div class="attribute-heading">
    <h1>Revision {$node.name|wash}</h1>
</div>

{def $is_github_log_message=$node.object.remote_id|contains( 'github.com' )}
{if $is_github_log_message}
    {def $diff_url=$node.object.remote_id}                                      
{else}
    {def $diff_url=concat( "http://websvn.projects.ez.no/wsvn/",
                              $node.parent.data_map.unix_name.content,
                              "?",
                              "op=comp",
                              "&",
                              "compare[]=%2F@", $node.data_map.revision.content|dec,
                              "&",
                              "compare[]=%2F@", $node.data_map.revision.content)}

{/if}

<p>Committed on {$node.data_map.date.content.timestamp|l10n( shortdatetime )} by {if $is_github_log_message}{$node.data_map.github_author.content|wash|autolink}{else}{attribute_view_gui attribute=$node.data_map.author}{/if} [<a href="{$diff_url}" {if $is_github_log_message}target="_blank"{/if}>{if $is_github_log_message}Diff{else}WebSVN diff{/if}</a>]</p>

{undef $diff_url}

<div class="attribute-long">
{if $is_github_log_message}
    {$node.object.data_map.log.content}
{else}
    {attribute_view_gui attribute=$node.object.data_map.log}
{/if}
</div>

</div>
</div>
</div>
{* End main part *}


{* Right info *}
<div class="template-design area-sidebar-normal">
<div class="template-module content-view-sidebar">
<div class="template-object class-subversion_log_message">

{if $is_github_log_message}
<div class="attribute-heading">
    <h2 class="bullet">Useful git and Github resources</h2>
</div>

<p>
    New to git and Github.com ? <br /> 
    Check this out first : <a href="http://help.github.com/" target="_blank">http://help.github.com/</a>
</p> 
<p>
    Next step : learn the eZ Publish + github FU : <a href="http://share.ez.no/learn/ez-publish/how-to-contribute-to-ez-publish-using-git">How to contribute to eZ Publish using Git</a>
</p>
{else}
<div class="attribute-heading">
    <h2 class="bullet">Useful Subversion links</h2>
</div>

<ul class="linklist">
    <li><a href="http://subversion.tigris.org/">Subversion homepage</a></li>
    <li><a href="http://svnbook.red-bean.com/">Version Control with Subversion: a free book</a></li>
    <li><a href="http://subversion.tigris.org/links.html#clients">List of links to Subversion GUI clients and plugins</a></li>
</ul>
{/if}

</div>
</div>
</div>

{include uri="design:parts/sidebar_actions.tpl" name="actions" node=$node}

{* Right info end *}

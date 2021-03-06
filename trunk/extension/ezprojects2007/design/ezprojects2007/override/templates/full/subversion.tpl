{* Full view for article *}
{def $is_github_project=$node.parent.object.data_map.external_url.content|isgithubrepo()}

{* Start main part *}
<div class="template-design area-main-normal">
<div class="template-module content-view-full">
<div class="template-object class-subversion {if $is_github_project}fork-me{/if}">

{* Header *}
<div class="attribute-heading">
    <h1>{$node.name|wash}</h1>
</div>

<div class="attribute-long">
{if $is_github_project}
<p>This project is hosted on Github.com.</p>
<p> Fork, clone, make loads of pull-requests, and celebrate here : 
<a href="{$node.parent.object.data_map.external_url.content}" target="_blank">{$node.parent.object.data_map.external_url.content}</a></p>
{elseif $node.object.data_map.repository.has_content}
<p>You can anonymously check out the source code released by this project from its Subversion repository:</p>
<code>svn checkout <a href="{$node.object.data_map.repository.content}">{$node.object.data_map.repository.content}</a></code>
<p>To be able to commit changes to the project's repository, you need to be a member of the project.</p>
{else}
The repository is being initialized. Please visit this page again in a few minutes.
{/if}
</div>

{if $node.object.data_map.repository.has_content}
<div class="attribute-heading">
    <h2>Latest log messages</h2>

    {let $logs=fetch('content', 'list', hash(
            'parent_node_id', $node.node_id,
            'class_filter_type', 'include',
            'class_filter_array', array( 'subversion_log_message' ),
            'sort_by', array( 'attribute', false(), 'subversion_log_message/date' ),
            'limit', 15,
            'offset', 0
    ))}
    <ul>
    {foreach $logs as $log}
    {def $is_github_log_message=$log.object.remote_id|contains( 'github.com' )}
    {if $is_github_log_message}
        {def $diff_url=$log.object.remote_id}                                      
    {else}
        {def $diff_url=concat( "http://websvn.projects.ez.no/wsvn/",
                                  $node.parent.data_map.unix_name.content,
                                  "?",
                                  "op=comp",
                                  "&",
                                  "compare[]=%2F@", $log.data_map.revision.content|dec,
                                  "&",
                                  "compare[]=%2F@", $log.data_map.revision.content)}

    {/if}
    <li><a href={$log.url_alias|ezurl}>{$log.data_map.revision.content|shorten( 5, '' )}</a> on {$log.data_map.date.content.timestamp|l10n( shortdatetime )} by {if $is_github_log_message}{$log.data_map.github_author.content|wash|autolink}{else}{attribute_view_gui attribute=$log.data_map.author}{/if} [<a href="{$diff_url}" {if $is_github_log_message}target="_blank"{/if}>{if $is_github_log_message}Diff{else}WebSVN diff{/if}</a>]</li>
    {undef $diff_url}
    {/foreach}
    </ul>
    {/let}
</div>
{/if}

</div>
</div>
</div>
{* End main part *}


{* Right info *}
<div class="template-design area-sidebar-normal">
<div class="template-module content-view-sidebar">
<div class="template-object class-subversion">
{if $is_github_project}
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

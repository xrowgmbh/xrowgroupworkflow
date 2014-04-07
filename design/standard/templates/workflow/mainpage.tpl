{def $locale = fetch('content', 'locale')}
{ezcss_require(array('jquery-ui.css', 'xrowgroupworkflow.css'))}
{ezscript_require(array('ezjsc::jqueryUI', 'xrowgroupworkflow.js'))}
<h2>{'Groupworkflow'|i18n('extension/xrowgroupworkflow')}</h2>
{if is_set($error)}
    <div class="message-error">{$error}</div>
{/if}
{if is_set($returnstatus)}
    <div class="message-feedback">
        <h2>{$returnstatus}</h2>
    </div>
{/if}
<div class="xrowGroupWorkflowNewGroup">
    <form method="post" action={'xrowgroupworkflow/view'|ezurl()}>
        <h3>{'Add new group'|i18n('extension/xrowgroupworkflow')}</h3>
        <input type="hidden" value="{$locale.http_locale_code|extract(0,2)}" name="xrowGroupWorkflow[locale]" />
        <div class="xrowGroupWorkflowGroupName">
            {'Groupname'|i18n('extension/xrowgroupworkflow')} <input type="text" name="xrowGroupWorkflow[groupname]" value="{if and(is_set($groupData), is_set($groupData.id)|not())}{if is_set($groupData.groupname)}{$groupData.groupname|trim()}{/if}{/if}" />
        </div>
        <div class="xrowGroupWorkflowDatePicker">
            <label for="xrowGroupWorkflowDate">{'Date'|i18n('design/admin/shop/orderview')}</label>
            <input type="text" class="xrowGroupWorkflowDate" name="xrowGroupWorkflow[date]" value="{if and(is_set($groupData), is_set($groupData.id)|not())}{if is_set($groupData.date)}{$groupData.date|trim()}{/if}{/if}" data-locale="{$locale.http_locale_code|extract(0,2)}" />
            <select name="xrowGroupWorkflow[hour]">
            {for 0 to 23 as $counter}
                <option value="{$counter}"{if and(is_set($groupData), is_set($groupData.id)|not())}{if is_set($groupData.hour)}{if $groupData.hour|eq($counter)} selected="selected"{/if}{/if}{/if}>{if $counter|le(9)}0{/if}{$counter}</option>
            {/for}
            </select>:
            <select name="xrowGroupWorkflow[minute]">
            {for 0 to 59 as $counter}
                <option value="{$counter}"{if and(is_set($groupData), is_set($groupData.id)|not())}{if is_set($groupData.minute)}{if $groupData.minute|eq($counter)} selected="selected"{/if}{/if}{/if}>{if $counter|le(9)}0{/if}{$counter}</option>
            {/for}
            </select>
        </div>
        <div class="xrowGroupWorkflowStateBlock">
            <label for="xrowGroupWorkflowState">{'State'|i18n('design/standard/package')}</label>
            <select name="xrowGroupWorkflow[status]">
                <option value="{$statedisabled}">{'Disabled'|i18n( 'design/admin/settings' )}</option>
            {foreach $stategroup.states as $state}
                <option value="{$state.id}"{if and(is_set($groupData), is_set($groupData.id)|not())}{if is_set($groupData.status)}{if $groupData.status|eq($stateItem.id)} selected="selected"{/if}{/if}{/if}>{$state.current_translation.name|wash}</option>
            {/foreach}
            </select>
        </div>
        <div class="xrowGroupWorkflowButtons">
            <input class="button" type="submit" name="xrowGroupWorkflowAddGroup" value="{'Add'|i18n('design/admin2/ajaxuploader')}" />
        </div>
    </form>
</div>
<br />
{if is_set($groups)}
{if $groups|count|gt(0)}
<h3>{'Overview'|i18n('extension/xrowgroupworkflow')}</h3>
<div id="xrowGroupWorkflowOverview">
    <ul id="xrowGroupWorkflowUL">
    {def $groupindex = 1}
    {foreach $groups as $group}
        {def $selectedGroupData = $group
             $data = $selectedGroupData.data
             $startminute = $selectedGroupData.date|datetime( 'custom', '%i' )}
        {if $startminute|begins_with( '0' )}{set $startminute = $startminute|extract( 1 )}{/if}
        {def $date = hash( 'date', $selectedGroupData.date|l10n( 'shortdate' ),
                           'hour', $selectedGroupData.date|datetime( 'custom', '%G' ),
                           'minute', $startminute )}
        {if and(is_set($groupData), is_set($groupData[$group.id]))}
            {def $postGroupData = $groupData[$group.id]}
            {if is_set($error)}
                {if is_set($postGroupData.children)}{def $selectedChildren = $postGroupData.children}{/if}
                {if is_set($postGroupData.groupname)}{def $selectedGroupname = $postGroupData.groupname}{/if}
                {if is_set($postGroupData.date)}
                    {set $date = hash( 'date', $postGroupData.date,
                                       'hour', $postGroupData.hour,
                                       'minute', $postGroupData.minute )}
                {/if}
            {/if}
        {else}
            {if is_set($selectedChildren)}{undef $selectedChildren}{/if}
            {if is_set($selectedGroupname)}{undef $selectedGroupname}{/if}
        {/if}
        <li class="xrowGroupWorkflowULLI">
        <form method="post" action={concat('xrowgroupworkflow/view/', $selectedGroupData.id)|ezurl()}>
            <input type="hidden" value="{$locale.http_locale_code|extract(0,2)}" name="xrowGroupWorkflow[locale]" />
            <input type="hidden" value="{$selectedGroupData.id}" name="xrowGroupWorkflow[id]" />
            <input type="hidden" value="{$selectedGroupData.id}" name="xrowGroupWorkflow[{$selectedGroupData.id}][id]" />
            <div class="xrowGroupWorkflowGroupName">
                {$groupindex}. {'Groupname'|i18n('extension/xrowgroupworkflow')} <input type="text" id="xrowGroupWorkflowGroupName{$selectedGroupData.id}" name="xrowGroupWorkflow[{$selectedGroupData.id}][groupname]" value="{$data.groupname|wash()}" />
            </div>
            <div class="xrowGroupWorkflowDatePicker">
                <label for="xrowGroupWorkflowDate">{'Date'|i18n('design/admin/shop/orderview')}</label>
                <input data-time="{$date.date}" value="{$date.date}" type="text" class="xrowGroupWorkflowDate" id="xrowGroupWorkflowDate{$selectedGroupData.id}" name="xrowGroupWorkflow[{$selectedGroupData.id}][date]" data-locale="{$locale.http_locale_code|extract(0,2)}" />
                <select id="xrowGroupWorkflowDateHour{$selectedGroupData.id}" name="xrowGroupWorkflow[{$selectedGroupData.id}][hour]" onChange="setButtonToSave('{$selectedGroupData.id}')">
                {for 0 to 23 as $counter}
                    <option value="{$counter}"{if $date.hour|eq($counter)} selected="selected"{/if}>{if $counter|le(9)}0{/if}{$counter}</option>
                {/for}
                </select>:
                <select id="xrowGroupWorkflowDateMinute{$selectedGroupData.id}" name="xrowGroupWorkflow[{$selectedGroupData.id}][minute]" onChange="setButtonToSave('{$selectedGroupData.id}')">
                {for 0 to 59 as $counter}
                    <option value="{$counter}"{if $date.minute|eq($counter)} selected="selected"{/if}>{if $counter|le(9)}0{/if}{$counter}</option>
                {/for}
                </select>
            </div>
            <div class="xrowGroupWorkflowStateBlock">
                <label for="xrowGroupWorkflowState">{'State'|i18n('design/standard/package')}</label>
                <select id="xrowGroupWorkflowState{$selectedGroupData.id}" name="xrowGroupWorkflow[{$selectedGroupData.id}][status]" onChange="setButtonToSave('{$selectedGroupData.id}')">
                    <option value="{$statedisabled}"{if $selectedGroupData.status|eq($statedisabled)} selected="selected"{/if}>{'Disabled'|i18n('design/admin/settings')}</option>
                {foreach $stategroup.states as $state}
                    <option value="{$state.id}"{if $selectedGroupData.status|contains($state.id)} selected="selected"{/if}>{$state.current_translation.name|wash}</option>
                {/foreach}
                    <option value="{$statedone}"{if $selectedGroupData.status|eq($statedone)} selected="selected"{/if}>{'Done'|i18n('design/admin/settings')}</option>
                </select>
            </div>
            <div class="xrowGroupWorkflowButtons">
                <input type="submit" class="editFieldButtonCopy" name="xrowGroupWorkflowCopyGroup[{$selectedGroupData.id}]" value="{'Copy group'|i18n('extension/xrowgroupworkflow')}" />
                <input type="submit" class="editFieldButtonCopy" name="xrowGroupWorkflowSetStateGroup[{$selectedGroupData.id}]" value="{'Set now group state'|i18n('extension/xrowgroupworkflow')}" />
                <input type="submit" name="xrowGroupWorkflowRemoveGroup[{$selectedGroupData.id}]" value="{'Remove group'|i18n('extension/xrowgroupworkflow')}" />
            </div>

            {if is_set($selectedGroupData.data.children)}
            <h4>Existing Objects</h4>
            <ul id="xrowGroupWorkflowULChildren{$selectedGroupData.id}" class="xrowGroupWorkflowULChildren">
                {foreach $selectedGroupData.data.children as $children_node_id}
                {def $child = fetch('content', 'node', hash('node_id', $children_node_id))}
                <li>
                    <input type="hidden" name="xrowGroupWorkflow[{$selectedGroupData.id}][children][{$child.node_id}]" value="{$child.node_id}" />
                    <a href={$child.url_alias|ezurl()} class="xrowGroupWorkflowULChildrenLink">{$child.name|wash()}</a> <input type="submit" name="xrowGroupWorkflowRemoveObject[{$selectedGroupData.id}][{$child.node_id}]" value="{'Remove object'|i18n('extension/xrowgroupworkflow')}" />
                </li>
                {undef $child}
                {/foreach}
            </ul>
            {/if}
            {if or(is_set($selected_node_ids[$selectedGroupData.id]), is_set($selectedChildren))}
            <h4>New Objects</h4>
            <ul class="xrowGroupWorkflowULChildren">
                {if is_set($selectedChildren)}{def $group_selected_node_ids = $selectedChildren}{/if}
                {if is_set($selected_node_ids[$selectedGroupData.id])}{def $group_selected_node_ids = $selected_node_ids[$selectedGroupData.id]}{/if}
                {foreach $group_selected_node_ids as $selected_node_id}
                    {if $selectedGroupData.data.children|contains($selected_node_id)|not()}
                    {def $child = fetch('content', 'node', hash('node_id', $selected_node_id))}
                    <li style="overflow: hidden; height: 20px">
                        <input type="hidden" name="xrowGroupWorkflow[{$selectedGroupData.id}][children][{$child.node_id}]" value="{$child.node_id}" />
                        <a href={$child.url_alias|ezurl()} class="xrowGroupWorkflowULChildrenLink">{$child.name|wash()}</a>{* <input type="submit" name="xrowGroupWorkflowRemoveObject[{$selectedGroupData.id}][{$child.node_id}]" value="{'Remove object'|i18n('extension/xrowgroupworkflow')}" />*}
                    </li>
                    {undef $child}
                    {/if}
                {/foreach}
                {undef $group_selected_node_ids}
            </ul>
            {/if}
            <div class="xrowGroupWorkflowBrowseButtonBlock">
                <input class="button" type="submit" name="xrowGroupWorkflowBrowseButton[{$selectedGroupData.id}_browse_related_node]" value="{'Add objects'|i18n('extension/xrowgroupworkflow')}" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input id="xrowGroupWorkflowSave{$selectedGroupData.id}" class="{if is_set($selected_node_ids[$selectedGroupData.id])}defaultbutton{else}button{/if}" type="submit" name="xrowGroupWorkflowSave[{$selectedGroupData.id}]" value="{'Save this groupworkflow'|i18n('extension/xrowgroupworkflow')}" />
            </div>
            </form>
        </li>
        {undef $selectedGroupData $data $startminute $date}
        {set $groupindex = $groupindex|sum(1)}
    {/foreach}
    </ul>
</div>
{/if}
{/if}
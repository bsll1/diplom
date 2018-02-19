{if (isset($voms_delrole_usrdet_updok))}
	<p class="alldone">{$voms_delrole_usrdet_updok}</p>
{/if}
{if (isset($voms_attribute_updok))}
	<p class="alldone">{$voms_attribute_updok}</p>
{/if}
{if (isset($voms_attribute_delok))}
	<p class="alldone">{$voms_attribute_delok}</p>
{/if}
<div id="usrdetcaption" >
	{$information_per_role_per_group}
	<a href="javascript:showhide('usrdet');"><img src="pics/minimize.png" id="usrdetimg" border="0" alt="minimize" /></a>
</div>
{if isset($voms_roleinfo_userdet)}
<div id="usrdet">
	<p>{$voms_roleinfo_userdet}</p>
	<!-- Show search section -->
	<div class="search">
		<form method="post" action="?vo={$vo}&amp;operation=roleinfo&amp;id={$uid}">
			<input class="searchtxt" type="text" name="search"/>
			<select class="searchsel" name="sgrp">
				{$serch_roles_select_options}
			</select>
			<input class="searchbt" type="submit" name="sbutton" value="{$voms_mng_user_search}">
		</form>
	</div><br/>
	{if (isset($voms_no_users))}
		{$voms_no_users}
	{/if}
	{if (isset($voms_no_search))}
		{$voms_no_search}
	{/if}
	{if (isset($table))}
		<table>
			<th>$voms_userinfo_usrdet_dn</th><th></th>
			{foreach from=$users item=vommbe}
				<div class="mmbdn">
					<a href="?vo={$vo}&amp;operation=userinfo&amp;id={$vommbe['id']}">{$vommbe["cn"]}</a>
				</div>
				<div class="mmbca">{$vommbe["ca"]}</div></td>
				<!-- Dissmiss role link for admins -->
				{if isACLallow($groups_pvap[$sgrp],"container","w")}
					// Form with parameters for Role removal; Submittion via link
					<td><form name="rmrid{$vommbe['id']}" method="post" action="">
					<input type="hidden" name="sgrp" value="{$sgrp}">
					<input type="hidde\" name="agrp" value="{$agrp}">
					<input type="hidden" name="rid" value="{$uid}">
					<input type="hidden" name="uid" value="{$vommbe['id']}">
					<input type="hidden" name="ridrm" value=1>
					</form>
					<a href="javascript:document.rmrid{$vommbe['id']}{submit()}">{$voms_dissmiss_role}</a></td>
				{else}  
					<td></td>
				{/if}
			{/foreach}
			</table>
			{$showLimitsCaption}
	{/if}
</div>
{/if}

<!-- Attribute management -->
<div id="attrmcaption" >
	{$voms_attrm_caption}
	<a href="javascript:showhide('attrm');">
		<img src="pics/minimize.png" id="attrmimg" border="0" alt="minimize" />
	</a>
</div>
<div id="attrm">
	{if (isset($voms_attributes_notexists))}
		{$voms_attributes_notexists}
	{/if}
	{if (isset($showaddatribute))}
		{$showaddatribute}
	{/if}
</div>
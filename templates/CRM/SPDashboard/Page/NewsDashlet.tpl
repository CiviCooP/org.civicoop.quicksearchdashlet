	<div id="civicrm-blog-feed">
		{foreach from=$items item=item}
		<div class="crm-accordion-wrapper collapsed">
			<div class="crm-accordion-header">{$item->title}</div>
			<div class="crm-accordion-body help">
				{$item->body.und.0.value}
			</div>
		</div>
		{/foreach}
	</div>
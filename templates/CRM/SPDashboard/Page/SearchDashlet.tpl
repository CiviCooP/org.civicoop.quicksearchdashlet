<form action="{crmURL p='civicrm/contact/search/advanced' h=0 }" name="dashboard_spqs_block" id="id_dashboard_spqs_block" method="post">
	<div id="spqs-form">
		<input type="text" class="form-text spqs-id" placeholder="Lidnummer" name="contact_id" data-tablename="cc">
		<input type="text" class="form-text spqs-postcode" id="sort_name_navigation" placeholder="Postcode" name="postal_code" data-tablename="sts">
		<input type="text" class="form-text spqs-name" id="sort_name_navigation" placeholder="Achternaam" name="sort_name" data-tablename="cc" data-fieldname="last_name">
		<input type="text" class="form-text spqs-email" id="sort_name_navigation" placeholder="E-mail" name="email" data-tablename="eml">
		<input type="hidden" name="hidden_location" value="1" />
		<input type="hidden" name="qfKey" value="{crmKey name='CRM_Contact_Controller_Search' addSequence=1}" />
		<div style="height:1px; overflow:hidden;"><input type="submit" value="{ts}Go{/ts}" name="_qf_Advanced_refresh" class="form-submit default" /></div>
	</div>
</form>
<div id="spqs-results">
</div>
<script type="text/javascript">
	{literal}
	cj(function () {
		var searchUrl = {/literal}"{crmURL p='civicrm/ajax/rest' q='className=CRM_Contact_Page_AJAX&fnName=getContactList&json=1&context=navigation' h=0 }"{literal};

		cj("#spqs-form input[type=text]").each(function (index, element) {
			cj(element).autocomplete(searchUrl, {
				width: 200,
				selectFirst: false,
				minChars: 1,
				matchContains: true,
				delay: 400,
				max: {/literal}{crmSetting name="search_autocomplete_count" group="Search Preferences"}{literal},
				extraParams: {
					fieldName: cj(element).attr('data-fieldname') ? cj(element).attr('data-fieldname') : cj(element).attr('name'),
					tableName: cj(element).attr('data-tablename') ? cj(element).attr('data-tablename') : ''
				}
			}).result(function (event, data, formatted) {
						document.location = CRM.url('civicrm/contact/view', {reset: 1, cid: data[1]});
						return false;
					});
		});
	});
	{/literal}
</script>
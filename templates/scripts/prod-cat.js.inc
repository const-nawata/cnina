{% trans_default_domain 'prompts' %}

	$("div.dataTables_filter label").prepend(
		'<div class="form-check form-check-inline">'+
			'<label class="form-check-label" for="inactive_chkbx">{{ 'title.showInactive'|trans }}</label>'+
			'<input class="form-check-input" type="checkbox" id="inactive_chkbx" name="inactive_chkbx" value="option1" {{ showInactive }} />'+
		'</div>'
	);

	$("form#form-resp").append('<input type="hidden" name="showInactive" id="showInactive" value="{{ showInactive }}" />');

	$("#inactive_chkbx").on('change',function (event) {
		let checked	= $(this).is(":checked") ? "checked" : "";
		$("#searchStr").val('');
		$("#showInactive").val(checked);
		$('form#form-resp').submit();
	});

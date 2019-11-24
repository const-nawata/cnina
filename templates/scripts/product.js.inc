{% trans_default_domain 'prompts' %}

	$("div.dataTables_filter label").prepend(
		'<div class="form-check form-check-inline">'+
			'<label class="form-check-label" for="show_sold">{{ 'title.showsold'|trans }}</label>'+
			'<input class="form-check-input" type="checkbox" id="show_sold" name="show_sold" value="option1" {{ showSold }} />'+
		'</div>'
	);

	$("form#form-resp").append('<input type="hidden" name="showSold" id="showSold" value="{{ showSold }}" />');

	$("#show_sold").on('change',function (event) {
		let checked	= $(this).is(":checked") ? "checked" : "";
		$("#searchStr").val('');
		$("#showSold").val(checked);
		$('form#form-resp').submit();
	});

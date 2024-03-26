//Select 2  Searchbox//
$('.select2auto').select2({
	theme: "bootstrap",
	//minimumInputLength:1,
	//tags: true,
	allowClear: true,
	placeholder: 'Type | Search | Select',
	ajax: {
		delay: 500,
		url: function(){
			return '<?= BASE_URL.'select2searchs/'?>'+$(this).attr("data-route");
		},
		dataType: 'json',
		type: 'POST',
		data: function(params) {
			//console.log($(this).attr("data-route"));
			//return;
			return {
				field_name: $(this).attr("name"), //this field name
				field_val: params.term, //search term
				prVal: $(this).attr("data-parent"), // parrent value search term
				page_limit: $(this).attr("data-limit"), // items limit
				init_id: $(this).attr("data-val"), // initial value
			};
		},
		processResults: function(data, page) {
			return {
				results: data.results
			};
		}
	}
});
$('.select2auto').change(function() {
	var val = $(this).val();
	var elm = $(this).attr("data-child");
	if(elm){
		let str = elm;
		let result = str.replace(/[\[\]]/g, '').split(',');
		$.each(result, function(key, elem) {
			$('#'+elem).attr('data-parent',val);
		});
	}
});
$(document).ready(function(){
	$(".btn-delete").click(function(){
		id = $(this).data("id"); 
		$.ajax({
				type: "DELETE",
				url: "/",
				data: { id: id }
			})
			.done(function( data ) {
				$("#disk-"+id).load("index.php #disk-"+id);
			});	
	})
	
	$('#deleteModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget) 
		var content = button.data('content')
		$(this).find('#del').data("id", content);
	})
	
	$(".btn-record").click(function(){
		id = $(this).data("id");
		$.ajax({
			type: "POST",
			url: "/",
			data: { id: id }
		})
		.done(function( data ) {
			$("#disk-"+id).load("index.php #disk-"+id);
		});
	})
	
	$(".btn-copy").click(function(){
		id = $(this).data("id");
		$.ajax({
			type: "POST",
			url: "/copy",
			data: { id: id }
		})
	})
})


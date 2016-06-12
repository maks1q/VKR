$(document).ready(function(){
	$(".btn-delete").click(function(){
		id = $(this).data("id"); 
		$.ajax({
				type: "DELETE",
				url: "/",
				data: { id: id }
			})
			.done(function( data ) {
				//$("#result").html( '<td colspan=3> <div class="alert alert-success" role="alert"><a href="#" class="close" data-dismiss="alert">×</a>'+data+'</div> </td>' );
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
		.done(function(data){
			$("#disk-"+id).load("index.php")
		});
	})
})

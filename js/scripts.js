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
	
	$(".btn-magaz").click(function(){
		id = $(this).data("id"); 
		$(".btn-magaz").removeClass('btn-magaz');
		$.ajax({
				type: "GET",
				url: "/medved/"+id+"/magaz",
			})
			.done(function( data ) {			
				$("#magaz").append('<thead class="thead-default"><tr><th>#</th><th>Название магазина</th><th>Адрес</th></tr></thead><tbody>');
				for (var mag of data.magaz) {
					$("#magaz").append('<tr><th scope="row">'+mag.pk_magaz+'</th><td>'+mag.name_magaz+'</td><td>'+mag.adres_magaz+'</td></tr>');
				}
				$("#magaz").append('</tbody>');			
			});			
	})
	
	$('#deleteModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget) 
		var content = button.data('content')	
		$(this).find('#del').attr('data-id', content);
	})
})

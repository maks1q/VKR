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
	
	$('#recordModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget) 
		var content = button.data('content')
		$(this).find('#rec').data("id", content);
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
		.done(function( data ) {
			for (var d of data.disk1) {
				$("#disks").append('<tr id="disk-{{d.pk_disk}}" class="well"><td>'+d.name_disk+'</td><td>'+d.type_disk+'</td><td>'+d.status_string_disk+'</td></tr>');	
			//$("#disk-"+id).load("index.php #disk-"+id);
			}
		});
	})
	/*
	$(".btn-copy").click(function(){
		id = $(this).data("id"); 
		//$(".btn-magaz").removeClass('btn-magaz');
		$.ajax({
				type: "GET",
				url: "/copy/"+id,
			})
			.done(function( data ) {			
			for (var d of data.disk1) {
				$("#disks").append('<tr id="disk-{{d.pk_disk}}" class="well"><td>'+d.name_disk+'</td><td>'+d.type_disk+'</td><td>'+d.status_string_disk+'</td></tr>');	
				//$("#disk-"+id).load("index.php #disk-"+id);
			}
				//$("#disks").append('<tr id="disk-{{d.pk_disk}}" class="well"><td>'+disk.name_disk+'</td><td>'+disk.type_disk+'</td><td>'+disk.status_string_disk+'</td></tr>');		
			});			
	})*/
})


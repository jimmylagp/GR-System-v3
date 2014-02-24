$(document).ready(function(){

/*Funciones para la lista de productos*/
	$('#search .type').click(function(){
		$('#search input[type="search"]').val($(this).attr('data-s'));
		$('#search input[type="search"]').focus();

		return false;
	});
/*------------------------------------*/


/*Functiones para administrar los productos*/
	$('#aproducts tr').keydown(function(e) {
		if (e.keyCode == 13) {
			$.post(
				"/index.php/productos/update",
				{
					id: $(this).attr('data-id'),
					amount: $(this).find('.amount').text(),
					name: $(this).find('.name').text(),
					price: $(this).find('.price').text()
				},
				function(result){
					var obj = JSON.parse(result);
					if(obj['error'] == 0){
						alert("Producto actualizado.");
					}else{
						alert("Ocurrio un error al actualizar el producto.");
					}
				}
			);
			
			return false;
		}
	});

	$('#aproducts .delete').click(function(){
		var tr = $(this);
		if(confirm("¿Deseas eliminar este producto?")){
			$.post(
				"/index.php/productos/delete",
				{
					id: $(this).attr('data-id')
				},
				function(result){
					var obj = JSON.parse(result);
					if(obj['error'] == 0){
						tr.closest("tr").remove();
						alert("Producto eliminado.");
					}else{
						alert("Ocurrio un error al eliminar el producto.");
					}
				}
			);
		}

		return false;
	});
/*-----------------------------------------*/

/*Functiones para adrministrar rutas*/
	$('#arutas tr').keydown(function(e) {
		if (e.keyCode == 13) {
			$.post(
				"/index.php/rutas/update",
				{
					id: $(this).attr('data-id'),
					name: $(this).find('.name').text()
				},
				function(result){
					var obj = JSON.parse(result);
					if(obj['error'] == 0){
						alert("Ruta actualizada.");
					}else{
						alert("Ocurrio un error al actualizar la ruta.");
					}
				}
			);
			
			return false;
		}
	});

	$('#arutas .delete').click(function(){
		var tr = $(this);
		if(confirm("¿Deseas eliminar esta ruta?")){
			$.post(
				"/index.php/rutas/delete",
				{
					id: $(this).attr('data-id')
				},
				function(result){
					var obj = JSON.parse(result);
					if(obj['error'] == 0){
						tr.closest("tr").remove();
						alert("Ruta eliminada.");
					}else{
						alert("Ocurrio un error al eliminar la ruta.");
					}
				}
			);
		}

		return false;
	});
/*----------------------------------*/

/*Funciones para Administrar los clientes*/
	$('.crutas').on('change', function(){
		$.post(
			"/index.php/clientes/load",
			{
				cruta: $('.crutas option:selected').attr('value')
			},
			function(result){
				var obj = JSON.parse(result);
				if(obj['error'] == 0)
				{
					$('#aclientes tbody').empty();
					$.each(obj['data'], function(i, item){
						$('#aclientes').prepend('<tr data-id="'+item.id+'"> <td><span class="name" contenteditable="true">'+item.nombre+'</span></td> <td><span class="place" contenteditable="true">'+item.lugar+'</span></td> <td><span class="delete fi-x button postfix" data-id="'+item.id+'"></span></td> </tr>');
					})
				}else
				{
					alert("Ocurrio un error al cargar los clientes.");
				}
			}
		);
	});

	$(document).on("keydown", '#aclientes tr', function(e) {
		if (e.keyCode == 13) {
			$.post(
				"/index.php/clientes/update",
				{
					id: $(this).attr('data-id'),
					name: $(this).find('.name').text(),
					place: $(this).find('.place').text()
				},
				function(result){
					var obj = JSON.parse(result);
					if(obj['error'] == 0){
						alert("Cliente actualizado.");
					}else{
						alert("Ocurrio un error al actualizar el cliente.");
					}
				}
			);
			
			return false;
		}
	});

	$(document).on('click', '#aclientes .delete', function(){
		var tr = $(this);
		if(confirm("¿Deseas eliminar este cliente?")){
			$.post(
				"/index.php/clientes/delete",
				{
					id: $(this).attr('data-id')
				},
				function(result){
					var obj = JSON.parse(result);
					if(obj['error'] == 0){
						tr.closest("tr").remove();
						alert("Cliente eliminado.");
					}else{
						alert("Ocurrio un error al eliminar el cliente.");
					}
				}
			);
		}

		return false;
	});
/*---------------------------------------*/

/*Funciones para pedidos*/
	$('#rutap').on('change', function(){
		$.post(
			"/index.php/clientes/load",
			{
				cruta: $('#rutap option:selected').attr('value')
			},
			function(result){
				var obj = JSON.parse(result);
				if(obj['error'] == 0)
				{
					$('#clientep').empty();
					$.each(obj['data'], function(i, item){
						$('#clientep').prepend('<option value="'+item.id+'">'+item.nombre+'</option>');
					})
				}else
				{
					alert("Ocurrio un error al cargar los clientes.");
				}
			}
		);
	});
/*----------------------*/
});
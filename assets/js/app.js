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
					id: parseInt( $(this).attr('data-id') ),
					amount: parseInt( $(this).find('.amount').text() ),
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
					id: parseInt( $(this).attr('data-id') )
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
					id: parseInt( $(this).attr('data-id') ),
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
					id: parseInt( $(this).attr('data-id') )
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
						$('#aclientes').prepend('<tr data-id="'+item.id+'"> <td><span class="name" contenteditable="true">'+item.nombre+'</span></td> <td><span class="place" contenteditable="true">'+item.lugar+'</span></td> <td><span class="delete fi-trash button postfix" data-id="'+item.id+'"></span></td> </tr>');
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
					id: parseInt( $(this).attr('data-id') ),
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
					id: parseInt( $(this).attr('data-id') )
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

	$('#list-to-add button').click(function(){
		$(this).hide();
		$(this).next().show();
		$(this).next().focus();
	});

	$('#list-to-add input').keydown(function(e){
		var row = $(this);
		var val = parseInt( $(this).val() );
		if (e.keyCode == 13) {
			if(parseInt(row.closest('tr').find('#cant').html()) >= 0 && val % 1 == 0 && val > 0 && parseInt(row.closest('tr').find('#cant').html()) >= val ){
				$.post(
					"/index.php/pedidos/add",
					{
						id_prod: parseInt( row.closest('tr').attr('data-id') ),
						cant: val
					},
					function(result){
						var obj = JSON.parse(result);
						if(obj['error'] == 0)
						{
							row.attr('disabled', 'disabled');
							get_total_pedido();
						}
						else
						{
							alert("Ocurrio un error al agregar el producto al pedido.");
						}
					}
				);

				$.post(
					"/index.php/pedidos/updateproducto",
					{
						id_producto: parseInt( row.closest('tr').attr('data-id') ),
						cantidad: parseInt( row.closest('tr').find('#cant').html() ) - val
					},
					function(result){
						var obj = JSON.parse(result);
						if(obj['error'] == 0)
						{
							row.closest('tr').find('#cant').html( parseInt(row.closest('tr').find('#cant').html()) - val );
						}
						else
						{
							alert("Ocurrio un error al actualizar el stock.");
						}
					}
				);
			}
			else
			{
				alert("No hay suficiente producto.");
			}
		}
	});

	$('#pdescuento').keydown(function(e){
		if (e.keyCode == 13) {
			$.post(
				"/index.php/pedidos/updatedescuento",
				{
					descuento: $(this).html()
				},
				function(result){
					var obj = JSON.parse(result);
					if(obj['error'] == 0){
						get_total_pedido();
						alert("Descuento actualizado.");
					}else{
						alert("Ocurrio un error al actualizar el descuento.");
					}
				}
			);
			
			return false;
		}
	});

	$('.delete_pa').click(function(){
		var tr = $(this);
		var updatecant = 0;
		if(confirm("¿Deseas eliminar este producto del pedido?")){
			$.post(
				"/index.php/pedidos/deletepa",
				{
					id_pa: tr.closest("tr").attr("data-id")
				},
				function(result){
					var obj = JSON.parse(result);
					if(obj['error'] == 0){
						updatecant = parseInt(tr.closest('tr').attr('data-pcantidad')) + parseInt(tr.closest('tr').find('td:first-child').attr("data-cant"));
						$.post(
							"/index.php/pedidos/updateproducto",
							{
								id_producto: parseInt( tr.closest('tr').attr('data-id-producto') ),
								cantidad: updatecant
							},
							function(result){
								var obj = JSON.parse(result);
								if(obj['error'] == 0)
								{
									tr.closest('tr').attr('data-pcantidad', updatecant);
								}
								else
								{
									alert("Ocurrio un error al actualizar el stock.");
								}
							}
						);

						tr.closest("tr").remove();
						get_total_pedido();
					}else{
						alert("Ocurrio un error al eliminar el producto argegado.");
					}
				}
			);
		}
	});

	$('#vpedido .updatecant').keydown(function(e){
		var row = $(this);
		var val = parseInt( $(this).html() );
		var updatecant = 0;
		if (e.keyCode == 13) {
			if( val > 0 ){
				$.post(
					"/index.php/pedidos/updatepa",
					{
						id_pa: parseInt( row.closest('tr').attr('data-id') ),
						cant: val
					},
					function(result){
						var obj = JSON.parse(result);
						if(obj['error'] == 0)
						{
							get_total_pedido();
						}
						else
						{
							alert("Ocurrio un error al actualizar la cantidad.");
						}
					}
				);

				updatecant = ( parseInt(row.closest('tr').attr('data-pcantidad')) + parseInt(row.closest('td').attr("data-cant")) ) - val;
				row.closest('td').attr("data-cant", val);

				$.post(
					"/index.php/pedidos/updateproducto",
					{
						id_producto: parseInt( row.closest('tr').attr('data-id-producto') ),
						cantidad: updatecant
					},
					function(result){
						var obj = JSON.parse(result);
						if(obj['error'] == 0)
						{
							row.closest('tr').attr('data-pcantidad', updatecant);
							alert("Pedido actualizado.");
						}
						else
						{
							alert("Ocurrio un error al actualizar el stock.");
						}
					}
				);
			}
			else
			{
				alert("No hay suficiente producto.");
			}

			return false;
		}
	});

	$('#lista-pedidos .delete').click(function(){
		if(confirm("¿Deseas eliminar este pedido?")){
			var tr = $(this);
			$.post(
				"/index.php/pedidos/delete",
				{
					id_pedido: parseInt( tr.closest("tr").attr("data-id") )
				},
				function(result){
					var obj = JSON.parse(result);
					if(obj['error'] == 0){
						tr.closest("tr").remove();
					}else{
						alert("Ocurrio un error al eliminar el pedido.");
					}
				}
			);
		}
	});

	$('#eliminar').click(function(){
		var b = $(this);
		if(confirm("¿Desea eliminar el pedido?. La nota ya no se podrá recuperar.")){
			$.post(
				"/index.php/pedidos/delete",
				{
					id_pedido: parseInt( b.attr("data-id") )
				},
				function(result){
					var obj = JSON.parse(result);
					if(obj['error'] == 0){
						window.location = "/"
					}else{
						alert("Ocurrio un error al eliminar el pedido.");
					}
				}
			);
		}
	});

	$('#imprimir').click(function(){
		var frm = $('#remision').get(0).contentWindow;
            frm.focus();
            frm.print();

		return false;
	});

	(function($){
		get_total_pedido();
	})(jQuery);

	function get_total_pedido(){
		$.get( "/index.php/pedidos/gettotal", function(data){
			var obj = JSON.parse(data);
			$('#psubtotal').html('$'+numeral(obj['total']).format('0,0.00'));

			var total = parseFloat(obj['total']),
				descuento = parseFloat($('#pdescuento').html());
			total = total - ((descuento/100)*total);

			$('#ptotal').html('$'+numeral(total).format('0,0.00'));
		});
	}
/*----------------------*/
});
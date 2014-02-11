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
/*-----------------------------------------*/
});
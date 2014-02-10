$(document).ready(function(){

/*Funciones para la lista de productos*/
	$('#search .type').click(function(){
		$('#search input[type="search"]').val($(this).attr('data-s'));
		$('#search input[type="search"]').focus();

		return false;
	});
/*------------------------------------*/

});
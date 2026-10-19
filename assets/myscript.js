( function( $ ) {

    $( document ).ready( function() {
		
        $( '#rdv_categorie' ).on( 'change', function() {
        	
        	var ajaxUrl = postdata.ajax_url,
				nonceValue = postdata.ajax_nonce,
				rdv_categorieidvalue = $( '#rdv_categorie' ).val();

			var request = $.post(
				ajaxUrl,   // this url till admin-ajax.php  is given by functions.php wp_localoze_script()
				{
					action: 'service_cat_option_admin',
					security: nonceValue,
					rdv_categorieid: rdv_categorieidvalue
				},
				function( status ){
					console.log( status );  // result {success: true, data: {…}}data: data_recieved_from_js: {action: "my_ajax_hook", name: "Lawrence", profession: "actress"}hello_world: "hello"__proto__: Objectsuccess: true__proto__: Object
				}
			);

			request.done( function ( response ) {
				console.log( 'The server responded: ');
				var productSubCatEl = $( '#rdv_service' );
				productSubCatEl.html( '' );
				productSubCatEl.append( response.data.my_data );
			} );


        });

    });

})(jQuery);
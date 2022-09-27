jQuery( function( $ ) {

	$( '#vicode_file' ).change( function() {

		if ( ! this.files.length ) {
			$( '#vicode_filelist' ).empty();
		} else {

			// ohh yeah baby, give me the first file
			const file = this.files[0];

			$( '#vicode_filelist' ).html( '<img src="' + URL.createObjectURL( file ) + '"><span>' + file.name + '</span>' );

			const formData = new FormData();
			formData.append( 'vicode_file', file );

			$.ajax({
				url: wc_checkout_params.ajax_url + '?action=vicodeupload',
				type: 'POST',
				data: formData,
				contentType: false,
				enctype: 'multipart/form-data',
				processData: false,
				success: function ( response ) {
					$( 'input[name="vicode_file_field"]' ).val( response );
				}
			});

		}

	} );

} );
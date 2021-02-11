<script>
	$(document).ready( function () {
		if($('#table')[0]){
			$('#table')[0].innerHTML = $('#table')[0].innerHTML + '<tfoot>' + $('#table .thead-dark')[0].innerHTML + '</tfoot>';

			// Setup - add a text input to each footer cell
			$('#table tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input type="text" class="form-control" placeholder="'+title+'" /><i class="fa fa-filter icon filtericon"></i> ' );
			} );

			// DataTable
			var table = $('#table').DataTable({
				"paging": true,
				"info": false,
				"order": [],
				initComplete: function () {
					// Apply the search
					this.api().columns().every( function () {
						var that = this;

						$( 'input', this.footer() ).on( 'keyup change clear', function () {
							if ( that.search() !== this.value ) {
								that
									.search( this.value )
									.draw();
							}
						} );
					} );
				}
			});
		}
	} );
</script>

<footer class="footer noPrint">
	<div class="container">
		<p>Contact <a href="mailto:softwaresupport@signpost.eu">Software Support</a> for issues 😊</p>
	</div>
</footer>

$(document).ready(function () {
    if ($('#table')[0]) {
        $('#table')[0].innerHTML = $('#table')[0].innerHTML + '<tfoot>' + $('#table .thead-dark')[0].innerHTML + '</tfoot>';

        // Setup - add a text input to each footer cell
        $('#table tfoot th').each(function () {
            var title = $(this).text();
            $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" /><i class="fa fa-filter icon filtericon"></i> ');
        });

        // DataTable
        var table = $('#table').DataTable({
            "paging": false,
            "info": false,
            "order": [],
            initComplete: function () {
                // Apply the search
                this.api().columns().every(function () {
                    var that = this;

                    $('input', this.footer()).on('keyup change clear', function () {
                        if (that.search() !== this.value) {
                            that
                                .search(this.value)
                                .draw();
                        }
                    });
                });
            }
        });
    }
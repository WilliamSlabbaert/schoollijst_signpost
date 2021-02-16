$(document).ready(() => {
   /*--- Create DataTable  ---*/
   let tbl = createDataTable();
   
   /*--- Search event ---*/
   $('thead input').each((i,element)=>{
      $(element).on('keyup change',()=>{
         tbl.columns(i).search(element.value).draw();
      });
   });
});

const loadData = (tempArray, tbl) => {
   /*--- Add filtered data to DataTable  ---*/
   if (tempArray != null || tempArray != undefined || typeof (tempArray) === Array) {
      tbl.clear().draw();
      tbl.rows.add(tempArray).draw();
   }
}

/*--- Create DataTable  ---*/
const createDataTable = () =>{
   $('#table thead tr').clone(true).appendTo( '#table thead' );
   $('thead tr:eq(1) th').each((index,element)=>{
      $(element).html( '<input type="text" class="inputSearch form-control" placeholder="Search" />');
   });
   $('.inputSearch').css("width","100px");
  

   let tbl = $('#table').DataTable({
      "paging": true,
      "info": true,
      "searching": true,
      "pagingType": "full_numbers",
      "bPaginate": false,
      "bLengthChange": false,
      "bFilter": true,
      "bInfo": false,
      "bAutoWidth": false,
      "orderCellsTop": true,
      "fixedHeader": true,
      "sDom": 'lrtip'
   });
   loadData(itemArray,tbl);
   return tbl;
}
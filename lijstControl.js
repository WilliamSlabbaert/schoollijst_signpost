let tbl= null;
$(document).ready(() => {
   /*--- Create DataTable  ---*/
   tbl = createDataTable();
   
   /*--- Search event ---*/
   $('#table tfoot input').each((i,element)=>{
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
   $('#table')[0].innerHTML = $('#table')[0].innerHTML + '<tfoot>' + $('#table .thead-dark')[0].innerHTML + '</tfoot>';
   $('#table tfoot th').each((index,element)=>{
      let title = $("th")[index].innerHTML;
      $(element).html( '<input type="text" class="inputSearch form-control" placeholder="Search ' + title + '" /><i class="fa fa-filter icon filtericon"></i> ');
   });

   let tbl = $('#table').DataTable({
      "paging": true,
      "info": true,
      "searching": true,
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
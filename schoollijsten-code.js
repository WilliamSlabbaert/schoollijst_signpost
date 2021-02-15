$(document).ready(() => {
   /* --- Create DataTable  ---*/
   var tbl = $('#tableCustom').DataTable({
      "paging": true,
      "info": false,
      "searching": false,
      "pagingType": "full_numbers",
      "bPaginate": false,
      "bLengthChange": false,
      "bFilter": true,
      "bInfo": false,
      "bAutoWidth": false,
   });

   /* --- Fill (PHP Query) DataTable  ---*/
   tbl.rows.add(itemArray).draw(false)
   
   /* --- Search click event  ---*/
   $('#searchButton').click(() => {
      loadData(Search(),tbl);
   });
});
const loadData = (tempArray,tbl) => {
    /* --- Add filtered data to DataTable  ---*/
   if (tempArray != null || tempArray != undefined || typeof (tempArray) === Array)
      tbl.clear().draw();
      tbl.rows.add(tempArray).draw(false); 
}

const Search = () => {
   /* --- Filters default object array  ---*/
   var temp = itemArray.filter((obj) => String(obj[$('#inptForm select').prop('selectedIndex')]).includes($('#gsearch').val())).reverse();
   return temp;
}
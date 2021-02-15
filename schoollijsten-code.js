$(document).ready(() => {
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
   tbl.rows.add(itemArray).draw(false)
   
   $('#searchButton').click(() => {
      loadData(Search(),tbl);
   });
});
const loadData = (tempArray,tbl) => {
   if (tempArray != null || tempArray != undefined || typeof (tempArray) === Array)
      tbl.clear().draw();
      tbl.rows.add(tempArray).draw(false); 
}

const Search = () => {
   var temp = itemArray.filter((obj) => String(obj[$('#inptForm select').prop('selectedIndex')]).includes($('#gsearch').val())).reverse();
   return temp;
}
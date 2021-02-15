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
   "bAutoWidth": false
});

$(document).ready(() => {
   /*--- Fill default object array(PHP Query) DataTable  ---*/
   loadData(itemArray, tbl);

   /*--- Search click event  ---*/
   $('#searchButton').click(() => {
      loadData(Search(), tbl);
   });
});

const loadData = (tempArray, tbl) => {
   /*--- Add filtered data to DataTable  ---*/
   if (tempArray != null || tempArray != undefined || typeof (tempArray) === Array) {
      tbl.clear().draw();
      tbl.rows.add(tempArray).draw(false);
   }
}
/*--- Filters default object array  ---*/
const Search = () => itemArray.filter((obj) => String(obj[$('#inptForm select').prop('selectedIndex')]).includes($('#gsearch').val())).reverse();


/*--- Code encrypt ---*/
//const _0x3967=['43619kkQnim','860405MvUwre','prop','#gsearch','1793491fMMwil','val','#tableCustom','ready','1buEPXw','10XoFzRm','draw','DataTable','133624oLNBII','includes','clear','124AzrbZM','#searchButton','1298117kdZXRS','filter','219219pMNdtj','33IflmaW','5438CISpjh','add','rows'];const _0x2c26=function(_0x1ec3b2,_0x18bbc9){_0x1ec3b2=_0x1ec3b2-0x1ed;let _0x39670b=_0x3967[_0x1ec3b2];return _0x39670b;};const _0x42565a=_0x2c26;(function(_0x847eac,_0x42fde3){const _0x2abf35=_0x2c26;while(!![]){try{const _0x4b7571=parseInt(_0x2abf35(0x1f3))+parseInt(_0x2abf35(0x1fe))*parseInt(_0x2abf35(0x1fb))+parseInt(_0x2abf35(0x1ee))*-parseInt(_0x2abf35(0x1f2))+-parseInt(_0x2abf35(0x203))+-parseInt(_0x2abf35(0x1fa))*-parseInt(_0x2abf35(0x1ed))+parseInt(_0x2abf35(0x201))*-parseInt(_0x2abf35(0x1ef))+parseInt(_0x2abf35(0x1f6));if(_0x4b7571===_0x42fde3)break;else _0x847eac['push'](_0x847eac['shift']());}catch(_0x239638){_0x847eac['push'](_0x847eac['shift']());}}}(_0x3967,0xc2b3b));var tbl=$(_0x42565a(0x1f8))[_0x42565a(0x1fd)]({'paging':!![],'info':![],'searching':![],'pagingType':'full_numbers','bPaginate':![],'bLengthChange':![],'bFilter':!![],'bInfo':![],'bAutoWidth':![]});$(document)[_0x42565a(0x1f9)](()=>{const _0x4713c5=_0x42565a;loadData(itemArray,tbl),$(_0x4713c5(0x202))['click'](()=>{loadData(Search(),tbl);});});const loadData=(_0x374bc8,_0x2ad859)=>{const _0x232c52=_0x42565a;if(_0x374bc8!=null||_0x374bc8!=undefined||typeof _0x374bc8===Array)_0x2ad859[_0x232c52(0x200)]()[_0x232c52(0x1fc)]();_0x2ad859[_0x232c52(0x1f1)][_0x232c52(0x1f0)](_0x374bc8)[_0x232c52(0x1fc)](![]);},Search=()=>{const _0x1f78d1=_0x42565a;var _0x1c8f7d=itemArray[_0x1f78d1(0x204)](_0x17f2cc=>String(_0x17f2cc[$('#inptForm\x20select')[_0x1f78d1(0x1f4)]('selectedIndex')])[_0x1f78d1(0x1ff)]($(_0x1f78d1(0x1f5))[_0x1f78d1(0x1f7)]()))['reverse']();return _0x1c8f7d;};

window.addEventListener('load', () => {
   //loadData(itemArray);
   document.querySelector('#searchButton').addEventListener('click', () => {
      loadData(Search());
   });

});
const loadData = (tempArray) => {
   var tr = document.querySelectorAll('table tr');
   for (var i = 1; i < tr.length; i++)
      tr[i].remove();

   if (tempArray != null || tempArray != undefined || typeof (tempArray) === Array) {
      for (var i = 0; i < tempArray.length; i++) {
         let temp = tempArray[i];
         document.querySelector('#myTr').insertAdjacentHTML("afterend",
            `<tr><td>${temp[0]}</td><td>${temp[1]}</td><td>${temp[2]}</td><td>${temp[3]}</td><td>${temp[4]}</td><td>${temp[5]}</td><td>${temp[6]}</td><td>${temp[7]}</td></tr>`);
      };
   }
}

const Search = () => {
   var temp = itemArray.filter((obj) => String(obj[document.querySelector('form select').selectedIndex]).includes(document.querySelector('#gsearch').value));
   return temp;
}

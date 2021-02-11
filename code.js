$(document).ready(() => {
   $('#searchButton').click(() => {
      loadData(Search());
   });
});
const loadData = (tempArray) => {
   var tr = document.querySelectorAll('table tr');
   for (var i = 1; i < tr.length; i++)
      tr[i].remove();

   if (tempArray != null || tempArray != undefined || typeof (tempArray) === Array) 
      for (var i = 0; i < tempArray.length; i++) 
         document.querySelector('thead').insertAdjacentHTML("afterend",
            `<tbody><tr><td>${tempArray[i][0]}</td><td>${tempArray[i][1]}</td><td>${tempArray[i][2]}</td><td>${tempArray[i][3]}</td><td>${tempArray[i][4]}</td><td>${tempArray[i][5]}</td><td>${tempArray[i][6]}</td><td>${tempArray[i][7]}</td></tr><tbody>`);
}

const Search = () => {
   var temp = itemArray.filter((obj) => String(obj[document.querySelector('form select').selectedIndex]).includes(document.querySelector('#gsearch').value));
   return temp;
}

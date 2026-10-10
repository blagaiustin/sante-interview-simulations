 jQuery(document).ready(function($) { //jQuery, așteaptă până când structura paginii HTML este încărcată și gata de folosit, apoi execută funcția asta.

     $('#arata-informatii').on('click', function() { //Găsește elementul cu ID-ul arata-informatii și, când se dă click pe el, execută funcția.
         $('#mesaj-informatii').toggle(); //Găsește elementul cu ID-ul mesaj-informatii și schimbă-i starea de vizibilitate. .toggle() exact ca pe un întrerupător:
     });

 });

 jQuery(document).ready(function($) {
     $("#vezi-averitisment").click(function() {
         $("#mesaj-avertisment").toggle();
     });
 });

 jQuery(document).ready(function($) {
     $("#informatii-programare").click(function() {
         $("#mesaj-programare").toggle();
     });
 });
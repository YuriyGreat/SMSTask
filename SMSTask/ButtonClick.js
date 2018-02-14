$(document).ready(function(){  
    $("#addsms").click(function(e){ 
        e.preventDefault(); 
        ajax_search();
    }); 
    $("#TEXT").keyup(function(e){ 
        e.preventDefault(); 
        ajax_search(); 
    }); 
    $("#delete").click(function(e){
        e.preventDefault(); 
        ajax_delete();
    });
 
});


function ajax_search(){ 
  var searchForm = document.forms["searchform"];
  var search_val=$("#TEXT").val(); 
  //alert(searchForm.elements["TEXT"].value);
  $.post("./index.php", {TEXT : search_val}, function(data){
   
  }) 
} 

function ajax_delete(){ 
  var searchForm = document.forms["deleteForm"];
  alert("messages deleted");
  $.post("./index.php", {TEXT : delete_val}, function(data){
   
  }) 
} 


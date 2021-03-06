//modal
var modal;
var clear = 'true';

function openModal(){
  modal = document.getElementById("myModal");
  modal.style.display = "block";
}

function closeModal(){
  modal = document.getElementById("myModal");
  modal.style.display = "none";
}

function openModalUsername(){
  modal = document.getElementById("askUsername_modal");
  modal.style.display = "block";
}

function closeModalUsername(){
  modal = document.getElementById("askUsername_modal");
  modal.style.display = "none";
}

function submitForm(){
  document.getElementById("upload_box").submit();
}

function setItemTrue(item){
  document.getElementById(item).style["border-color"] = "lightgreen";
}

function setItemFalse(item){
  document.getElementById(item).style["border-color"] = "red";
}

function notValidate(){
  return false;
}

function disableProcessing(){
  document.getElementById("start_processing").disabled = true;
  document.getElementById("checkBox").disabled = true;
}

function clearSession(){
  $.ajax({
     url: './php/index/clearSession.php',
     dataType: 'json',
      async: true,
     success: function(data){
         console.log('success');
          //data returned from php
     }
  });
}

function disableClear(){
  //clear = 'false';
}

window.onunload = window.onbeforeunload = (function(){
  console.log(clear);
  if(clear === 'true'){
    clearSession();
  }
})

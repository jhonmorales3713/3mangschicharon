let toastBgColor = {
	success: "#5cb85c",
	warning: "#f0ad4e",
	error: "#f0ad4e"
}

function showToastNotify(message, type){
    $.notify({
        message,
    },{
        type
    });
}

  showCover = function(message){

    $('#current-activity').html(message);
      $('#transparent-cover').css({'display':'table'});

  }

  hideCover = function(){

    $('#current-activity').html('');
      $('#transparent-cover').css({'display':'none'});

  }

$('#datepicker').datepicker({
	uiLibrary: 'bootstrap4'
})

$('.input-daterange').datepicker({
    todayBtn: "linked"
});

// $('input[name="daterange"]').daterangepicker();



$(document).on({
    ajaxStart: function(){
        $(".overlay").css("display","block");
    },
    ajaxStop:function(){
        $(".overlay").css("display","none");
    }
});

function isNumberKeyOnly(evt)   {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

function renderEmpty () {
    return `
    <div class="col-12">
          <div class="portal-table__column">
              <div class="col-12 text-center"><h4>No records found</h4></div>
          </div>
      </div>
    `
}

//autocomplete
function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }

  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }

  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
      x[i].parentNode.removeChild(x[i]);
      }
    }
  }

  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      closeAllLists(e.target);
  });
}

// Display Form Errors
function show_errors(data, container){
  if ('field_errors' in data) { // Check field errors exists in response
    var opts = {
        'class': 'field-error',
        'style': 'font-size:11px; color:#ff0000'
    };

    // Write errors to UI
    Object.keys(data.field_errors).forEach(function(key,index) {
        var el = container.find('#'+key);
        var span_field_error = $('<span>').attr(opts).text(data.field_errors[key]);
        if(el){
            var parent = el.parent();
            if (parent.next('span.field-error').length) {
              parent.next('span.field-error').remove();
            }

            if(parent.is('.input-daterange, .input-group')){ // mostly used by datepicker
                parent.find('input').addClass('has-error');
                container.find('#'+key).parent().after( span_field_error );
            } else if (el.is('select')) { // select2
                if (el.hasClass('select2')) {
                    container.find('#'+key).siblings('.select2-container').addClass('has-error').after( span_field_error );
                } else {
                    container.find('#'+key).addClass('has-error').after( span_field_error );
                }
            } else {  // normal input fields
                el.addClass('has-error');
                container.find('#'+key).after( span_field_error );
            }
        }
        if( typeof CKEDITOR !== "undefined" ){ // check if ckeditor exists in the page
            for(var instanceName in CKEDITOR.instances) {
                if(key == instanceName){
                    var keys = container.find('#cke_'+key+' .cke_contents');
                    keys.addClass('has-error');
                    keys.closest('#cke_'+key).after( span_field_error );
                }
            }
        }
    });
  }
}

function clearFormErrors(container){
    container.find('.has-error').removeClass('has-error');
    container.find('span.field-error').remove();
}

function remove_ext(file){
	// console.log("file",file);
	if(file == null){
		return 'logo-placeholder';
	}else{
		let r = file.split('.').slice(0, -1).join('.')
		return r;
	}
}

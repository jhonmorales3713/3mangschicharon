$(function(){
  var base_url = $("body").data('base_url');
  $('.contactNumber').numeric({
		maxPreDecimalPlaces : 11,
		maxDecimalPlaces: 0,
		allowMinus: false
	});

  function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }
});

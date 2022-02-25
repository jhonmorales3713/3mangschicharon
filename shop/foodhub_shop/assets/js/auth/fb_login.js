function statusChangeCallback(response) {  // Called with the results from FB.getLoginStatus().
  console.log('statusChangeCallback');
  console.log(response);                   // The current login status of the person.
  if (response.status === 'connected') {   // Logged into your webpage and Facebook.
    // testAPI();
  } else {                                 // Not logged into your webpage or we are unable to tell.
    document.getElementById('status').innerHTML = 'Please log ' +
      'into this webpage.';
  }
}


// function checkLoginState() {               // Called when a person is finished with the Login Button.
//   FB.getLoginStatus(function(response) {   // See the onlogin handler
//     statusChangeCallback(response);
//   });
// }


window.fbAsyncInit = function() {
  FB.init({
    appId      : '279580529828552',
    cookie     : true,                     // Enable cookies to allow the server to access the session.
    xfbml      : true,                     // Parse social plugins on this webpage.
    version    : 'v7.0'           // Use this Graph API version for this call.
  });


  // FB.getLoginStatus(function(response) {   // Called after the JS SDK has been initialized.
  //   statusChangeCallback(response);        // Returns the login status.
  // });
};


(function(d, s, id) {                      // Load the SDK asynchronously
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "https://connect.facebook.net/en_US/sdk.js";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// function testAPI() {                      // Testing Graph API after login.  See statusChangeCallback() for when this call is made.
//   console.log('Welcome!  Fetching your information.... ');
//   FB.api('/me', function(response) {
//     console.log('Successful login for: ' + response.name);
//     document.getElementById('status').innerHTML =
//       'Thanks for logging in, ' + response.name + '!';
//   });
// }

function fb_login(){
  FB.login(function(response) {
    if (response.authResponse) {
     FB.api('/me', {fields: 'first_name,last_name,email,birthday,gender'}, function(response) {
       // console.log('Good to see you, ' + response.name + '.');
       // console.log(response);
       $.ajax({
         url: base_url+'auth/authentication/login_social_media',
         type: 'post',
         dataType: 'json',
         cache: false,
         data:{
           email: response.email,
           fname: response.first_name,
           lname: response.last_name,
           birthday: response.birthday,
           gender: response.gender,
           login_type: 'fb'
         },
         beforeSend: function(){
           showCover("Authenticating...");
         },
         success: function(data){
           hideCover();
           if(data.success == true){
             showToast({
                 type: "success",
                 css: "toast-top-full-width mt-5",
                 msg: data.message
             });
             window.location = data.url;
           }else{
             showToast({
                 type: "warning",
                 css: "toast-top-full-width mt-5",
                 msg: data.message
             });
           }
         },
         error: function(){
           showToast({
             type: "error",
             css: "toast-top-full-width mt-5",
             msg: 'Something went wrong. Please try again'
           });
           hideCover();
         }
       });
     });
    } else {
     console.log('User cancelled login or did not fully authorize.');
    }
  },{
    scope: 'email',
    return_scope: true
  });
}

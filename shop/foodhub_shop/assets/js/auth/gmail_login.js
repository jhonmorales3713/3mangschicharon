
var googleUser = {};
var startApp = function() {
	gapi.load('auth2', function() {
		// Retrieve the singleton for the GoogleAuth library and set up the client.
		auth2 = gapi.auth2.init({
			client_id: '741217174198-8tjosbg52nhgio2de092muntq56j6pat.apps.googleusercontent.com',
			cookiepolicy: 'single_host_origin',
			// Request scopes in addition to 'profile' and 'email'
			scope: 'https://www.googleapis.com/auth/userinfo.profile'
		});
		attachSignin(document.getElementById('gmailBtn'));
	});
};

function attachSignin(element) {
	// console.log(element.id);
	auth2.attachClickHandler(element, {},
		function(googleUser) {
				// console.log(googleUser.getBasicProfile().getName());
				var profile = googleUser.getBasicProfile();
				$.ajax({
					url: base_url + 'auth/authentication/login_social_media',
					type: 'post',
          dataType: 'json',
          cache: false,
					data: {
						email: profile.getEmail(),
						fname: profile.getGivenName(),
            lname: profile.getFamilyName(),
						birthday: '',
						gender: '',
						login_type: 'gmail'
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

		},
		function(error) {
			alert(JSON.stringify(error, undefined, 2));
		});
}

$(function(){
	startApp();
});

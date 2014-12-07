<!DOCTYPE html>
<html>
    <head>
        <title>Flash Card Game - Login</title>
        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
        <script>
			function doLogin() {
				if (document.getElementById("username").value.length > 0) {
					if (document.getElementById("password").value.length > 0) {
						//submit the form
						document.getElementById("loginForm").submit();
					} else {
						document.getElementById("reqPass").innerHTML = "*";
						document.getElementById("loginErrorBox").innerHTML = "One or more required fields missing!";
					}
				} else {
					document.getElementById("reqUname").innerHTML = "*";
					document.getElementById("loginErrorBox").innerHTML = "One or more required fields missing!";
					if (document.getElementById("password").value.length <= 0) {
						document.getElementById("reqPass").innerHTML = "*";
					}
				}
			}
        </script>
    </head>
    <body>
        <div class="container">
            <!-- Header Section -->
            <div class="header"><div class="headerText">Flash Card Game - Login</div></div>

            <!-- Login Window -->
            <div class="loginFormHolder">
                <form method="POST" action="<?php echo base_url() ?>index.php/game/login" id="loginForm">
                    <p>User Name<span class="required" id="reqUname"></span></p>
                    <input type="text" name="username" id="username" />
                    <p>Password<span class="required" id="reqPass"></span></p>
                    <input type="password" name="password" id="password" />
                    <!-- Login Error -->
                    <div class="loginError" id="loginErrorBox"><?php
						if (isset($message)) {
							echo $message;
						}
						?></div>
                    <div class="clearFloat"></div><br/>
                    <div class="buttonHolder loginButton"><div class="buttonInner"><div class="button" onclick="doLogin()" ><p>Login</p></div></div></div>
                </form>
            </div>

        </div>

    </body>
</html>

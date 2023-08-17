<!DOCTYPE html>
<html lang="en">
<head>
<title>Turnstile &dash; Dummy Login Demo</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.1/css/bootstrap.min.css" integrity="sha512-siwe/oXMhSjGCwLn+scraPOWrJxHlUgMBMZXdPe2Tnk3I0x3ESCoLz7WZ5NTH6SZrywMY+PB1cjyqJ5jAluCOg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css" integrity="sha512-5PV92qsds/16vyYIJo3T/As4m2d8b6oWYfoqV+vtizRB6KhF1F9kYzWzQmsO6T3z3QG2Xdhrx7FQ+5R1LiQdUA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onloadTurnstileCallback" defer></script>
<script>
  window.onloadTurnstileCallback = function () {
    turnstile.render('#example-container', {
        sitekey: '0x4AAAAAAAIvyo6qh5_dBY98',
        callback: function(token) {
            console.log(`Challenge Success ${token}`);
        },
    });
};

</script>
<style>
html,
body {
  height: 100%;
}

body {
  display: flex;
  align-items: center;
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #fefefe;
}

.form-signin {
  width: 100%;
  max-width: 330px;
  padding: 15px;
  margin: auto;
}

.form-signin .checkbox {
  font-weight: 400;
}

.form-signin .form-floating:focus-within {
  z-index: 2;
}

.form-signin input[type="text"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}

.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
</style>
</head>
<body>
<main class="form-signin">

  <form method="POST" action="testecapcha/teste/login">
    <h2 class="h3 mb-3 fw-normal">Turnstile &dash; Dummy Login Demo</h2>
    <div class="form-floating">
      <input type="text" id="user" class="form-control">
      <label for="user">User name</label>
    </div>
    <div class="form-floating">
      <input type="password" id="pass" class="form-control" autocomplete="off" readonly value="CorrectHorseBatteryStaple">
      <label for="pass">Password (dummy)</label>
    </div>
    <div class="#example-container">
      <div class="cf-turnstile" data-sitekey="0x4AAAAAAAIvyo6qh5_dBY98"></div>
    </div>
    <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
  </form>
 
</main>
</body>
</html>


<?php



?>



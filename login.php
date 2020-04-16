<?php

// include composer autoload
require 'vendor/autoload.php';
require './db.php';

$errorClass = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST["username"];
    $password = $_POST["password"];
    $response = DB::query("SELECT * FROM `User` where login = ? and password = ?", [$name, $password]);
    if (count($response->fetchAll()) == 1) {
        // Authorization succeeded
        $session_id = uniqid();
        setcookie("session_id", $session_id, time() + (86400 * 30), "/");
        DB::query("UPDATE `User` SET session_id = ? where login = ? and password = ?", [$session_id, $name, $password]);
        header("Location: /db_weather/private.php");
    } else {
        $errorClass = "is-invalid";
    }
}

//if ($name == "davron" && password == "12345") {
//    setcookie("")
//  }

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

</head>
<body>

<div class="d-flex align-items-center">
    <form class="form-signin m-auto" method="post">
        <img class="mb-4" src="https://getbootstrap.com/docs/4.4/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
        <h1 class="h3 mb-3 font-weight-normal">
            Sign in
        </h1>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="text" name="username" id="inputEmail" class="form-control <?php echo $errorClass; ?>" placeholder="Email address" required="" autofocus="">
        <div class="invalid-feedback">
            Login is wrong.
        </div>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required="">
        <div class="invalid-feedback">
            Password is wrong.
        </div>
        <div class="checkbox mb-3">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <p class="mt-5 mb-3 text-muted">© 2017-2019</p>
    </form>
</div>
</body>
</html>

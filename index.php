<?php

    require 'autoload.php';

    use vendor\models\User;

    $errorClass = [
        'login' => '',
        'password' => '',
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $password = $_POST["password"];
        $response = User::getItem([
            'login' => $_POST["login"],
        ]);

        if($response){
            if(User::checkPassword($password, $response['password'])){
                $session_id = uniqid();
                $session_expire_time = time() + (86400 * 30);
                setcookie("session_id", $session_id, $session_expire_time, "/");
                $res = User::updateItem([
                    'session_id' => $session_id,
                    'session_expire_time' => date('Y-m-d', $session_expire_time),
                ],[
                    'login' => $response['login']
                ]);


                $url = 'user/'.User::getUserType($response['login']).'/';
                header("Location: ".$url."");
                exit();
            }else
                $errorClass['password'] = 'is-invalid';
        }else
            $errorClass['login'] = 'is-invalid';


    }
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

<div class="d-flex align-items-center mt-5">
    <form class="form-signin m-auto" method="post">
        <h1 class="h3 mb-3 font-weight-normal">
            Sign in
        </h1>

        <div class="my-3">
            <label for="Login" class="sr-only">Login</label>
            <input type="text" name="login" id="Login" class="form-control <?= $errorClass['login']; ?>" placeholder="Login" required="" autofocus="">
            <div class="invalid-feedback">
                Login is wrong.
            </div>
        </div>

        <div class="my-3">
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" name="password" id="inputPassword" class="form-control <?= $errorClass['login']; ?>" placeholder="Password" required="">
            <div class="invalid-feedback">
                Password is wrong.
            </div>
        </div>


        <div class="my-3">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </div>

        <p class="mt-5 mb-3 text-muted">© TEAM 3</p>
    </form>
</div>
</body>
</html>

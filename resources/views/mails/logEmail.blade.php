<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../../resources/css/logEmail.css">
</head>
<body>
    <style>
        body{
            background-color: bisque
            color: blue;
        }
    </style>
    <div class="head">
        <h1>Olá Administrador!</h1>
    </div>
    <br>
    <div class="body">
        <h2>Um erro foi encontrado na plataforma Hello Senai!!</h2><br>
        <p>{{$data['message']}}</p><br>
        <p>Por favor verifique o log para mais informações sobre o erro!!</p>
    </div>
</body>
</html>

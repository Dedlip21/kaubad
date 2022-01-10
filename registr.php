<?php
$yhendus=new mysqli("localhost", "rolanmas", "123456", "rolanmas");
session_start();
// uue kasutaja lisamine andmetabeli sisse
$error=$_SESSION["error"];

function puhastaAndmed($data){
    //trim() - Не учитывается пробел в начале или конце текста
    $data=trim($data);
    //htmlspecialchars - игнорирует <kask>
    $data=htmlspecialchars($data);
    // stripslashes - Удаляет это хехе ( \ ) <-- да это
    $data=stripslashes($data);
    return $data;
}
if(isset($_REQUEST["knimi"]) && isset($_REQUEST["psw"])) {

    $login = puhastaAndmed($_REQUEST["knimi"]);
    $pass = puhastaAndmed($_REQUEST["psw"]);
    $sool = 'vagavagatekst';
    $krypt = crypt($pass, $sool);

//kasutajanimi kontroll
    $kask = $yhendus->prepare("SELECT id, unimi, psw FROM uuedkasutajad WHERE unimi=?");
    $kask->bind_param("s", $login);
    $kask->bind_result($id, $login, $pass);
    $kask->execute();

    if ($kask->fetch()) {
        $_SESSION["error"] = "Kasutaja on juba olemas";
        header("Location: $_SERVER[PHP_SELF]");
        $yhendus->close();
        exit();
    } else {
        $_SESSION["error"] = "  ";
    }

    $kask = $yhendus->prepare("INSERT INTO uuedkasutajad(unimi, psw, isanimi) VALUES(?,?,?)");
    $kask->bind_param("ssi", $login, $krypt, $_REQUEST["admin"]);
    $kask->execute();
    $_SESSION['unimi'] = $login;
    $_SESSION['admin'] = true;
    /*header("location: kaubahaldus.php");
    $yhendus->close();
    exit();*/
}
?>
<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Registreerimisvorm</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>

<body>
<h1>Uus kasutaja registreerimine</h1>
<form action="registr.php" method="post">
    <label for="knimi">Kasutajanimi</label>
    <input type="text" placeholder="Sisesta kasutajanimi" name="knimi" id="knimi" requered>
    <br>
    <label for="psw">Parool</label>
    <input type="password" placeholder="Sisesta parool" name="psw" id="psw" requered>
    <br>
    <label for="admin">Kas teha admin?</label>
    <input type="checkbox" name="admin" id="admin" value="1">
    <br>
    <label for="psw">Parool</label>
    <input type="submit" value="Loo kasutaja">


<strong> <?=$error ?></strong>

</form>



</body>
</html>

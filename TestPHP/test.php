<?php
$name = "";
$message = "";
$age = 0;

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["my_name"];
    if($name == "Teezy") {
        $message = "Cau Teezy"; 
        $age = $_POST["my_age"];
        }
        else {
            $message = "Tebe neznam";
        }
    }


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formular test</title>
</head>
<body>
    <h1>Test formulare</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolore, ea molestias earum quisquam modi aspernatur ad animi dicta. Obcaecati mollitia ab rerum qui accusantium cumque sit assumenda quaerat, illum similique?</p>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Perferendis id ad fuga eaque vitae dolorem voluptatem sit officiis perspiciatis quo ab ipsam adipisci suscipit quidem, sint nulla. Numquam, assumenda doloremque!</p>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Unde, id! Cumque, reiciendis quia, error harum alias, explicabo ratione animi eligendi veniam cupiditate exercitationem? Aliquam voluptatem quisquam labore perspiciatis dolorum iusto.</p>
    <form method="post">
        <input type="text" name="my_name" id="" placeholder="Zadejte jmeno">
        <input type="number" name="my_age" id="" placeholder="Zadejte Vek">
        <button type="submit">Odeslat</button>
    </form>

    <p>
        <?php  
        echo "Vystup: "; 
        echo $message;

        ?>
    </p>

    <p>
        <?php 
        echo "Tvuj vek je: ";
        echo $age;
        ?>
    </p>

</body>
</html>
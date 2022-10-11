<?php
require_once "../php/functions.php";

?>

<?= show_page("header", ["page_title" => "ChatMa8 - About"]) ?>
<h1>About page</h1>


<?php
echo dirname($_SERVER['SCRIPT_URL'], 2) . '<br>';
// echo dirname($_SERVER['SERVER_ADDR']);
// echo dirname($_SERVER['SERVER_NAME']);
// sleep(4);
// header('Location: ' . dirname($_SERVER['SCRIPT_URL'], 3))
// header('Location: /chatma8')
?>
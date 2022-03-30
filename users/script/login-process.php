<?php
require_once '../../core/init.php';

$user = new User();
$validate = new Validate();
$show = new Show();

if (isset($_POST['action']) && $_POST['action'] == 'loginStudent') {

    $regno = $show->test_input($_POST['regno']);
    $password = $show->test_input($_POST['password']);

    if (empty($_POST['regno'])) {
        echo $show->showMessage('danger','Registration number is required', 'warning');
        return false;
    }
    if (empty($_POST['password'])) {
        echo $show->showMessage('danger','Password is required', 'warning');
        return false;
    }


    $login = $user->login($regno, $password);
    if ($login) {
        echo 'success';
    }


}

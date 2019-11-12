<?php

class UserController
{
    /**
     * @return bool
     */
    public function actionRegister()
    {

        $name = false;
        $email = false;
        $password = false;
        $result = false;

        if (isset($_POST['submit'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            $errors = false;

            if (!User::checkName($name)) {
                $errors[] = 'Имя не должно быть короче 2-х символов';
            }

            if (!User::checkEmail($email)) {
                $errors[] = 'E-mail Не верный';
            }

            if (!User::checkPassword($password)) {
                $errors[] = 'Пароль должен быть не менее 6-ти символов';
            }

            if(User::checkEmailExists($email)){
                $errors[] = 'Такой E-mail уже используется';
            }

            if ($errors == false){
                $result = User::register($name, $email, $password);
            }
        }

        require_once(ROOT . '/views/user/register.php');
        return true;

    }
}
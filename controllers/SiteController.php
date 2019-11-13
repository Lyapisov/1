<?php

class SiteController
{

    public function actionIndex()
    {
        $categories = array();
        $categories = Category::getCategoriesList();

        $latestProducts = array();
        $latestProducts = Product::getLatestProducts(6);
        
        require_once(ROOT . '/views/site/index.php');

        return true;
    }

    public function actionContact(){

       $userEmail = '';

       $userText = '';
       $result = false;

       if(isset($_POST['submit'])){

           $userEmail = $_POST['userEmail'];
           $userText = $_POST['userText'];

           $errors = false;

           if(!User::checkEmail($userEmail)){
               $errors[] = 'Неправильный E-mail';
           }

           if($errors == false){
               $adminEmail = 'aaa@rambler.ru';
               $message = "Текст: {$userText}. От: {$userEmail}";
               $subject = 'Тема письма';
               $result = mail($adminEmail, $subject, $message);
               $result = true;
           }
       }

       require_once (ROOT . '/views/site/contact.php');
       return true;
    }
}

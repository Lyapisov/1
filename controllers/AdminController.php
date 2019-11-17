<?php
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 16.11.2019
 * Time: 18:33
 */

class AdminController extends AdminBase{

    public function actionIndex(){

        Self::checkAdmin();

        $productList = Product::getProductsList();
        require_once (ROOT . '/views/admin/index.php');
        return true;
    }


}
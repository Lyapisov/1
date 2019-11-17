<?php


class CartController{

    public function actionAdd($id){

        Cart::addProduct($id);

        $referrer = $_SERVER['HTTP_REFERER'];
        header("Location: $referrer");
    }

    public function actionAddAjax($id){

        echo Cart::addProduct($id);
        return true;
    }

    public function actionDelete($id)
    {
        // Удаляем заданный товар из корзины
        Cart::deleteProduct($id);
        // Возвращаем пользователя в корзину
        header("Location: /cart");
    }

    public function actionIndex(){

        $categories = array();
        $categories = Category::getCategoriesList();

        $productsInCart = false;
        $productsInCart = Cart::getProducts();

        if($productsInCart) {

            $productsIds = array_keys($productsInCart);
            $products = Product::getProductsByIds($productsIds);

            $totalPrice = Cart::getTotalPrice($products);
        }

        require_once (ROOT . '/views/cart/index.php');
        return true;

    }

    public function actionCheckout(){

        $categories = array();
        $categories = Category::getCategoriesList();

        $result = false;

        // Обработка формы
        if (isset($_POST['submit'])) {
            // Если форма отправлена
            // Получаем данные из формы
            $userName = $_POST['userName'];
            $userPhone = $_POST['userPhone'];
            $userComment = $_POST['userComment'];
            // Флаг ошибок
            $errors = false;
            // Валидация полей
            if (!User::checkName($userName)) {
                $errors[] = 'Неправильное имя';
            }
            if (!User::checkPhone($userPhone)) {
                $errors[] = 'Неправильный телефон';
            }
            if ($errors == false) {
                // Если ошибок нет

                // Получием данные из корзины
                $productsInCart = Cart::getProducts();

                if (!User::isGuest()) {
                    // Если пользователь не гость
                    // Получаем информацию о пользователе из БД
                    $userId = User::checkLogged();
                    $user = User::getUserById($userId);
                    $userName = $user['name'];
                } else {
                    // Если гость, поля формы останутся пустыми
                    $userId = false;
                }

                // Сохраняем заказ в базе данных
                $result = Order::save($userName, $userPhone, $userComment, $userId, $productsInCart);

                if ($result) {
                    // Если заказ успешно сохранен
                    // Оповещаем администратора о новом заказе по почте
                    $adminEmail = 'php.start@mail.ru';
                    $message = '<a href="http://digital-mafia.net/admin/orders">Список заказов</a>';
                    $subject = 'Новый заказ!';
                    mail($adminEmail, $subject, $message);
                    // Очищаем корзину
                    Cart::clear();
                }
            } else {
                // Получием данные из корзины
                $productsInCart = Cart::getProducts();
                // Находим общую стоимость
                $productsIds = array_keys($productsInCart);
                $products = Product::getProductsByIds($productsIds);
                $totalPrice = Cart::getTotalPrice($products);
                // Количество товаров
                $totalQuantity = Cart::countItems();
            }
        } else {

            // Получием данные из корзины
            $productsInCart = Cart::getProducts();
            // Если товаров нет, отправляем пользователи искать товары на главную
            if ($productsInCart == false) {
                header("Location: /");
            } else {

                // Находим общую стоимость
                $productsIds = array_keys($productsInCart);
                $products = Product::getProductsByIds($productsIds);
                $totalPrice = Cart::getTotalPrice($products);
                // Количество товаров
                $totalQuantity = Cart::countItems();

                // Поля для формы
                $userName = false;
                $userPhone = false;
                $userComment = false;

                if (!User::isGuest()) {
                    // Если пользователь не гость
                    // Получаем информацию о пользователе из БД
                    $userId = User::checkLogged();
                    $user = User::getUserById($userId);
                    $userName = $user['name'];
                }
            }
        }
        // Подключаем вид
        require_once(ROOT . '/views/cart/checkout.php');
        return true;
    }

}
#Простая библиотека для работы с API АТОЛ онлайн.

## Установка

$ php composer.phar require dmuromtsev/php-atol

## Использование

use dmuromtsev\phpAtol\Atol;

$A = new Atol( 'login', 'password', 'group_code' );

//Регистрация документа

$A->send('operation_type', array('params'));


//Получение результата обработки документа

$A->check( 'uuid' );


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
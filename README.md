Попрошу учесть Проект был реализован в условиях острой нехватки времени  
для реального использования требуется ряд доработок и оптимизаций. 


установвка 

склонить в папку проекта 
composer update + install 
.env - заполнить актуальными данными подключений к бд , так же пароль для BasicAuth выведен в отдельную переменную окружения
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate



php bin/dbSeeder.php - отдельный простенький скрипт сидер на посев 1М записей 
почем отдельный ? потомучто не хотел захломлять проект лишними зависимостями 
потомучто он отрабатывает гораздо быстрее 
потомучто столкнулся с ошибками выолнения стандартных фикстур из пакетов fzaninotto/faker + doctrine/doctrine-fixtures-bundle

так же в корне проекта 
API_BOOK.postman_collection.json - коллекция для тестов постманом

реализоанны след маршруты
------------------ ----------- -------- ------ --------------------------
  Name               Method      Scheme   Host   Path
 ------------------ ----------- -------- ------ --------------------------
  app.swagger        GET         ANY      ANY    /api/doc.json
  api_books_create   POST        ANY      ANY    /api/books
  api_books_index    GET         ANY      ANY    /api/books
  api_book_show      GET         ANY      ANY    /api/books/{id}
  api_book_update    PUT|PATCH   ANY      ANY    /api/books/{id}
  api_book_delete    DELETE      ANY      ANY    /api/books/{id}
 ------------------ ----------- -------- ------ --------------------------

используются следующие пакеты на основе symfony 6 
composer create-project symfony/skeleton:"6.*" ./symf6_api >> на 7ой никак не удавалось прикрутить FOSRest bundle 
composer require jms/serializer-bundle
composer require friendsofsymfony/rest-bundle
composer require symfony/maker-bundle --dev
composer require symfony/orm-pack  --with-all-dependencies
composer require symfony/validator
composer require symfony/security-bundle
composer require nelmio/api-doc-bundle  >> потребовало включить анотации 


# ReCRM #
Импорт объектов недвижимости и агентов из ReCRM

## Description ##
* Плагин выгружает информацию об объектах/агентах из ReCRM используя API http://api.recrm.ru/doc/index.html
* Позволяет настроить категории недвижимости для импорта объектов в зависимости от их типа.
* Сохраняет изображения агентов и объектов на сервере сайта.

## Installation ##
1. Установите плагин
2. Перейдите в раздел с настройками плагина, заполните поля согласно их назначению.
2.1. Ключ - необходимо запросить у поддержки ReCRM
2.2. Водяной знак - накладывать watermark указанный в настройках ReCRM
2.3. Скрытые объекты - выгружать объекты, у которых свойство hidden = true
2.4. Активный статус - выгружать объекты, у которых свойство status = 0
2.5. Успешный статус - выгружать объекты, у которых свойство status = 1
2.6. Неудачный статус - выгружать объекты, у которых свойство status = 3
3. Настройте ЧПУ для агентов и объектов недвижимости.
3.1. Агенты с категориями - если необходимо, можно добавить таксономию для агентов
3.2. Префикс страницы агентов - адрес с агентами относительно корня сайта, например /agents/
3.3. Недвижимость с категориями - если необходимо, можно добавить таксономию для недвижимости. При отмеченной опции плагин позволяет автоматически определить нужные вам типа недвижимости в категориях.
3.3. Префикс страницы недвижимости - адрес с объектами относительно корня сайта, например /estate/
5. Если необходимо разместить разные типы недвижимости в разных категориях, создайте категории, перейдите в раздел с настройками плагина "Типы недвижимости и категории". Определите, какие типы недвижимости нужно отображать в созданных категориях.
6. Добавьте cron задачу для импорта. Рекомендуется использовать flock для предотвращения повторного запуска скрипта.
Пример запуска задачи с flock:
``` */5 * * * * /usr/bin/flock  -n /var/tmp/cron.recrm.import.lock -c '/opt/php71/bin/php -f /path.../wp-content/plugins/recrm/cron.php' ```
Пример запуска задачи без flock:
``` */5 * * * * /opt/php71/bin/php -f /path.../wp-content/plugins/recrm/cron.php ```
7. В связи присутствием большого количества фотографий, скрипт сначала загрузит всю информацию во временные файлы. После того как файлы созданы, при каждом запуске задачи cron задачи, скрипт поэтапно (по 30 объектов) начнет запись объектов в базу данных вместе с фотографиями.
Переопределить количество импортируемых объектов за раз можно при помощи константы (int) RECRM_CHUNK_TEMP_FILES

## FAQ ##

### Как получить API ключ ###

* Обратиться в службу поддержки https://recrm.ru/

### Как получить демо данные ###

* Указать ключ demo

### Какие хуки/фильтры присутствуют в плагине ###

* recrm_import_convert_phone - конвертирует номер телефона агента [^0-9+]

```php
add_filter('recrm_import_convert_phone', function($phone, $phone_initial) {
    //return $phone;
}, 10, 2);
```

* recrm_store_tax_id - определяет таксономию для объекта недвижимости в зависимости от его свойства type_id

```php
add_filter('recrm_store_tax_id', function($tax_id, $item) {
    //return $tax_id;
}, 10, 2);
```

* recrm_import_finish - срабатывает каждый раз после завершения крон задачи

### Что происходит с удаленными объектами в ReCRM ###
* Объект/Агент будет помещен в корзину.

### У меня не работает ЧПУ/адрес с объектом/агентом ###
* Перейдите в "Настройки" - "Постоянные ссылки" - нажмите кнопку "Сохранить изменения"

# WordPress CustomRoute

Класс для создания кастомного роутинга для различных задач:
- создания полноценных html-страниц без необходимости добавлять их в административной панели
- REST-like ответов в формате JSON
- имеет возможность проверять наличие авторизованного пользователя и возвращать объект данного пользователя

В классе `CustomRoute` имеются 3 функции:
- `parseRoute` - основная функция, которая проверяет текущий адрес запроса на вхождение и поддерживает указание переменных; возвращает пустой массив, если путь не удовлетворяет условию, или массив с параметрами, если данный путь прошёл проверку.
- `responseHtml` - возвращает ответ в формате html
- `responseJson` - возвращает ответ в формате json

В настоящее время `parseRoute` может работать только с латинскими строками (кириллическое написание пока не поддерживается).

Функции `responseHtml` и `responseJson` также устанавливают `http_response_code === 200` для того, чтобы ответ воспринимался как существующий адрес.


## Пример использования

Роуты указываются в файле `core.php`. Необходимо задать проверяемый путь (с указанием нужных переменных) и функцию-обработчик:
```php
add_action('wp', 'customRouter');
function customRouter()
{
    global $wp;
    switch (true) {

        case ($request = CustomRoute::parseRoute('magic-page/{value1}/{value2}', $wp->request)): {
            CustomRoute::responseHtml(otherHandler($request['value1'], $request['value2']));
        }

        case ($request = CustomRoute::parseRoute('any-action/{filter1}/{filter2}', $wp->request)): {
            CustomRoute::responseJson(anotherHandler($request['filter1'], $request['filter2']));
        }

        default: return null;
    }
}
```

Дополним пример функциями-обработчиками:
```php

function otherHandler(string $filter1, string $filter2): string
{
    $user = wp_get_current_user();

    return(
        '<p>login: '. $user->user_login .'</p>' .
        '<p>filter1: '. $filter1 .'</p>' .
        '<p>filter2: '. $filter2 .'</p>'
    );
}


function anotherHandler(string $filter1, string $filter2): array
{
    if(!is_user_logged_in()) return accessDeniedJson();

    $user = wp_get_current_user();

    return [
        'user' => [
            'login' => $user->user_login,
            'nicename' => $user->user_nicename,
            'role' => current($user->roles)
        ],
        'filter1' => $filter1,
        'filter2' => $filter2
    ];
}


function accessDeniedJson(): array
{
    return [
        'code' => 401,
        'message' => 'access denied'
    ];
}
```

Не забудьте подключить файл `core.php` в `functions.php`.
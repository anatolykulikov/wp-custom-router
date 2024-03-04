<?php

class CustomRoute {

    /**
     * @param string $path - маска проверки пути
     * @param string $verified - проверяемый запрос
     *
     * @return array
     */
    public static function parseRoute(string $path, string $verified): array
    {
        $params = [];
        $pathArgs = [];
        foreach (explode('/', $path) as $item) {
            if(preg_match('/(?<={)\S+(?=})/m', $item, $matches)) {
                $params[] = $matches[0];
                $pathArgs[] = '([a-zA-Z0-9-_]+)';
            } else {
                $pathArgs[] = $item;
            }
        }

        $regexpPath = implode('\/', $pathArgs);
        preg_match_all('/'. $regexpPath . '/', $verified, $pathMatches, PREG_SET_ORDER, 0);

        if(empty($pathMatches)) return [];

        $returned = ['is_entry' => true];

        for ($i = 0; $i < count($params); $i++) {
            $returned[$params[$i]] = $pathMatches[0][$i+1];
        }

        return $returned;
    }


    /**
     * Возвращает ответ в формате html
     * @param string $html
     * @return void
     */
    public static function responseHtml(string $html): void
    {
        header('Content-Type: text/html; charset=utf-8');
        http_response_code(200);
        echo($html);
        exit;
    }

    /**
     * Возвращает ответ в формате json
     * @param mixed $jsonData
     * @return void
     */
    public static function responseJson(mixed $jsonData): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(200);
        echo(json_encode($jsonData));
        exit;
    }
}
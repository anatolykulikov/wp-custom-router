<?php

// Подключение основного класса
require_once('CustomRoute.php');


// Подлючение к хуку
add_action('wp', 'customRouter');
function customRouter()
{
    global $wp;
    switch (true) {

        // Здесь указываем роуты для подключения

        default: return null;
    }
}

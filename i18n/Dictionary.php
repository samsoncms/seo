<?php
namespace samsoncms\seo;

use samsonphp\i18n\IDictionary;

class Dictionary implements IDictionary
{
    public function getDictionary()
    {
        return array(
            "en" => array(
                "Модуль предназначен для вывода мета тегов на странице" => "The module to display the meta tag on the page",
                "А также для генерации сайт мапа для сайта" => "And also to generate saitmap for the site",
                "СЕО модуль" => "SEO Module",
                "Введите значение" => "Value",
                "Заголовок" => "Title",
                "Описание" => "Description",
                "Ключевые слова" => "Keywords",
                "Картинка" => "Picture",
                "Порядок подключения тега Url" => "Connect Url",
                "Если будет задан урл, то будет загружатся он" => "If defined this URL, it will be loaded",
                "Если нет то поднянется родительский" => "If not, it will be load the parent",
                "Если не задано, то будет использован урл страницы материала" => "If not defined, it will generate a full URL of the page",
                "Имя сайта" => "Site name",
                "Название продукта" => "Product name",
                "Цена" => "Price",
                "Валюта" => "Currency",
                "Автор" => "Author",
                "Издатель" => "Publisher",
                "Динамические мета теги" => "Dynamic meta tags",
                "Что бы отобразить на сайте тег" => "To display on the site",
                "Просто введите" => "Simply enter the tag"
            ),
            "ua" => array(
                "Модуль предназначен для вывода мета тегов на странице" => "Модуль генерації метатегів",
                "А также для генерации сайт мапа для сайта" => "А також генерації сайтмапи",
                "СЕО модуль" => "СЕО модуль",
                "Введите значение" => "Значення",
                "Заголовок" => "Заголовок",
                "Описание" => "Опис",
                "Ключевые слова" => "Ключові слова",
                "Картинка" => "Картинка",
                "Порядок подключения тега Url" => "Підключення Url",
                "Если будет задан урл, то будет загружатся он" => "Якщо вказано url, він буде завантажуватись",
                "Если нет то поднянется родительский" => "Якщо ні, тоді буде братися батьківський",
                "Если не задано, то будет использован урл страницы материала" => "Якщо не задано, то буде використовуватись url сторінки",
                "Имя сайта" => "Назва сайту",
                "Название продукта" => "Назва продукту",
                "Цена" => "Ціна",
                "Валюта" => "Валюта",
                "Автор" => "Автор",
                "Издатель" => "Видавник",
                "Динамические мета теги" => "Динамічні мета теги",
                "Что бы отобразить на сайте тег" => "Для відображення на сайті тегу",
                "Просто введите" => "Просто введіть"
            ),
            "de" => array(
                "Модуль предназначен для вывода мета тегов на странице" => "Das Modul ist das Meta-Tag auf der Seite angezeigt werden",
                "А также для генерации сайт мапа для сайта" => "Und auch saytmap für Website zu generieren",
                "СЕО модуль" => "SEO-Modul",
                "Введите значение" => "Geben Sie einen Wert",
                "Заголовок" => "Kopfzeile",
                "Описание" => "Beschreibung",
                "Ключевые слова" => "Stichworte",
                "Картинка" => "Bild",
                "Порядок подключения тега Url" => "Um eine Verbindung tag URL",
                "Если будет задан урл, то будет загружатся он" => "Wenn eine URL angegeben wird, dann wird es geladen",
                "Если нет то поднянется родительский" => "Wenn nicht, zieht es die Eltern",
                "Если не задано, то будет использован урл страницы материала" => "Wenn nicht angegeben, wird die URL der Seite, das Material verwendet werden",
                "Имя сайта" => "Site Name",
                "Название продукта" => "Produktnamen",
                "Цена" => "Preis",
                "Валюта" => "Währung",
                "Автор" => "Autor",
                "Издатель" => "Herausgeber",
                "Динамические мета теги" => "Dynamische Meta-Tags",
                "Что бы отобразить на сайте тег" => "Zur Anzeige Online den Tag",
                "Просто введите" => "geben Sie Ihre"
            ),
        );
    }
}

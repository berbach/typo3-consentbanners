<?php
namespace Bb\ConsentBanner\Utility;


class CookieUtility
{
    /**
     * Check when page new loaded is Cookie
     * @param string $cookie_name
     * @param null $cookie_value
     * @return bool
     */
    public static function isCookie(string $cookie_name, $cookie_value = null): bool
    {
        if(is_null($cookie_value)){
            return (isset($_COOKIE[$cookie_name]) && !empty($_COOKIE[$cookie_name]));
        }

        return (isset($_COOKIE[$cookie_name]) && !empty($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] === $cookie_value);

    }

    /**
     *
     * @param string $cookie_name
     * @return mixed|string
     */
    public static function getCookieValue(string $cookie_name): mixed
    {
        if(self::isCookie($cookie_name)){
            return $_COOKIE[$cookie_name];
        }
        return '';
    }

}

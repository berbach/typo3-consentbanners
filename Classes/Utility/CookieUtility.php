<?php
namespace Bb\Consentbanners\Utility;


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

    /**
     * @param string $cookie_name
     * @param string $cookie_value
     * @param int $day
     * @return bool
     */
    public static function setCookie(string $cookie_name, string $cookie_value, int $day = 1 ): bool
    {

        return setcookie($cookie_name, $cookie_value, time() + ($day*24*60*60), "/");
    }

    /**
     * Check is Session Started
     * @return bool
     */
    //TODO Helper Function for v2
    public static function isSessionStarted(): bool
    {
        if ( PHP_SAPI !== 'cli' ) {
            if (PHP_VERSION_ID >= 50400) {
                return session_status() === PHP_SESSION_ACTIVE;
            }

            return !(session_id() === '');
        }
        return FALSE;
    }

}

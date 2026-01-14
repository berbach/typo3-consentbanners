<?php

declare(strict_types=1);

namespace Bb\ConsentBanner\Utility;

use Exception;
use Random\RandomException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StringUtility
{
    public static function getUniqueId(string $prefix = ''): string
    {
        $uniqueId = uniqid($prefix, true);
        return str_replace('.', '', $uniqueId);
    }
    /**
     * @param int $length
     * @return string
     * @throws RandomException
     */
    public static function generateUniqueId(int $length = 11): string
    {

        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $uniqueId = '';
        $charLength = strlen($chars);

        for ($i = 0; $i < $length; $i++) {
            // Generiert ein zufälliges Zeichen aus der $chars-Zeichenkette
            $uniqueId .= $chars[random_int(0, $charLength - 1)];
        }

        return $uniqueId;
    }
    /**
     * Create hash
     *
     * @param string $string
     * @return string
     * @throws Exception
     */
    public static function generateHash(string $string): string
    {
        $string .= self::getEncryptionKey();
        return substr(md5($string), 0, 20);
    }

    /**
     * $alpha_out  = alphaID($number_in, false, 8);
     * $number_out = alphaID($alpha_in, true, 8);
     *
     * @param int|string $in
     * @param bool $to_num
     * @param bool|int $pad_up
     * @return int|string
     */
    public static function alphaID(int|string $in, bool $to_num = false, bool|int $pad_up = false): int|string
    {
        $out   =   '';
        $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base  = strlen($index);

        if ($to_num) {
            // Digital number  <<--  alphabet letter code
            $len = strlen($in) - 1;

            for ($t = $len; $t >= 0; $t--) {
                $bcp = bcpow((string)$base, (string)($len - $t));
                $out = $out + strpos($index, substr($in, $t, 1)) * $bcp;
            }

            if (is_numeric($pad_up)) {
                $pad_up--;

                if ($pad_up > 0) {
                    $out -= pow($base, $pad_up);
                }
            }
        } else {
            // Digital number  -->>  alphabet letter code
            if (is_numeric($pad_up)) {
                $pad_up--;

                if ($pad_up > 0) {
                    $in += pow($base, $pad_up);
                }
            }

            for ($t = ($in != 0 ? floor(log($in, $base)) : 0); $t >= 0; $t--) {
                $bcp = bcpow((string)$base, (string)$t);
                $a   = floor($in / $bcp) % $base;
                $out = $out . substr($index, $a, 1);
                $in  = $in - ($a * $bcp);
            }
        }

        return $out;
    }

    /**
     * Get TYPO3 encryption key
     *
     * @return string
     * @throws Exception
     */
    protected static function getEncryptionKey(): string
    {
        $confVars = self::getTypo3ConfigurationVariables();
        if (empty($confVars['SYS']['encryptionKey'])) {
            throw new \DomainException('No encryption key found in this TYPO3 installation', 1514910284796);
        }
        return $confVars['SYS']['encryptionKey'];
    }

    /**
     * Get extension configuration from LocalConfiguration.php
     *
     * @return array
     */
    protected static function getTypo3ConfigurationVariables(): array
    {
        return (array)$GLOBALS['TYPO3_CONF_VARS'];
    }
}

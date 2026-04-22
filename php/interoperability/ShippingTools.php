<?php

namespace ship\interoperability;

use Exception;

class ShippingTools {

    /**
     * Parameters for JSON serialization.
     */
    private const JSON_PARAMETERS = JSON_HEX_QUOT | JSON_HEX_TAG;

    /**
     * Encodes the given object to a JSON string.
     * @param array|string|float|int|bool $object The object.
     * @return string
     */
    public static function jsonEncode(array|string|float|int|bool|null $object) : string {
        return json_encode($object, self::JSON_PARAMETERS);
    }

    /**
     * Decodes the JSON to PHP data types.
     * @param string $jsonString The JSON string.
     * @return array|string|float|int|bool|null
     */
    public static function jsonDecode(string $jsonString) : array|string|float|int|bool|null {
        return json_decode($jsonString,true);
    }

    /**
     * Checks if a given datatype is scalar.
     * @param string $type The datatype.
     * @return bool
     */
    public static function isScalar(string $type) : bool {
        return match ($type) {
            "integer", "boolean", "bool", "int", "string", "float", "double", "content" => true,
            default => false,
        };
    }

    /**
     * Checks if a string starts with a substring.
     * @param string $needle The string to search.
     * @param string $haystack The string to search in.
     * @return bool
     */
    public static function startsWith(string $needle, string $haystack) : bool {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Checks if a string ends with a substring.
     * @param string $needle The string to search.
     * @param string $haystack The string to search in.
     * @return bool
     */
    public static function endsWith(string $needle, string $haystack) : bool {
        $length = strlen($needle);
        return $length === 0 || (substr($haystack, -$length) === $needle);
    }

    /**
     * Returns the last n lines of a file.
     * @param string $file The file.
     * @param int $numberLines The number of lines to return.
     * @return string|null
     */
    public static function getLastLinesOfFile(string $file, int $numberLines = 100) : string|null {
        if (!file_exists($file) && !self::urlExists($file)) return null;
        $windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        if ($windows) {
            $lines = array();
            $fp = fopen($file, "r");
            while (!feof($fp)) {
                $line = fgets($fp, 4096);
                $lines[] = $line;
                if (count($lines) > $numberLines) array_shift($lines);
            }
            fclose($fp);
            return implode(PHP_EOL, $lines);
        } else {
            exec("tail -" . $numberLines . " \"" . $file . "\"", $output);
            return implode(PHP_EOL, $output);
        }
    }

    /**
     * Mask typical characters causing problems.
     * @param string $str The string to mask.
     * @return string
     */
    public static function maskTypical(string $str) : string {
        return str_replace("\"", "'", $str);
    }

    /**
     * Check whether the array is empty or not.
     * @param array $array The array.
     * @return bool
     */
    public static function isEmpty(array $array) : bool {
        return (count($array) === 0);
    }

    /**
     * Only unique elements with unique keys in an array.
     * @param IShippable[] $array The array
     * @return IShippable[]
     */
    public static function uniqueKeys(array $array) : array {
        return array_values(
            array_reduce($array, function ($a, IShippable $o) {
                $a[ShipperRegister::getKey($o)] = $o; return $a;
            }, [])
        );
    }

    /**
     * Does the given array has any string as a key?
     * @param array $array The array to check.
     * @return bool
     */
    public static function hasStringKeys(array $array) : bool {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    /**
     * Checks if an array has masked integer keys.
     * @param array $array The array to check.
     * @return bool
     */
    public static function hasMaskedIntegerKeys(array &$array) : bool {
        if (!self::hasStringKeys($array)) return false;
        if (!self::isEmpty(array_filter(array_keys($array), function (string $k) {
            return !(self::startsWith('i', $k) && intval(substr($k, 1)) . '' === substr($k, 1));
        }))) return false;
        $array = array_reduce(array_keys($array), function (array $a, string $k) use ($array) {
            $a[intval(substr($k, 1))] = $array[$k];
            return $a;
        }, []);
        return true;
    }

    /**
     * Flattens an array.
     * @param array $array The array.
     * @param bool $preserveKeys The keys to preserve.
     * @return array
     */
    public static function flatten(array $array, bool $preserveKeys = false) : array {
        $return = [];
        array_walk_recursive($array, function($a,$k) use (&$return, $preserveKeys) {
            if ($preserveKeys) $return[$k] = $a; else $return[] = $a;
        });
        return $return;
    }

    /**
     * Only unique elements within an array.
     * @param array $array The array.
     * @return array
     */
    public static function unique(array $array) : array {
        return array_reduce($array, function($a, $b) { if (!in_array($b, $a)) $a[] = $b; return $a; }, []);
    }

    /**
     * Checks if a specific object of {@link IShippable} is in a given array.
     * @param IShippable[] $arr The array.
     * @param IShippable|string|int $obj The object or the key of the object.
     * @return bool
     */
    public static function inArray(array $arr, IShippable|string|int $obj) : bool {
        $isId = is_string($obj) || is_int($obj);
        $oKey = $isId ? $obj : ShipperRegister::getKey($obj);

        foreach ($arr as $a) {
            $aIsId = (is_string($a) || is_int($a));
            $aKey = $aIsId ? $a : ShipperRegister::getKey($a);
            if (($isId || $aIsId || get_class($a) === get_class($obj)) && $aKey === $oKey) return true;
        }
        return false;
    }

    /**
     * Generates an UUID.
     * @param string|null $data The data.
     * @return string
     * @throws Exception
     *
     * @url https://www.uuidgenerator.net/dev-corner/php
     */
    public static function uuid(string $data = null) : string {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Checks if a given URL exists.
     * @param string $url The URL to check.
     * @return bool
     */
    public static function urlExists(string $url) : bool {
        if (!self::startsWith('http', $url)) return false;
        $headers = get_headers($url);
        return (bool) stripos($headers[0], "200 OK");
    }

    /**
     * Compute the mean value.
     * @param int[]|float[] $values The values.
     * @return float|int
     */
    public static function mean(array $values) : int|float {
        return array_sum($values) / count($values);
    }

    /**
     * Print a text with potential space at the beginning.
     * @param string $text The text to print.
     * @param int $depth (optional) the deep
     * @param int $shelf (optional) the tab size
     * @return string
     */
    static public function code(string $text, int $depth = 0, int $shelf = 4) : string {
        return sprintf("%'. " . ($depth * $shelf) . "s%s", "", $text);
    }
}
?>
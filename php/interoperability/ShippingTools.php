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
     * @param IShippable $obj The object.
     * @return bool
     */
    public static function inArray(array $arr, IShippable $obj) : bool {
        foreach ($arr as $a) {
            if (get_class($a) === get_class($obj) &&
                ShipperRegister::getKey($a) === ShipperRegister::getKey($obj)) return true;
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
}
?>
<?php

namespace ship\interoperability;

/**
 * Enumeration DataType.
 *
 * <p>A list of data types that can be represented as integers.</p>
 *
 * @package ship
 * @subpackge php/interoperability
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
class DataType extends SEnumeration {

    /*
     * Defining constants for different data types.
     * WARNING: Do not change the values of the types as this may affect functionality.
     */
    public const STRING 	= 0;
    public const INTEGER 	= 1;
    public const FLOAT 	    = 2;
    public const DOUBLE     = 3;
    public const BOOLEAN    = 4;
    public const OBJECT 	= 5;
    public const VECTOR     = 6;
    public const MATRIX     = 7;
    public const ARRAYS     = 8;
    public const DATAFRAME  = 9;
    public const LISTS      = 10;
    public const FACTOR     = 11;
    public const FUNCTIONS  = 12;
    public const DATALIST   = 13;

    public const TYPE = [
        0   =>  'STRING',
        1   =>  'INTEGER',
        2   =>  'FLOAT',
        3   =>  'DOUBLE',
        4   =>  'BOOLEAN',
        5   =>  'OBJECT',
        6   =>  'VECTOR',
        7   =>  'MATRIX',
        8   =>  'ARRAYS',
        9   =>  'DATAFRAME',
        10  =>  'LISTS',
        11  =>  'FACTOR',
        12  =>  'FUNCTIONS',
        13  =>  'DATALIST'
    ];

    public const TYPE_ID = [
        'STRING' 	=> 0,
        'INTEGER' 	=> 1,
        'FLOAT' 	=> 2,
        'DOUBLE'    => 3,
        'BOOLEAN'   => 4,
        'OBJECT' 	=> 5,
        'VECTOR'    => 6,
        'MATRIX'    => 7,
        'ARRAYS'    => 8,
        'DATAFRAME' => 9,
        'LISTS'     => 10,
        'FACTOR'    => 11,
        'FUNCTIONS' => 12,
        'DATALIST'  => 13
    ];

    public const TITLES = [
        0   =>  'STRING',
        1   =>  'INTEGER',
        2   =>  'FLOAT',
        3   =>  'DOUBLE',
        4   =>  'BOOLEAN',
        5   =>  'OBJECT',
        6   =>  'VECTOR',
        7   =>  'MATRIX',
        8   =>  'ARRAYS',
        9   =>  'DATAFRAME',
        10  =>  'LISTS',
        11  =>  'FACTOR',
        12  =>  'FUNCTIONS',
        13  =>  'DATALIST'
    ];

    /**
     * @inheritDoc
     */
    public static function getValues() : array {
        return self::TYPE_ID;
    }

    /**
     * @inheritDoc
     */
    public static function getNames() : array {
        return self::TITLES;
    }
}
?>
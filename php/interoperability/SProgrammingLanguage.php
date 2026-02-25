<?php

namespace ship\interoperability;

/**
 * Enumeration SProgrammingLanguage.
 *
 * <p>An enumeration of different programming languages to exchange code correctly.</p>
 *
 * @package ship
 * @subpackge php/interoperability
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.2.0
 */
class SProgrammingLanguage extends SEnumeration {

    /*
     * Define constants for different programming languages.
     * WARNING: Do not change the values of the programming languages as they
     * 			can be used as index, e.g, in the database.
     */
    public const R        = 0;
    public const PYTHON   = 1;
    public const JS       = 2;
    public const PHP      = 3;
    public const LATEX    = 4;
    public const JAVA     = 5;
    public const HTML     = 6;
    public const CSS      = 7;
    public const R_SYS    = 8;
    public const VERIFIER = 9;
    public const CSV      = 10;

    public const PROGRAMMING_LANGUAGE = [
        self::R          =>  'R',
        self::PYTHON     =>  'PYTHON',
        self::JS         =>  'JS',
        self::PHP        =>  'PHP',
        self::LATEX      =>  'LATEX',
        self::JAVA       =>  'JAVA',
        self::HTML       =>  'HTML',
        self::CSS        =>  'CSS',
        self::R_SYS      =>  'R_SYS',
        self::VERIFIER   =>  'VERIFIER',
        self::CSV        =>  'CSV'
    ];

    public const PROGRAMMING_LANGUAGE_ID = [
        'R'	        => self::R,
        'PYTHON' 	=> self::PYTHON,
        'JS'	    => self::JS,
        'PHP'       => self::PHP,
        'LATEX'     => self::LATEX,
        'JAVA'	    => self::JAVA,
        'HTML'      => self::HTML,
        'CSS'       => self::CSS,
        'R_SYS'     => self::R_SYS,
        'VERIFIER'  => self::VERIFIER,
        'CSV'       => self::CSV
    ];

    public const TITLES = [
        self::R          =>  'R',
        self::PYTHON     =>  'Python',
        self::JS         =>  'JS',
        self::PHP        =>  'PHP',
        self::LATEX      =>  'LaTeX',
        self::JAVA       =>  'Java',
        self::HTML       =>  'HTML',
        self::CSS        =>  'CSS',
        self::R_SYS      =>  'R_SYS',
        self::VERIFIER   =>  'VERIFIER',
        self::CSV        =>  'CSV'
    ];

    /**
     * @inheritDoc
     */
    public static function getValues() : array {
        return self::PROGRAMMING_LANGUAGE_ID;
    }

    /**
     * @inheritDoc
     */
    public static function getNames() : array {
        return self::TITLES;
    }
}
?>
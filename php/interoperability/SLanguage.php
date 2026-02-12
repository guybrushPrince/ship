<?php

namespace ship\interoperability;

/**
 * Enumeration SLanguage.
 *
 * <p>An application may have multiple language support. This enumeration provides constants for different
 * languages.</p>
 *
 * @package ship
 * @subpackge php/interoperability
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.2.0
 */
class SLanguage {

    const GERMAN 	= 0;
    const ENGLISH 	= 1;
    const SPANISH 	= 2;
    const FRENCH 	= 3;

    public const LANGUAGE = [
        self::GERMAN  => 'GERMAN',
        self::ENGLISH => 'ENGLISH',
        self::SPANISH => 'SPANISH',
        self::FRENCH  => 'FRENCH'
    ];

    public const LANGUAGE_ID = [
        'GERMAN'  => self::GERMAN,
        'ENGLISH' => self::ENGLISH,
        'SPANISH' => self::SPANISH,
        'FRENCH'  => self::FRENCH
    ];

    public const NICE_TITLES = [
        self::GERMAN  => 'Deutsch',
        self::ENGLISH => 'English',
        self::SPANISH => 'Espa&ntilde;ola',
        self::FRENCH  => 'Fran&ccedil;aise'
    ];

    public const BCP47 = [
        self::GERMAN  => 'de',
        self::ENGLISH => 'en',
        self::SPANISH => 'es',
        self::FRENCH  => 'fr'
    ];
}
?>
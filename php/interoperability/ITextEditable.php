<?php

namespace ship\interoperability;

use Exception;
use ship\protocols\ModifyProtocol;
use ship\views\View;

/**
 * Interface ITextEditable.
 *
 * <p>Defines UI functions that can be called if a text is changed.</p>
 *
 * @package ship
 * @subpackage php/interoperability
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
interface ITextEditable {

    /**
     * Get the change protocol in case of any text access.
     * @param string $field The field that is accessed.
     * @param int $language The language.
     * @param View $view The view triggering the change.
     * @return ModifyProtocol
     */
    public function getChangeProtocol(string $field, int $language, View $view) : ModifyProtocol;

    /**
     * Create linkable text.
     * @param string $field The field.
     * @param int $language The language.
     * @param array|string|int|bool $params Additional specific parameters.
     * @param string|null $text The text (if available).
     * @return string
     * @throws Exception
     */
    public function createLinkableText(string $field, int $language = SLanguage::GERMAN,
                                       array|string|int|bool $params = [],
                                       ?string $text = null) : string;
}
?>
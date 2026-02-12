<?php

namespace ship\views;

use ship\interoperability\ShippingTools;
use ship\protocols\EventProtocol;
use ship\protocols\ModifyProtocol;

/**
 * Class TextView.
 *
 * @package ship
 * @subpackge php/model
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.0.0
 */
class TextView extends View {

    /**
     * The texts in an associative array (indexed by the language).
     * @var string[]
     */
    protected array $texts = [];

    /**
     * @inheritDoc
     */
    public function generate(bool $complete = false, bool $templates = false, bool $force = false) : string {
        if (array_key_exists(TextView::$language, $this->texts)) {
            return $this->texts[TextView::$language];
        } else {
            return '';
        }
    }

    /**
     * @inheritDoc
     */
    public function add(View $view) : View {
        return $this;
    }

    /**
     * Add a text.
     * @param int $language The language.
     * @param string $text The text.
     * @return void
     */
    public function addText(int $language, string $text) : void {
        $this->texts[$language] = $text;
    }

    //////////////////////////
    // Shortcuts
    //////////////////////////

    /**
     * @param int $language The language.
     * @param string|null $text The text.
     * @return $this
     * @see TextView->addText
     */
    public function text(int $language, ?string $text) : static {
        if ($text === null) $text = '';
        $this->addText($language, ShippingTools::maskTypical($text));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function protocol(int $event, array $params = []) : ?ModifyProtocol {
        if (self::isImplementation($this->parent())) {
            $implClass = EventProtocol::getImplementation($this->parent);
            if (is_null($implClass)) return null;
            $pro = new $implClass($event, $this, $params);
            $this->protocols[] = $pro;
            return $pro->modify();
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function firstProtocol(int $event, array $params = []) : ?ModifyProtocol {
        if (self::isImplementation($this->parent())) {
            $implClass = EventProtocol::getImplementation($this->parent);
            if (is_null($implClass)) return null;
            $pro = new $implClass($event, $this, $params);
            array_unshift($this->protocols, $pro);
            return $pro->modify();
        } else {
            return null;
        }
    }

    /**
     * Return a new text view.
     * @return TextView
     */
    public static function _new() : TextView {
        return new TextView();
    }

    /**
     * Return the id class for html.
     * @return string
     */
    public function idc() : string {
        if (!$this->uniqueId) $this->uniqueId = $this->generateId();
        return '.' . $this->id();
    }
}
?>
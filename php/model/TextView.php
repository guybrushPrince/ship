<?php

include_once dirname(__FILE__) . '/View.php';

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
    public function generate($complete = false, $templates = false, $force = false) : string {
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
     * @see TextView->addText
     * @param int $language The language.
     * @param string|null $text The text.
     * @return $this
     */
    public function text(int $language, ?string $text) : static {
        if ($text === null) $text = '';
        $this->addText($language, Tools::maskTypical($text));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function protocol(int $event, array $params = []) : ?ModifyProtocol {
        if ($this->parent() instanceof HtmlView) {
            $pro = new HtmlEventProtocol($event, $this, $params);
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
        if ($this->parent() instanceof HtmlView) {
            $pro = new HtmlEventProtocol($event, $this, $params);
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
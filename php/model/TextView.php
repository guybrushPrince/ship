<?php

include_once dirname(__FILE__) . '/View.php';

/**
 * Class TextView
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class TextView extends View {

    /**
     * The texts in an associative array.
     * @var string[]
     */
    protected array $texts = array();

    /**
     * @inheritDoc
     */
    public function generate($complete = false, $templates = false) : string {
        if (array_key_exists(TextView::$language, $this->texts)) {
            return $this->texts[TextView::$language];
        } else {
            return '';
        }
    }

    /**
     * @inheritDoc
     */
    public function add(View $view) : View { return $this; }

    /**
     * Add a text.
     * @param int $language The language.
     * @param string $text The text.
     */
    public function addText(int $language, string $text) {
        $this->texts[$language] = $text;
    }

    // Shortcuts

    /**
     * @see TextView->addText
     * @param int $language The language.
     * @param string $text The text.
     * @return TextView
     */
    public function text(int $language, string $text) {
        $this->addText($language, Tools::maskTypical($text));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function protocol(int $event, array $params = []) {
        if ($this->parent() instanceof HtmlView) {
            $pro = new HtmlEventProtocol($event, $this, $params);
            $this->protocols[] = $pro;
            $pro = $pro->modify();
            return $pro;
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function firstProtocol(int $event, array $params = []) {
        if ($this->parent() instanceof HtmlView) {
            $pro = new HtmlEventProtocol($event, $this, $params);
            array_unshift($this->protocols, $pro);
            $pro = $pro->modify();
            return $pro;
        } else {
            return null;
        }
    }

    /**
     * Return a new text view.
     * @return TextView
     */
    public static function _new() {
        return new TextView();
    }

    /**
     * Return the id class for html.
     * @return string
     */
    public function idc() {
        if (!$this->uniqueId) $this->uniqueId = $this->generateId();
        return '.' . $this->id();
    }

}
?>
<?php

include_once dirname(__FILE__) . '/View.php';

/**
 * Class TextView
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class TextView extends View {

    /**
     * The texts in an associative array.
     * @var array
     */
    protected $texts = array();

    /**
     * @inheritDoc
     */
    public function generate($complete = false, $templates = false) {
        if (array_key_exists(TextView::$language, $this->texts)) {
            return $this->texts[TextView::$language];
        } else {
            return '';
        }
    }

    /**
     * @inheritDoc
     */
    public function add(View $view) { }

    /**
     * Add a text.
     * @param integer $language The language.
     * @param string $text The text.
     */
    public function addText($language, $text) {
        $this->texts[$language] = $text;
    }

    // Short cuts

    /**
     * @see TextView->addText
     * @param integer $language The language.
     * @param string $text The text.
     * @return TextView
     */
    public function text($language, $text) {
        $this->addText($language, Tools::maskTypical($text));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function protocol($event, $delay = false) {
        if ($this->parent() instanceof HtmlView) {
            $prot = new HtmlEventProtocol($event, $this, $delay);
            $this->protocols[] = $prot;
            $prot = $prot->modify();
            return $prot;
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
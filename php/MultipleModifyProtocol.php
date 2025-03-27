<?php

/**
 * Class MultipleModifyProtocol.
 *
 * <p>This modify-protocol provides the same methods as a {@link ModifyProtocol}, however, it applies the same
 * functionality to a set of modify-protocols.</p>
 *
 * @package ship
 * @subpackage php
 *
 * @version 1.0.0
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class MultipleModifyProtocol extends ModifyProtocol {

    /**
     * The array of modify protocols.
     * @var ModifyProtocol[]
     */
    private array $multiProtocols;

    /**
     * Constructor.
     * @param Protocol|DirectView $parent The parent.
     * @param ModifyProtocol[] $protocols Some modifiy protocols.
     */
    public function __construct(Protocol|DirectView $parent, array $protocols) {
        parent::__construct($parent, '', '');
        $this->multiProtocols = $protocols;
    }

    /**
     * @inheritDoc
     */
    public function append($protocol) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->append($protocol);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function set(string $var, $new = false, $scope = false) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->set($var, $new, $scope);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCookie($new = false) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->setCookie($new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $var) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->get($var);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function add($view, $new = false) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->add($view, $new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addWithFallback($view, $fallback, $new = false) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->addWithFallback($view, $fallback, $new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function prepend($view, $new = false) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->prepend($view, $new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function text($view, $text = false) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->text($view, $text);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function value($view, $value = false) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->value($view, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resize($view) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->resize($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function makeEmpty($view) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->makeEmpty($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function disable($view) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->disable($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function focus($view) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->focus($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function reheight($view) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->reheight($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function reset($view) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->reset($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function after($view, $new = false) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->after($view, $new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function before($view, $new = false) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->before($view, $new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function replace($view, $new = false) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->replace($view, $new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function clear($view) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->clear($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addClass($view, $clazz) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->addClass($view, $clazz);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeClass($view, $clazz) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->removeClass($view, $clazz);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeClassSiblings($view, $clazz) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->removeClassSiblings($view, $clazz);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toggleClass($view, $clazz) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->toggleClass($view, $clazz);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete($view) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->delete($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function throwevent($event, $view, $data = false) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->throwevent($event, $view, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function throwObjectEvent($event, $object, $scopes = null, $data = null) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->throwObjectEvent($event, $object, $scopes, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function openWindow($url, $name, $protocol = null, $view = null) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->openWindow($url, $name, $protocol, $view);
        return $this;
    }

    public function modify() {
        parent::modify();
    }

    /**
     * @inheritDoc
     */
    public function action(string $controller, string $name, array $parameters = [], array $title = [
        Language::ENGLISH => 'New Task',
        Language::GERMAN => 'Neue Aktion'
    ], $seaStar = false) : ActionProtocol {
        $actions = [];
        foreach ($this->multiProtocols as $p) $actions[] = $p->action($controller, $name, $parameters, $title, $seaStar);
        return new MultipleActionProtocol($this->parent, $actions);
    }

    /**
     * @inheritDoc
     */
    public function alert(array $texts = [], ?array $options = null) : DialogProtocol {
        $dialogs = [];
        foreach ($this->multiProtocols as $p) $dialogs[] = $p->alert($texts, $options);
        return new MultipleDialogProtocol($this, $dialogs);
    }

    /**
     * @inheritDoc
     */
    public function event(int $event, $view) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->event($event, $view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function mousePosition($view) : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->mousePosition($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function reload() : static {
        foreach ($this->multiProtocols as $p) $p->reload();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function function ($code) : static {
        foreach ($this->multiProtocols as $p) $p->function($code);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function scrollTop(DirectView|string|TemplateObject $view): static {
        foreach ($this->multiProtocols as $p) $p->scrollTop($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function doIt(string $name, array $context = [], string $environment = 'global') : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->doIt($name, $context, $environment);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function gc() : ModifyProtocol {
        foreach ($this->multiProtocols as $p) $p->gc();
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function detectView($view) {
        foreach ($this->multiProtocols as $p) $p->detectView($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parent() {
        return $this->parent;
    }


    /**
     * @inheritDoc
     */
    public function generate() : string {
        return '';
    }
}
?>
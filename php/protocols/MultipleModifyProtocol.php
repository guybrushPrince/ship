<?php

namespace ship\protocols;

use Closure;
use ReflectionException;
use ship\interoperability\IShippable;
use ship\interoperability\SLanguage as Language;
use ship\template\TemplateObject;
use ship\views\DirectView;

/**
 * Protocol MultipleModifyProtocol.
 *
 * <p>This modify-protocol provides the same methods as a {@link ModifyProtocol}, however, it applies the same
 * functionality to a set of modify-protocols.</p>
 *
 * @see ModifyProtocol
 * @package ship
 * @subpackage php/protocols
 *
 * @version 1.0.0
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 *
 * @protocol
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
    public function append($protocol) : static {
        foreach ($this->multiProtocols as $p) $p->append($protocol);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function set(string $var, $new = false, $scope = false) : static {
        foreach ($this->multiProtocols as $p) $p->set($var, $new, $scope);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCookie(?string $new = null) : static {
        foreach ($this->multiProtocols as $p) $p->setCookie($new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $var) : static {
        foreach ($this->multiProtocols as $p) $p->get($var);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function add(DirectView|TemplateObject|array|string $view,
                        DirectView|TemplateObject|string|null|false $new = false) : static {
        foreach ($this->multiProtocols as $p) $p->add($view, $new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addWithFallback(DirectView|TemplateObject|array|string $view,
                                    DirectView|TemplateObject|array|string $fallback,
                                    DirectView|TemplateObject|string|null|false $new = false) : static {
        foreach ($this->multiProtocols as $p) $p->addWithFallback($view, $fallback, $new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function prepend(DirectView|TemplateObject|array|string $view,
                            DirectView|TemplateObject|string|null|false $new = false) : static {
        foreach ($this->multiProtocols as $p) $p->prepend($view, $new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function text(DirectView|TemplateObject|array|string $view,
                         Closure|string|null $text = null) : static {
        foreach ($this->multiProtocols as $p) $p->text($view, $text);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function value(DirectView|TemplateObject|array|string $view,
                          IShippable|TemplateObject|string|bool|int|float|null $value = null) : static {
        foreach ($this->multiProtocols as $p) $p->value($view, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resize(DirectView|TemplateObject|string $view) : static {
        foreach ($this->multiProtocols as $p) $p->resize($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function makeEmpty(DirectView|TemplateObject|array|string $view) : static {
        foreach ($this->multiProtocols as $p) $p->makeEmpty($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function enable(DirectView|TemplateObject|array|string $view) : static {
        foreach ($this->multiProtocols as $p) $p->enable($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function disable(DirectView|TemplateObject|array|string $view) : static {
        foreach ($this->multiProtocols as $p) $p->disable($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function focus(DirectView|TemplateObject|array|string $view) : static {
        foreach ($this->multiProtocols as $p) $p->focus($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function reheight(DirectView|TemplateObject|array|string $view) : static {
        foreach ($this->multiProtocols as $p) $p->reheight($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function reset(DirectView|TemplateObject|array|string $view) : static {
        foreach ($this->multiProtocols as $p) $p->reset($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function after(DirectView|TemplateObject|array|string $view,
                          DirectView|TemplateObject|string|null|false $new = false) : static {
        foreach ($this->multiProtocols as $p) $p->after($view, $new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function before(DirectView|TemplateObject|array|string $view,
                           DirectView|TemplateObject|string|null|false $new = false) : static {
        foreach ($this->multiProtocols as $p) $p->before($view, $new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function replace(DirectView|TemplateObject|array|string $view,
                            DirectView|TemplateObject|string|null|false $new = false) : static {
        foreach ($this->multiProtocols as $p) $p->replace($view, $new);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function clear(DirectView|TemplateObject|array|string $view) : static {
        foreach ($this->multiProtocols as $p) $p->clear($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addClass(DirectView|TemplateObject|array|string $view,
                             TemplateObject|array|string $clazz) : static {
        foreach ($this->multiProtocols as $p) $p->addClass($view, $clazz);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeClass(DirectView|TemplateObject|array|string $view,
                                TemplateObject|array|string $clazz) : static {
        foreach ($this->multiProtocols as $p) $p->removeClass($view, $clazz);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeClassSiblings(DirectView|TemplateObject|array|string $view,
                                        TemplateObject|array|string $clazz,
                                        TemplateObject|bool|null $onlyChildren = false) : static {
        foreach ($this->multiProtocols as $p) $p->removeClassSiblings($view, $clazz);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toggleClass(DirectView|TemplateObject|array|string|null $view,
                                TemplateObject|array|string $clazz) : static {
        foreach ($this->multiProtocols as $p) $p->toggleClass($view, $clazz);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(DirectView|TemplateObject|array|string $view) : static {
        foreach ($this->multiProtocols as $p) $p->delete($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function throwevent(TemplateObject|int $event, DirectView|TemplateObject|array|string $view,
                               TemplateObject|array|null $data = null) : static {
        foreach ($this->multiProtocols as $p) $p->throwevent($event, $view, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function throwObjectEvent(TemplateObject|int $event, TemplateObject|IShippable|array $object,
                                     TemplateObject|array|string|null $scopes = null,
                                     TemplateObject|array|null $data = null) : static {
        foreach ($this->multiProtocols as $p) $p->throwObjectEvent($event, $object, $scopes, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function broadcastEvent(TemplateObject|int $event, TemplateObject|array|string|null $scopes = null,
                                   TemplateObject|array|null $data = null) : static {
        foreach ($this->multiProtocols as $p) $p->broadcastEvent($event, $scopes, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function openWindow(TemplateObject|string $url, TemplateObject|string $name,
                               ?TemplateObject $protocol = null,
                               DirectView|TemplateObject|array|string|null $view = null) : static {
        foreach ($this->multiProtocols as $p) $p->openWindow($url, $name, $protocol, $view);
        return $this;
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function action(string $controller, string $name, array $parameters = [], array $title = [
        Language::ENGLISH => 'New Task',
        Language::GERMAN => 'Neue Aktion'
    ], int|bool $seaStar = false) : ActionProtocol {
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
    public function event(TemplateObject|int $event, DirectView|TemplateObject|array|string $view) : static {
        foreach ($this->multiProtocols as $p) $p->event($event, $view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function mousePosition(DirectView|TemplateObject|array|string $view) : static {
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
    public function function (TemplateObject|string $code) : static {
        foreach ($this->multiProtocols as $p) $p->function($code);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function scrollTop(DirectView|TemplateObject|array|string $view) : static {
        foreach ($this->multiProtocols as $p) $p->scrollTop($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function doIt(string $name, array $context = [], string $environment = 'global') : static {
        foreach ($this->multiProtocols as $p) $p->doIt($name, $context, $environment);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function gc() : static {
        foreach ($this->multiProtocols as $p) $p->gc();
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function detectView(DirectView|TemplateObject|IShippable|array|string|null|false $view) :
        DirectView|array|string|TemplateObject|ModifyProtocol|null|false {
        foreach ($this->multiProtocols as $p) $p->detectView($view);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parent() : DirectView|Protocol|null {
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
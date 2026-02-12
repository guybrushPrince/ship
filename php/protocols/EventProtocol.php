<?php

namespace ship\protocols;

use ship\views\DirectView;
use ship\views\View;

include_once dirname(__FILE__) . '/Protocol.php';
include_once dirname(__FILE__) . '/ModifyProtocol.php';

/**
 * Protocol EventProtocol.
 *
 * <p>An event {@link Protocol} allowing for reacting to events.</p>
 *
 * @see Protocol
 * @package ship
 * @subpackge php/protocols
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.2.0
 *
 * @protocol
 */
abstract class EventProtocol extends Protocol {

    /**
     * Stores implementations of the
     * @var array
     */
    private static array $implementations = [];

    /**
     * The event, for which the modify protocol is applied.
     * @var int
     */
    protected int $event;

    /**
     * The class of the implementation of the modify abstract class.
     * @var string
     */
    private string $implementation;

    /**
     * @inheritDoc
     */
    public function __construct(int $event, DirectView $parent, string $implementation) {
        parent::__construct($parent);
        $this->event = $event;
        $this->implementation = $implementation;
    }

    /**
     * Generate a modify branch.
     * @return ModifyProtocol
     */
    public function modify() : ModifyProtocol {
        $this->protocol[] = array(
            "operation" => "modify"
        );
        $impl = $this->implementation;
        return new $impl($this);
    }

    /**
     * Registers an implementation of {@link EventProtocol} related to an implementation of {@link View}.
     * @param string $implClass The class of the implementation.
     * @param string $implView The class of the view implementation.
     * @return void
     */
    public static function registerImplementation(string $implClass, string $implView) : void {
        self::$implementations[$implView] = $implClass;
    }

    /**
     * Get the {@link EventProtocol} implementation for a given {@link View} implementation (or instance).
     * @param View|string $implView The view.
     * @return string|null
     */
    public static function getImplementation(View|string $implView) : string|null {
        if (is_string($implView)) {
            if (array_key_exists($implView, self::$implementations)) return self::$implementations[$implView];
            else return null;
        }
        foreach (self::$implementations as $iV => $implementation) {
            if ($implView instanceof $iV) return $implementation;
        }
        return null;
    }
}
?>
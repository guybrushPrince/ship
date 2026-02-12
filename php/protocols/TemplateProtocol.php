<?php

namespace ship\protocols;

use Exception;
use ship\views\DirectView;

include_once dirname(__FILE__) . '/Protocol.php';
include_once dirname(__FILE__) . '/ModifyProtocol.php';

/**
 * Protocol TemplateProtocol.
 *
 * <p>A {@link TemplateProtocol} is similar to a function defining a chain of protocols and can
 * be invoked multiple times with {@link ModifyProtocol::doIt()} without the necessity of
 * multiple code generation.</p>
 *
 * <p>A {@link TemplateProtocol} is specified by a <i>name</i>, an <i>environment</i> where this
 * name is valid, and some protocols.</p>
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
abstract class TemplateProtocol extends Protocol {

    /**
     * An array of template protocols.
     * @var TemplateProtocol[]
     */
    private static array $templates = [];

    /**
     * The class name of the implementation of this protocol.
     * @var string
     */
    private string $implementation;

    /**
     * The name of the template protocol.
     * @var string
     */
    protected string $name;

    /**
     * The environment of the template protocol (something as a namespace).
     * @var string
     */
    protected string $environment;

    /**
     * Whether the template is used, or not.
     * @var bool
     */
    protected bool $inUse = false;

    /**
     * The template was already sent to the client (i.e., it is already generated).
     * @var bool
     */
    protected bool $delivered = false;

    /**
     * TemplateProtocol constructor.
     * @param string $name The name of the template/event.
     * @param string $implementation The class name of the implementation.
     * @param string $environment The name of the environment.
     */
    public function __construct(string $name, string $implementation, string $environment = 'global') {
        parent::__construct(null);
        $this->implementation = $implementation;
        $this->name = $name;
        $this->environment = $environment;
        TemplateProtocol::$templates[$this->getCallName()] = $this;
    }

    /**
     * Return the calling name (with environment), i.e., under which name the template is invoked.
     * @return string
     */
    public function getCallName() : string {
        return TemplateProtocol::getCallNameFunc($this->name, $this->environment);
    }

    /**
     * @inheritDoc
     */
    public function generate(array $context = [], DirectView|Protocol|null $entryView = null) : string {
        $this->inUse = true;
        return $this->createThrower($context, $entryView);
    }

    /**
     * Create the code to throw/invoke the template.
     * @param array $context The calling context.
     * @param DirectView|Protocol|null $entryView The entry / context view.
     * @return string
     */
    public abstract function createThrower(array $context = array(),
                                           DirectView|Protocol|null $entryView = null) : string;

    /**
     * Create code for the template (alias the function).
     * @return string
     */
    public function listener() : string {
        if (!$this->delivered) {
            $this->delivered = true;
            return $this->createListener();
        } else {
            return '';
        }
    }

    /**
     * Create the code for the template (alias the function).
     * @return string
     */
    protected abstract function createListener() : string;

    /**
     * Return the calling name (with environment).
     * @param string $name The name of the template.
     * @param string $environment The name of the environment.
     * @return string
     */
    private static function getCallNameFunc(string $name, string $environment = 'global') : string {
        return $environment . '_' . $name;
    }

    /**
     * Generates the code for all the templates.
     * @param array $context Some storage context (sent by reference).
     * @return string
     */
    public static function generateAll(array &$context = []) : string {
        return implode('', array_map(function (TemplateProtocol $protocol) use (&$context) {
            if (!in_array($protocol->getCallName(), $context)) {
                $context[] = $protocol->getCallName();
                return $protocol->listener();
            } else return '';
        }, TemplateProtocol::$templates));
    }

    /**
     * Generate a modify branch.
     * @return ModifyProtocol
     */
    public function modify() : ModifyProtocol {
        $impl = $this->implementation;
        $instance = new $impl($this);
        $this->protocol[] = array(
            "operation" => "modify",
            "protocol" => $instance
        );
        return $instance;
    }

    /**
     * Get a template protocol implementation given by the name and environment.
     * @param string $name The name.
     * @param string $environment The environment.
     * @return TemplateProtocol
     * @throws Exception
     */
    public static function get(string $name, string $environment = 'global') : TemplateProtocol {
        $callName = TemplateProtocol::getCallNameFunc($name, $environment);
        if (!array_key_exists($callName, TemplateProtocol::$templates)) {
            throw new Exception('Unknown template protocol: ' . $callName);
        }
        return TemplateProtocol::$templates[$callName];
    }
}
?>
<?php

include_once dirname(__FILE__) . '/Protocol.php';
include_once dirname(__FILE__) . '/ModifyProtocol.php';

/**
 * Class TemplateProtocol
 * Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class TemplateProtocol extends Protocol {

    /**
     * An array of template protocols.
     * @var array
     */
    private static $templates = array();

    /**
     * The class of the implementation of the modify abstract class.
     * @var string
     */
    private $implementation;

    /**
     * The name of the template protocol.
     * @var string
     */
    protected $name;

    /**
     * The environment of the template protocol.
     * @var string
     */
    protected $environment;

    /**
     * Whether the template is used or not.
     * @var bool
     */
    protected $inUse = false;

    /**
     * The template was already sent to the client.
     * @var bool
     */
    protected $delivered = false;

    /**
     * TemplateProtocol constructor.
     * @param string $name The name of the template/event.
     * @param string $implementation The implementation.
     * @param string $environment The name of the environment.
     */
    public function __construct($name, $implementation, $environment) {
        parent::__construct(null);
        $this->implementation = $implementation;
        $this->name = $name;
        $this->environment = $environment;
        TemplateProtocol::$templates[$this->getCallName()] = $this;
    }

    /**
     * Return the calling name (with environment).
     * @return string
     */
    public function getCallName() {
        return TemplateProtocol::getCallNameFunc($this->name, $this->environment);
    }

    /**
     * @inheritDoc
     */
    public function generate($context = array(), $entryView = null) {
        $this->inUse = true;
        return $this->createThrower($context, $entryView);
    }

    /**
     * Create the code to throw the event / template.
     * @param array $context The calling context.
     * @param DirectView $entryView The entry / context view.
     * @return string
     */
    public abstract function createThrower(array $context = array(), DirectView $entryView = null);

    /**
     * Create code for the template (alias the function).
     * @return string
     */
    public function listener() {
        if (!$this->delivered) {
            $this->delivered = true;
            return $this->createListener();
        } else {
            return '';
        }
    }

    /**
     * Create the code for execution after event / template.
     * @return string
     */
    protected abstract function createListener();

    /**
     * Return the calling name (with environemt).
     * @param string $name The name of the template/event.
     * @param string $environment The name of the environment.
     * @return string
     */
    private static function getCallNameFunc($name, $environment = 'global') {
        return $environment . '_' . $name;
    }

    /**
     * Generates the code for all the templates.
     * @param array $context Some storage context.
     * @return string
     */
    public static function generateAll(array &$context = array()) {
        return implode('', array_map(function(TemplateProtocol $protocol) use (&$context) {
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
    public function modify() {
        $impl = $this->implementation;
        $instance = new $impl($this);
        $this->protocol[] = array(
            "operation" => "modify",
            "protocol" => $instance
        );
        return $instance;
    }

    /**
     * Get the template protocol.
     * @param string $name The name.
     * @param string $environment The environment.
     * @return mixed
     */
    public static function get($name, $environment = 'global') {
        $callName = TemplateProtocol::getCallNameFunc($name, $environment);
        if (!array_key_exists($callName, TemplateProtocol::$templates))
            DebugLogger::instance()->debug($callName, array_keys(TemplateProtocol::$templates));
        return TemplateProtocol::$templates[$callName];
    }
}
?>
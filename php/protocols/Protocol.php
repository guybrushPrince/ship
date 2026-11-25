<?php

namespace ship\protocols;

use ship\interoperability\ShippingTools;
use ship\template\AShipFunction;
use ship\template\TemplateObject;
use ship\views\DirectView;

/**
 * Class Protocol.
 *
 * <p>The core of each {@link Protocol}.</p>
 *
 * @package ship
 * @subpackge php/protocols.
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.1.0
 *
 * @protocol
 */
abstract class Protocol {

    /**
     * The protocol(s)/action(s) to be performed.
     * @var array
     */
    protected array $protocol = [];

    /**
     * The parent protocol or parent view this protocol belongs to.
     * @var Protocol|DirectView|null
     */
    protected Protocol|DirectView|null $parent = null;

    /**
     * The children protocols.
     * @var Protocol[]
     */
    protected array $children = [];

    /**
     * An array of used template objects within the protocol.
     * @var TemplateObject[]
     */
    protected array $templateObjects = [];

    /**
     * An array of all collected template objects (recursively). (Just for computation!)
     * @var array|null
     */
    private ?array $unsetParameters = null;

    /**
     * Constructor.
     * @param Protocol|DirectView|null $parent The parent protocol or view.
     */
    public function __construct(Protocol|DirectView|null $parent) {
        if ($parent instanceof Protocol) {
            $parent->children[] = $this;
        }
        $this->parent = $parent;
    }

    /**
     * Generates the code for the protocol.
     * @return string
     */
    public abstract function generate() : string;

    /**
     * Sets the parent protocol or view.
     * @param DirectView|Protocol $parent The parent.
     * @return void
     */
    protected function setParent(DirectView|Protocol $parent) : void {
        $this->parent = $parent;
    }

    /**
     * Get the template objects.
     * @return TemplateObject[]
     */
    public function getTemplateObjects() : array {
        return $this->templateObjects;
    }

    /**
     * Set the template objects.
     * @param TemplateObject[] $templateObjects The template objects.
     * @return void
     */
    public function setTemplateObjects(array $templateObjects) : void {
        $this->templateObjects = $templateObjects;
    }

    /**
     * Add a template object.
     * @param TemplateObject $object The template object.
     * @return void
     */
    public function addTemplateObject(TemplateObject $object) : void {
        $this->templateObjects[] = $object;
    }

    /**
     * Collects the parameter templates with the protocol (and its children).
     * @return array
     */
    public function collectParameters() : array {
        if (!$this->unsetParameters) {
            $this->unsetParameters = ShippingTools::unique(
                array_merge(
                    $this->getTemplateObjects(),
                    $this->collectParametersInArray($this->protocol)
                )
            );
        }
        return $this->unsetParameters;
    }

    /**
     * Collect the parameters within an array recursively.
     * @param array $parameters The protocol representation.
     * @return array
     */
    private function collectParametersInArray(array $parameters) : array {
        return ShippingTools::flatten(
            array_filter(
                array_map(function ($param) {
                    if ($param instanceof TemplateObject) return $param;
                    else if ($param instanceof AShipFunction) return $param;
                    else if (is_array($param)) return $this->collectParametersInArray($param);
                    else if ($param instanceof Protocol) return $param->collectParameters();
                    return false;
                }, $parameters),
                function ($param) {
                    return (bool)$param;
                }
            )
        );
    }


    //////////////////////////
    // Shortcuts
    //////////////////////////

    /**
     * Get the parent.
     * @return Protocol|DirectView|null
     */
    public function parent() : Protocol|DirectView|null {
        return $this->parent;
    }

    /**
     * Determine the view that is connected to this protocol (if any).
     * @return DirectView|null
     */
    public function innerView() : ?DirectView {
        if ($this->parent instanceof DirectView) {
            return $this->parent;
        } else if ($this->parent === null) {
            return null;
        } else {
            return $this->parent->innerView();
        }
    }
}
?>
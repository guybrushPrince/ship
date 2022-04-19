<?php

/**
 * Class DialogProtocol
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class DialogProtocol extends Protocol {

    /**
     * The class of the implementation of the modify abstract class.
     * @var string
     */
    private string $implementation;

    /**
     * The options.
     * @var array
     */
    protected array $options;

    /**
     * The texts.
     * @var array
     */
    protected array $texts;

    /**
     * DialogProtocol constructor.
     * @param ModifyProtocol $parent The parent protocol.
     * @param array $texts The texts of the dialog.
     * @param array|null $options The available options.
     * @param string $implementation
     */
    public function __construct(
        ModifyProtocol $parent,
        array $texts,
        ?array $options = null,
        $implementation = "DialogProtocol"
    ) {
        parent::__construct($parent);
        $this->implementation = $implementation;
        if ($options === null) {
            $options = [
                1   =>  [
                    Language::ENGLISH   =>  "Yes",
                    Language::GERMAN    =>  "Ja"
                ],
                2   =>  [
                    Language::ENGLISH   =>  "No",
                    Language::GERMAN    =>  "Nein"
                ]
            ];
        }
        $this->options = $options;
        $this->texts = $texts;
    }

    /**
     * Generate a success branch.
     * @return ModifyProtocol
     */
    public function yes() : ModifyProtocol {
        $impl = $this->implementation;
        $obj = new $impl($this);
        $this->protocol[] = array(
            "operation" => "path",
            "protocol"  => $obj,
            "number"    => 1
        );
        return $obj;
    }

    /**
     * Generate an error branch.
     * @return ModifyProtocol
     */
    public function no() : ModifyProtocol {
        $impl = $this->implementation;
        $obj = new $impl($this);
        $this->protocol[] = array(
            "operation" => "no",
            "protocol"  => $obj,
            "number"    => 2
        );
        return $obj;
    }

    /**
     * Generate an error branch.
     * @param int $nr The option number.
     * @return ModifyProtocol
     */
    public function option(int $nr) : ModifyProtocol {
        $impl = $this->implementation;
        $obj = new $impl($this);
        $this->protocol[] = array(
            "operation" => "no",
            "protocol"  => $obj,
            "number"    => $nr
        );
        return $obj;
    }

}
?>
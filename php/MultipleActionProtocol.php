<?php

/**
 * Class MultipleActionProtocol.
 *
 * <p>This action protocol provides the same methods as an {@link ActionProtocol}, however, it applies the same
 * functionality to a set of action protocols.</p>
 *
 * @package ship
 * @subpackage php
 *
 * @version 1.0.0
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class MultipleActionProtocol extends ActionProtocol {

    /**
     * The array of action protocols.
     * @var ActionProtocol[]
     */
    private array $multiProtocols;

    /**
     * Constructor.
     * @param ModifyProtocol $parent The parent.
     * @param ModifyProtocol[] $protocols Some modifiy protocols.
     */
    public function __construct(ModifyProtocol $parent, array $protocols) {
        parent::__construct($parent, '', '', '');
        $this->multiProtocols = $protocols;
    }

    /**
     * @inheritDoc
     */
    public function success() {
        $modifies = [];
        foreach ($this->multiProtocols as $p) $modifies[] = $p->success();
        return new MultipleModifyProtocol($this, $modifies);
    }

    /**
     * @inheritDoc
     */
    public function error() {
        $modifies = [];
        foreach ($this->multiProtocols as $p) $modifies[] = $p->error();
        return new MultipleModifyProtocol($this, $modifies);
    }

    /**
     * @inheritDoc
     */
    public function eventually() {
        $modifies = [];
        foreach ($this->multiProtocols as $p) $modifies[] = $p->eventually();
        return new MultipleModifyProtocol($this, $modifies);
    }


    /**
     * @inheritDoc
     */
    public function generate() : string {
        return '';
    }
}
?>
<?php

/**
 * Class MultipleDialogProtocol.
 *
 * <p>This dialog protocol provides the same methods as a {@link DialogProtocol}, however, it applies the same
 * functionality to a set of dialog protocols.</p>
 *
 * @package ship
 * @subpackage php
 *
 * @version 1.0.0
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
class MultipleDialogProtocol extends DialogProtocol {

    /**
     * The array of dialog protocols.
     * @var DialogProtocol[]
     */
    private array $multiProtocols;

    /**
     * Constructor.
     * @param ModifyProtocol $parent The parent.
     * @param ModifyProtocol[] $protocols Some modifiy protocols.
     */
    public function __construct(ModifyProtocol $parent, array $protocols) {
        parent::__construct($parent, [], null);
        $this->multiProtocols = $protocols;
    }

    /**
     * @inheritDoc
     */
    public function yes() : ModifyProtocol {
        $modifies = [];
        foreach ($this->multiProtocols as $p) $modifies[] = $p->yes();
        return new MultipleModifyProtocol($this, $modifies);
    }

    /**
     * @inheritDoc
     */
    public function no() : ModifyProtocol {
        $modifies = [];
        foreach ($this->multiProtocols as $p) $modifies[] = $p->no();
        return new MultipleModifyProtocol($this, $modifies);
    }

    /**
     * @inheritDoc
     */
    public function option(int $nr) : ModifyProtocol {
        $modifies = [];
        foreach ($this->multiProtocols as $p) $modifies[] = $p->option($nr);
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
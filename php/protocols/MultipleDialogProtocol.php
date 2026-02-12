<?php

namespace ship\protocols;

/**
 * Protocol MultipleDialogProtocol.
 *
 * <p>This dialog protocol provides the same methods as a {@link DialogProtocol}, however, it applies the same
 * functionality to a set of dialog protocols.</p>
 *
 * @see DialogProtocol
 * @package ship
 * @subpackage php/protocols
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.2.0
 *
 * @protocol
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
     * @param ModifyProtocol[] $protocols Some modify protocols.
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
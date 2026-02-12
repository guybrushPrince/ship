<?php

namespace ship\protocols;

use ReflectionException;

/**
 * Protocol MultipleActionProtocol.
 *
 * <p>This action protocol provides the same methods as an {@link ActionProtocol}, however, it applies the same
 * functionality to a set of action protocols.</p>
 *
 * @see ActionProtocol
 * @package ship
 * @subpackage php/protocols
 *
 * @version 1.0.0
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 *
 * @protocol
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
     * @throws ReflectionException
     */
    public function __construct(ModifyProtocol $parent, array $protocols) {
        parent::__construct($parent, '', '', '');
        $this->multiProtocols = $protocols;
    }

    /**
     * @inheritDoc
     */
    public function success() : ModifyProtocol {
        $modifies = [];
        foreach ($this->multiProtocols as $p) $modifies[] = $p->success();
        return new MultipleModifyProtocol($this, $modifies);
    }

    /**
     * @inheritDoc
     */
    public function fail() : ModifyProtocol {
        $modifies = [];
        foreach ($this->multiProtocols as $p) $modifies[] = $p->fail();
        return new MultipleModifyProtocol($this, $modifies);
    }

    /**
     * @inheritDoc
     */
    public function error() : ModifyProtocol {
        $modifies = [];
        foreach ($this->multiProtocols as $p) $modifies[] = $p->error();
        return new MultipleModifyProtocol($this, $modifies);
    }

    /**
     * @inheritDoc
     */
    public function eventually() : ModifyProtocol {
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
<?php

namespace ship\views;

use ship\interoperability\IShippable;
use ship\interoperability\ShipperRegister;
use ship\protocols\ModifyProtocol;

/**
 * View DirectView.
 *
 * <p>The basic class representing views independently of any implementation.</p>
 *
 * @package ship
 * @subpackge php
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.2.0
 *
 * @view
 */
abstract class DirectView {

    public const VALUE_SCOPE = 'value';

    /**
     * A reference list with string-indexed pairs of ["scope", "view", "name"] objects.
     * @var array
     */
    protected array $references = [];

    /**
     * A list of assigned names.
     * @var array
     */
    protected array $names = [];

    /**
     * A list of objects and their assigned views.
     * @var DirectView[][]
     */
    protected array $objects = [];

    /**
     * An array with protocols.
     * @var array
     */
    protected array $protocols = [];

    /**
     * Adds an action to the view.
     * @param int $event The event.
     * @param array $params Some optional event parameters.
     * @return ModifyProtocol|null
     */
    public abstract function protocol(int $event, array $params = []) : ?ModifyProtocol;

    /**
     * Adds an action to the view at the first position.
     * @param int $event The event.
     * @param array $params Some optional event parameters.
     * @return ModifyProtocol|null
     */
    public abstract function firstProtocol(int $event, array $params = []) : ?ModifyProtocol;

    /**
     * The access is granted if the access is set.
     * @param string|string[] $access Access key(s).
     * @return $this
     */
    public abstract function access(string|array $access) : static;

    /**
     * Search a referenced view in the current view and return some information about it.
     * @param string $id The id.
     * @param bool $lastChance If this is the last change to find the view.
     * @return array|null
     */
    public function get(string $id, bool $lastChance = false) : ?array {
        if (array_key_exists($id, $this->references)) {
            return $this->references[$id];
        } else return null;
    }

    /**
     * Set a reference.
     * @param string|IShippable $id The id or an object from which the id shall be taken.
     * @param bool|string $scope The scope (value, attribute, or nothing)
     * @param DirectView|false $view The view to add if it is different from the current.
     * @return $this
     */
    public function ref(string|IShippable $id, bool|string $scope = false, DirectView|bool $view = false) : static {
        $this->references[$id] = [
            "view" => ($view ? $view : $this),
            "scope" => $scope,
            "name" => $id
        ];
        if (!$view) $this->names[] = $id;
        return $this;
    }

    /**
     * Pick all views that are known to be bounded to the given object.
     * @param IShippable $object The bounded object.
     * @return DirectView[]|null
     */
    public function pick(IShippable $object) : ?array {
        $id = self::getObjectId($object);
        if (array_key_exists($id, $this->objects)) {
            return $this->objects[$id];
        } else return null;
    }

    /**
     * Bind an object to a view.
     * @param IShippable $object The object.
     * @param int[]|int|null $event The event(s) to react on.
     * @param ModifyProtocol|null $protocol The protocol to execute on the event.
     * @param string|null $scope A possible scope for the event.
     * @param string[]|null $ignoreData Whether specific data fields shall be ignored, or not.
     * @param DirectView|null $view (internal).
     * @return $this
     */
    public function bind(IShippable $object, array|int|null $event = null, ?ModifyProtocol $protocol = null,
                         ?string $scope = null, ?array $ignoreData = null, ?DirectView $view = null) : static {
        $id = self::getObjectId($object);
        if (!array_key_exists($id, $this->objects)) $this->objects[$id] = [];
        $this->objects[$id][] = ($view ? $view : $this);
        return $this;
    }

    /**
     * Register or bind a view to an event (and scope) to listen to broadcast events.
     * @param int[]|int $event The event(s) to react on.
     * @param ModifyProtocol|null $protocol The protocol to execute on the event.
     * @param string|null $scope A possible scope for the event.
     * @param string[]|null $ignoreData Whether specific data fields shall be ignored, or not.
     * @param DirectView|null $view (internal).
     * @return $this
     */
    public function bindBroadcast(array|int $event, ?ModifyProtocol $protocol = null, ?string $scope = null,
                                  ?array $ignoreData = null, ?DirectView $view = null) : static {
        return $this;
    }

    /**
     * Get the reference names of this view.
     * @return string[]
     */
    public function getRefs() : array {
        return $this->names;
    }

    /**
     * Return the id of the object.
     * @param IShippable $object The object id.
     * @return string
     */
    protected static function getObjectId(IShippable $object) : string {
        return ShipperRegister::getClass($object) . ':' . ShipperRegister::getKey($object);
    }
}
?>
<?php

/**
 * Class DirectView
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class DirectView {

    /**
     * An reference list.
     * @var array
     */
    protected $references = array();

    /**
     * A list of assigned names.
     * @var array
     */
    protected $names = array();

    /**
     * A list of assigned objects.
     * @var array
     */
    protected $objects = [];

    /**
     * An array with protocols.
     * @var array
     */
    protected $protocols = array();

    /**
     * Adds an action to the view.
     * @param int $event The event.
     * @param array $params Some optional event parameters.
     * @return $this|ModifyProtocol
     */
    public abstract function protocol(int $event, array $params = []);

    /**
     * Adds a first action to the view.
     * @param int $event The event.
     * @param array $params Some optional event parameters.
     * @return mixed
     */
    public abstract function firstProtocol(int $event, array $params = []);

    /**
     * The access is granted if the access is set.
     * @param string|array $access
     */
    public abstract function access($access);

    /**
     * Get an object of the view.
     * @param string $id The id.
     * @return DirectView
     */
    public function get($id) {
        if (array_key_exists($id, $this->references)) {
            return $this->references[$id];
        } else return null;
    }

    /**
     * Set a reference.
     * @param string $id The id.
     * @param bool|string $scope The scope (value, attribute, or nothing)
     * @param View|false $view The view to add.
     * @return $this
     */
    public function ref(string $id, bool|string $scope = false, View|bool $view = false) : static {
        $this->references[$id] = array(
            "view"  => ($view ? $view : $this),
            "scope" => $scope,
            "name" => $id
        );
        if (!$view) $this->names[] = $id;
        return $this;
    }

    /**
     * Pick the view if it is bound to the given object.
     * @param $object
     * @return DirectView[]|null
     */
    public function pick($object) {
        $id = self::getObjectId($object);
        if (array_key_exists($id, $this->objects)) {
            return $this->objects[$id];
        } else return null;
    }

    /**
     * Bind an object to a view.
     * @param PersistentTrait $object The object.
     * @param int|null $event The event to react on.
     * @param ModifyProtocol|null $protocol The protocol to execute on the event.
     * @param string|null $scope A possible scope for the event.
     * @param DirectView|null $view (internal).
     * @return $this
     */
    public function bind($object, ?int $event = null, ?ModifyProtocol $protocol = null, ?string $scope = null,
                         ?DirectView $view = null) {
        $id = self::getObjectId($object);
        if (!array_key_exists($id, $this->objects)) $this->objects[$id] = [];
        $this->objects[$id][] = ($view ? $view : $this);
        return $this;
    }

    /**
     * Get the reference names of this view.
     * @return array
     */
    public function getRefs() {
        return $this->names;
    }

    /**
     * Return the id of the object.
     * @param PersistentTrait $object The object id.
     * @return string
     */
    protected static function getObjectId($object) {
        return get_class($object) . ':' . $object->getKey();
    }

}
?>
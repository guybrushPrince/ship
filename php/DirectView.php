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
     * @param boolean|string $scope The scope (value, attribute, or nothing)
     * @param View|false $view The view to add.
     * @return $this
     */
    public function ref($id, $scope = false, $view = false) {
        $this->references[$id] = array(
            "view"  => ($view ? $view : $this),
            "scope" => $scope
        );
        if (!$view) $this->names[] = $id;
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
     * @param CObject $object The object id.
     * @return string
     */
    protected static function getObjectId(CObject $object) {
        return get_class($object) . ':' . $object->getId();
    }

}
?>
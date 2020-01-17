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
     * An array with protocols.
     * @var array
     */
    protected $protocols = array();

    /**
     * Adds an action to the view.
     * @param integer $event The event.
     * @return $this
     */
    public abstract function protocol($event);

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
        return $this;
    }

}
?>
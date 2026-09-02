<?php
/**
 * Loader class - handles hook registration
 *
 * @package Pola_Wyboru
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Pola_Wyboru_Loader
 *
 * Registers hooks (actions and filters) for the plugin
 */
class Pola_Wyboru_Loader {

	/**
	 * Array of actions registered with WordPress
	 *
	 * @var array
	 */
	private $actions = array();

	/**
	 * Array of filters registered with WordPress
	 *
	 * @var array
	 */
	private $filters = array();

	/**
	 * Add action hook
	 *
	 * @param string   $hook Hook name.
	 * @param object   $component Component object.
	 * @param string   $callback Callback method name.
	 * @param int      $priority Hook priority.
	 * @param int      $accepted_args Number of arguments.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add_hook( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add filter hook
	 *
	 * @param string   $hook Hook name.
	 * @param object   $component Component object.
	 * @param string   $callback Callback method name.
	 * @param int      $priority Hook priority.
	 * @param int      $accepted_args Number of arguments.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add_hook( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Generic hook registration
	 *
	 * @param array    $hooks Current hooks array.
	 * @param string   $hook Hook name.
	 * @param object   $component Component object.
	 * @param string   $callback Callback method name.
	 * @param int      $priority Hook priority.
	 * @param int      $accepted_args Number of arguments.
	 * @return array Updated hooks array.
	 */
	private function add_hook( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Register all hooks with WordPress
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}
}

<?php
/**
 * Loader class for managing hooks and filters
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
 * Manages all hooks and filters for the plugin
 */
class Pola_Wyboru_Loader {

	/**
	 * Array of actions to register
	 *
	 * @var array
	 */
	private $actions = array();

	/**
	 * Array of filters to register
	 *
	 * @var array
	 */
	private $filters = array();

	/**
	 * Add action hook
	 *
	 * @param string $hook The name of the WordPress action that is being registered.
	 * @param object $component A reference to the instance of the object on which the action is defined.
	 * @param string $callback The name of the function definition on the $component.
	 * @param int    $priority Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add_hook( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add filter hook
	 *
	 * @param string $hook The name of the WordPress filter that is being registered.
	 * @param object $component A reference to the instance of the object on which the filter is defined.
	 * @param string $callback The name of the function definition on the $component.
	 * @param int    $priority Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add_hook( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Helper function for adding hooks
	 *
	 * @param array  $hooks The array of hooks to add to.
	 * @param string $hook The name of the WordPress hook that is being registered.
	 * @param object $component A reference to the instance of the object on which the hook is defined.
	 * @param string $callback The name of the function definition on the $component.
	 * @param int    $priority The priority at which the function should be fired.
	 * @param int    $accepted_args The number of arguments that should be passed to the $callback.
	 * @return array
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
	 * Register all actions and filters
	 */
	public function run() {
		foreach ( $this->actions as $hook ) {
			add_action(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		foreach ( $this->filters as $hook ) {
			add_filter(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}
	}
}

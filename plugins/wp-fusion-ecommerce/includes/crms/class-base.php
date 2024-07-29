<?php

class WPF_EC_CRM_Base {

	/**
	 * Contains the class object for the currently active CRM
	 *
	 * @var api
	 * @since 1.0
	 */

	public $crm;


	public function __construct() {

		$configured_crms = wp_fusion_ecommerce()->get_crms();

		foreach ( $configured_crms as $slug => $classname ) {

			if ( class_exists( $classname ) ) {

				if ( wp_fusion()->crm->slug == $slug ) {

					$crm       = new $classname();
					$this->crm = $crm;
					$this->crm->init();

				}
			}
		}

	}

}

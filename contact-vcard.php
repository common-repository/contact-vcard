<?php
/*
Plugin Name: Contact vCard
Plugin URI: 
Version: 0.3
Author: Sheridan
Author URI: http://www.ordzhonikidze-sanatorium.ru
Description:Contact vCard generator with QR Code Generator
*/

require_once('cvc-database.php');
require_once('cvc-data-tools.php');
require_once('cvc-form-tools.php');
require_once('cvc-options.php');
require_once('cvc-contacts.php');
require_once('cvc-contacts-batch.php');
require_once('cvc-fields.php');
require_once('cvc-shortcodes.php');


/*Activation*/
register_activation_hook(__FILE__, 'cv_activate' );
register_uninstall_hook (__FILE__, 'cv_uninstall');
add_action('admin_menu', 'administrative_add_pages');
add_shortcode('single_contact', 'single_card_view');
add_shortcode('all_contacts'  , 'all_cards_view'  );

function cv_activate()
{
  global $cv_db_version;
  if( $cv_db_version !=  get_option('cv_db_version', "0.0"))
  {
    create_tables();
    create_init_data();
    add_option('cv_db_version', $cv_db_version);
  }
}

function cv_uninstall()
{
  delete_option('cv_o_vcard_template');
  delete_option('cv_o_qr_ecc');
  delete_option('cv_o_qr_size');
  delete_cv_database();
}

function administrative_add_pages() 
{
  add_menu_page   (          'Contact vCard'      , 'Contact vCard'      , 8, __FILE__                       , 'administrative_toplevel_page'      );
  add_submenu_page(__FILE__, 'Contacts list'      , 'Contacts list'      , 8, 'administrative_contacts_list' , 'administrative_contacts_list_page' );
  add_submenu_page(__FILE__, 'Contact add'        , 'Contact add'        , 8, 'administrative_contact_manage', 'administrative_contact_manage_page');
  add_submenu_page(__FILE__, 'Batch contacts edit', 'Batch contacts edit', 8, 'administrative_contact_edit'  , 'administrative_contact_manage_edit');
  add_submenu_page(__FILE__, 'Fields list'        , 'Fields list'        , 8, 'administrative_fields_list'   , 'administrative_fields_list_page'   );
  add_submenu_page(__FILE__, 'Field add'          , 'Field add'          , 8, 'administrative_field_manage'  , 'administrative_field_manage_page'  );
}

?>
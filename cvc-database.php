<?php
global $wpdb;
global $cv_db_version;
$cv_db_version = "1.9";
global $table_names;
$table_names = array
(
  'ftypes'   => "{$wpdb->prefix}contactvcard_field_types",
  'fields'   => "{$wpdb->prefix}contactvcard_fields",
  'contacts' => "{$wpdb->prefix}contactvcard_contacts"
);

function create_tables()
{
  global $wpdb;
  global $table_names;
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  // field types
  $sql = <<<EOL
    CREATE TABLE {$table_names['ftypes']}
    (
      id         mediumint(9) NOT NULL AUTO_INCREMENT,
      name       VARCHAR(32)  NOT NULL,
      vcard_type VARCHAR(32)  NOT NULL,
      only_one   bool         not null default 1,
      UNIQUE KEY id (id)
    );
EOL;
  dbDelta($sql);
  // fields
  $sql = <<<EOL
    CREATE TABLE {$table_names['fields']}
    (
      id            mediumint(9) NOT NULL AUTO_INCREMENT,
      name          VARCHAR(32)  NOT NULL,
      vcard_type_id mediumint(9) NOT NULL default 1,
      UNIQUE KEY id (id)
    );
EOL;
  dbDelta($sql);

  // contacts
  $sql = <<<EOL
    CREATE TABLE {$table_names['contacts']}
    (
      id         mediumint(9) NOT NULL,
      field_id   mediumint(9) NOT NULL,
      field_data VARCHAR(256) NOT NULL default "",
      UNIQUE KEY id (id, field_id)
    );
EOL;
  dbDelta($sql);
}

function cv_record_update($wpdb, $id, $table, $values, $format)
{
  if($wpdb->get_var($wpdb->prepare("select count(id) from {$table} where id={$id};")) == 0)
  {
    $equal = 1;
    $data = $wpdb->get_row("select * from {$table} where id = {$id}", ARRAY_A);
    foreach($values as $key => $value)
    {
      if($table != $table_names['fields'] and $data[$key] != $value)
      {
        $equal = 0;
        break;
      }
    }
    if($equal == 0)
    {
      $wpdb->update($table, $values, array('id' => $id), $format, array('%d'));
    }
  }
  else
  {
    $wpdb->insert($table, $values, $format);
  }
}

function create_init_data()
{
  global $wpdb;
  global $table_names;
  cv_record_update($wpdb, 1 , $table_names['ftypes'], array('id' => 1 , 'name' => 'Formatted name', 'vcard_type' => 'N'                  , 'only_one' => 1), array('%d', '%s', '%s', '%d'));
  cv_record_update($wpdb, 2 , $table_names['ftypes'], array('id' => 2 , 'name' => 'Nickname'      , 'vcard_type' => 'NICKNAME'           , 'only_one' => 1), array('%d', '%s', '%s', '%d'));
  cv_record_update($wpdb, 3 , $table_names['ftypes'], array('id' => 3 , 'name' => 'Mobile phone'  , 'vcard_type' => 'TEL;TYPE=cell'      , 'only_one' => 0), array('%d', '%s', '%s', '%d'));
  cv_record_update($wpdb, 4 , $table_names['ftypes'], array('id' => 4 , 'name' => 'Work phone'    , 'vcard_type' => 'TEL;TYPE=work'      , 'only_one' => 0), array('%d', '%s', '%s', '%d'));
  cv_record_update($wpdb, 5 , $table_names['ftypes'], array('id' => 5 , 'name' => 'Fax'           , 'vcard_type' => 'TEL;TYPE=fax'       , 'only_one' => 0), array('%d', '%s', '%s', '%d'));
  cv_record_update($wpdb, 6 , $table_names['ftypes'], array('id' => 6 , 'name' => 'Home phone'    , 'vcard_type' => 'TEL;TYPE=home'      , 'only_one' => 0), array('%d', '%s', '%s', '%d'));
  cv_record_update($wpdb, 7 , $table_names['ftypes'], array('id' => 7 , 'name' => 'E-Mail'        , 'vcard_type' => 'EMAIL;TYPE=INTERNET', 'only_one' => 0), array('%d', '%s', '%s', '%d'));
  cv_record_update($wpdb, 8 , $table_names['ftypes'], array('id' => 8 , 'name' => 'Job'           , 'vcard_type' => 'TITLE'              , 'only_one' => 1), array('%d', '%s', '%s', '%d'));
  cv_record_update($wpdb, 9 , $table_names['ftypes'], array('id' => 9 , 'name' => 'Organisation'  , 'vcard_type' => 'ORG'                , 'only_one' => 1), array('%d', '%s', '%s', '%d'));
  cv_record_update($wpdb, 10, $table_names['ftypes'], array('id' => 10, 'name' => 'Site'          , 'vcard_type' => 'URL'                , 'only_one' => 0), array('%d', '%s', '%s', '%d'));
  cv_record_update($wpdb, 11, $table_names['ftypes'], array('id' => 11, 'name' => 'Work address'  , 'vcard_type' => 'ADR;TYPE=work'      , 'only_one' => 1), array('%d', '%s', '%s', '%d'));
  cv_record_update($wpdb, 12, $table_names['ftypes'], array('id' => 12, 'name' => 'Home address'  , 'vcard_type' => 'ADR;TYPE=home'      , 'only_one' => 1), array('%d', '%s', '%s', '%d'));

  cv_record_update($wpdb, 1 , $table_names['fields'], array('id' => 1 , 'name' => 'Name'        , 'vcard_type_id' => 1  ), array('%d', '%s', '%d') );
  cv_record_update($wpdb, 2 , $table_names['fields'], array('id' => 2 , 'name' => 'Mobile phone', 'vcard_type_id' => 3  ), array('%d', '%s', '%d') );
  cv_record_update($wpdb, 3 , $table_names['fields'], array('id' => 3 , 'name' => 'Work phone'  , 'vcard_type_id' => 4  ), array('%d', '%s', '%d') );
  cv_record_update($wpdb, 4 , $table_names['fields'], array('id' => 4 , 'name' => 'Fax'         , 'vcard_type_id' => 5  ), array('%d', '%s', '%d') );
  cv_record_update($wpdb, 5 , $table_names['fields'], array('id' => 5 , 'name' => 'E-Mail'      , 'vcard_type_id' => 7  ), array('%d', '%s', '%d') );
  cv_record_update($wpdb, 6 , $table_names['fields'], array('id' => 6 , 'name' => 'Job'         , 'vcard_type_id' => 8  ), array('%d', '%s', '%d') );
  cv_record_update($wpdb, 7 , $table_names['fields'], array('id' => 7 , 'name' => 'Organisation', 'vcard_type_id' => 9  ), array('%d', '%s', '%d') );
  cv_record_update($wpdb, 8 , $table_names['fields'], array('id' => 8 , 'name' => 'Work address', 'vcard_type_id' => 11 ), array('%d', '%s', '%d') );
  cv_record_update($wpdb, 9 , $table_names['fields'], array('id' => 9 , 'name' => 'Site'        , 'vcard_type_id' => 10 ), array('%d', '%s', '%d') );
}

function delete_cv_database()
{
  global $wpdb;
  global $table_names;
  $sql = <<<EOL
    drop table
      {$table_names['contacts']},
      {$table_names['fields']},
      {$table_names['ftypess']}
EOL;
$wpdb->query($sql);

}

function load_shortcode_card_template()
{
  $result = get_option('cv_o_vcard_template', '');
  if ($result == '')
  {
    return file_get_contents('default-vcard-template.tmpl');
  }
  return base64_decode($result);
}

function store_shortcode_card_template($template)
{
  update_option('cv_o_vcard_template', base64_encode(stripslashes($template)));
  //echo base64_encode($template);
}

?>

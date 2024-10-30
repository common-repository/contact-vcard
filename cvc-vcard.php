<?php

require_once('cvc-database.php');
require_once('cvc-data-tools.php');

function generate_vcard($contact_id)
{
  global $wpdb;
  global $table_names;
  $result = <<<_
BEGIN:VCARD
VERSION:3.0

_;
  $sql = <<<EOL
  select 
    {$table_names['ftypes']}.vcard_type as vcard_type,
    {$table_names['contacts']}.field_data as contact_field_data
  from {$table_names['contacts']}
  left join {$table_names['fields']} on {$table_names['contacts']}.field_id = {$table_names['fields']}.id
  left join {$table_names['ftypes']} on {$table_names['ftypes']}.id = {$table_names['fields']}.vcard_type_id
  where 
    {$table_names['contacts']}.id={$contact_id}
EOL;
  //echo $sql;
  $contacts_data = $wpdb->get_results($sql);
  foreach ($contacts_data as $contact_data)
  {
    if(trim($contact_data->contact_field_data) != '')
    {
      switch(get_vcard_field_type($contact_data->vcard_type))
      {
        case 'N':
        {
          $result .= "{$contact_data->vcard_type}:{$contact_data->contact_field_data}\n";
          $result .= "FN:".format_field_data($contact_data->contact_field_data, $contact_data->vcard_type, 'vcard')."\n";
        } break;
        default: $result .= "{$contact_data->vcard_type}:".format_field_data($contact_data->contact_field_data, $contact_data->vcard_type, 'vcard')."\n";
      }
    }
  }
  $result .= <<<_
END:VCARD

_;
  return $result;
}

?>

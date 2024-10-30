<?php

require_once('cvc-database.php');
require_once('cvc-data-tools.php');
require_once('cvc-form-tools.php');

function administrative_contact_manage_edit()
{
  global $wpdb;
  global $table_names;
  global $forms_actions;

  if(isset($_POST['field_form_action']))
  {
    batch_edit($_POST['field_id']);
  }

  echo <<<EOT
  <div class='wrap'>
  <div id="icon-edit-pages" class="icon32"></div><h2>Batch contacts edit</h2>
EOT;
  $sql = <<<EOL
  select {$table_names['fields']}.name as field_name, 
         {$table_names['ftypes']}.vcard_type,
         {$table_names['fields']}.id as field_id
  from {$table_names['fields']} 
  left join {$table_names['ftypes']} on {$table_names['ftypes']}.id = {$table_names['fields']}.vcard_type_id
EOL;
  //echo $sql;
  $fields = $wpdb->get_results($sql);
  foreach ( $fields as $field )
  {
    $plank = array(plank_open("Edit {$field->field_name}"), plank_close());
    $element = get_field_controls('batch', $field->vcard_type, '');
    echo <<<EOL
{$plank[0]}
    <form method='post' name='batch_{$field->field_id}' action='{$forms_actions['batch_edit']}'>
      <input type='hidden' name='field_id' value='{$field->field_id}' />
      <input type='hidden' name='field_form_action' value='batch-edit' />
      <table>
      <tr><td>{$field->field_name}</td><td>{$element}</td></tr>
      </table>
      <input type='submit' value='Edit' class='button-primary' />
    </form>
{$plank[1]}
EOL;
  }
  echo <<<EOL
  </div>
EOL;
}

function batch_edit($field_id)
{
  global $wpdb;
  global $table_names;
  $sql = <<<EOL
select {$table_names['ftypes']}.vcard_type
from {$table_names['ftypes']}
left join {$table_names['fields']} ON {$table_names['ftypes']}.id = {$table_names['fields']}.vcard_type_id
where {$table_names['fields']}.id={$field_id}
EOL;
  $wpdb->update($table_names['contacts'],
              array('field_data' => extract_form_post_contact_field_data('batch', $wpdb->get_var($wpdb->prepare($sql)))),
              array('field_id' => $field_id),
              array('%s'),
              array('%d'));
}

?>

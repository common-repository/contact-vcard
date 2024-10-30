<?php

require_once('cvc-database.php');
require_once('cvc-data-tools.php');
require_once('cvc-form-tools.php');

function administrative_fields_list_page()
{
  global $wpdb;
  global $table_names;
  global $forms_actions;

  echo <<<EOT
  <div class='wrap'>
  <div id="icon-edit-pages" class="icon32"></div><h2>Contact fields list</h2>
  <table class='widefat'>
  <tr><thead><th>Name</th><th>Type</th><th>Action</th></thead></tr>
  <tbody>
EOT;
  $sql = <<<EOL
  select {$table_names['fields']}.name as field_name, 
         {$table_names['ftypes']}.name as type_name,
         {$table_names['fields']}.id as field_id
  from {$table_names['fields']} 
  left join {$table_names['ftypes']} on {$table_names['ftypes']}.id = {$table_names['fields']}.vcard_type_id
EOL;
  //echo $sql;
  $fields = $wpdb->get_results($sql);
  foreach ( $fields as $field )
  {
    echo <<<EOL
<tr>
  <td>{$field->field_name}</td>
  <td>{$field->type_name}</td>
  <td>
    <form method='post' name='field_form' action='{$forms_actions['field_edit']}'>
      <input type='hidden' name='field_id' value='{$field->field_id}' />
      <input type='hidden' name='field_form_action' value='edit-request' />
      <input type='submit' value='Edit' class='button-secondary' />
    </form>
  </td>
</tr>
EOL;
  }
  echo <<<EOL
  </tbody>
  <thead><tr><th>Name</th><th>Type</th><th>Action</th></tr></thead>
  </table>
  <br />
  <br />
    <form name='new_field' method='post' action='{$forms_actions['field_new_request']}'>
      <input type="hidden" name="field_form_action" value="new-request" />
      <input type='submit' value='Add field' class='button-secondary' />
    </form>
  </div>
EOL;
}

function administrative_field_manage_page()
{
  switch($_POST['field_form_action'])
  {
    case 'new'         : { add_field()   ; show_field_edit_form('edit', $_POST['field_id']); } break;
    case 'edit'        : { edit_field()  ; show_field_edit_form('edit', $_POST['field_id']); } break;
    case 'edit-request': {                 show_field_edit_form('edit', $_POST['field_id']); } break;
    case 'delete'      : { delete_field();                                                   }
    case 'new-request' :
    default            : {               show_field_edit_form('new', 0);                     } break;
  }
}

function get_avialable_field_options($field_id)
{
  global $wpdb;
  global $table_names;
  
  $result = '';
  $ftypes = $wpdb->get_results("select name, id, only_one from {$table_names['ftypes']}");
  foreach ( $ftypes as $ftype )
  {
    $selected = $field_id == 0 ? '' : ($wpdb->get_var($wpdb->prepare("SELECT vcard_type_id FROM {$table_names['fields']} where id={$field_id};")) == $ftype->id ? 'selected' : '');
    //echo "SELECT vcard_type_id FROM {$table_names['fields']} where id={$field_id}; - {$ftype_id}<br />";
    if (
         $selected != '' or
         $ftype->only_one == 0 or
         (
           $ftype->only_one == 1 and
           $wpdb->get_var($wpdb->prepare("SELECT count(id) FROM {$table_names['fields']} where vcard_type_id={$ftype->id};")) == 0
         )
       )
    {
      $result .= <<<EOL
    <option {$selected} value='{$ftype->id}'>
      {$ftype->name}
    </option>
EOL;
    }
  }
  return $result;
}

function show_field_edit_form($action, $field_id)
{
  global $wpdb;
  global $table_names;
  global $forms_actions;
  $form_action = $forms_actions['field_'.$action];
  $field_name = $field_id != 0 ? $wpdb->get_var($wpdb->prepare("SELECT name FROM {$table_names['fields']} where id={$field_id};")) : '';
  $field_options = get_avialable_field_options($field_id);
  $plank = array(plank_open("Field {$action}"), plank_open("Field delete"), plank_close());
  $default_data = $field_id == 0 ? '' : '';
  echo <<<EOT
  <div class='wrap'>
  <div id="icon-tools" class="icon32"></div><h2>Field manage</h2>
  {$plank[0]}
    <form name='field_form' method='post' action='{$form_action}'>
    <input type='hidden' name='field_form_action' value='{$action}' />
    <input type='hidden' name='field_id' value='{$field_id}' />
    <table>
    <tr>
      <td align='right'>Field name:</td>
      <td><input id='field_name' type='text' width='100%' name='field_name' value='{$field_name}' /></td>
    </tr>
    <tr>
      <td align='right'>Field type:</td>
      <td><select name='field_type' id='field_type'>{$field_options}</select></td>
    </tr>
    <tr>
      <td colspan='2'><input class='button-primary' type="submit" name="Submit" value="Submit" /></td>
    </tr>
    </table>
    </form>
  {$plank[2]}
EOT;
  if($field_id != 0)
  {
    echo <<<EOT
  {$plank[1]}
    <form name='delete_field' method='post' action='{$forms_actions['field_delete']}'>
      <input type='hidden' name='field_id' value='{$field_id}' />
      <input type='hidden' name='field_form_action' value='delete' />
      <input type='submit' value='Delete' class='button-secondary' />
    </form>
  {$plank[2]}
EOT;
  }
  echo '</div>';
}

function add_field()
{
  global $wpdb;
  global $table_names;
  $wpdb->insert($table_names['fields'], 
                  array('name' => $_POST['field_name'], 'vcard_type_id' => $_POST['field_type'] ), 
                  array('%s', '%d') );
  $field_id = $wpdb->insert_id;
  $ids = $wpdb->get_col("select distinct id from {$table_names['contacts']};");
  foreach($ids as $id)
  {
    $wpdb->insert($table_names['contacts'], 
          array('id' => $id, 'field_id' => $field_id, 'field_data' => ''), 
          array('%d', '%d', '%s') );
  }
}

function edit_field()
{
  global $wpdb;
  global $table_names;
  
  // rename into template
  $old_field_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$table_names['fields']} where id={$_POST['field_id']};"));
  $tmpl = load_shortcode_card_template();
  $tmpl = str_replace("%{$old_field_name}%" , "%{$_POST['field_name']}%" , $tmpl);
  $tmpl = str_replace("%!{$old_field_name}%", "%!{$_POST['field_name']}%", $tmpl);
  store_shortcode_card_template($tmpl);
  
  $wpdb->update($table_names['fields'], 
                  array('name' => $_POST['field_name'], 'vcard_type_id' => $_POST['field_type'] ), 
                  array('id' => $_POST['field_id']),
                  array('%s', '%d'),
                  array('%d') );
}

function delete_field()
{
  global $wpdb;
  global $table_names;
  $sql = <<<EOL
    delete from {$table_names['fields']}
    where id='{$_POST['field_id']}'
EOL;
  $wpdb->query($sql);
  $sql = <<<EOL
    delete from {$table_names['contacts']}
    where field_id='{$_POST['field_id']}'
EOL;
  $wpdb->query($sql);
}


?>

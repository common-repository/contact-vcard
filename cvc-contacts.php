<?php
require_once('cvc-database.php');
require_once('cvc-data-tools.php');
require_once('cvc-form-tools.php');

function administrative_contacts_list_page()
{
  global $wpdb;
  global $table_names;
  global $forms_actions;

  $fields = $wpdb->get_results("select id, name from {$table_names['fields']} order by id;");
  $html_fields = '';
  foreach ( $fields as $field )
  {
    $html_fields .= '<th>'.$field->name.'</th>';
  }
  echo <<<EOT
  <div class='wrap'>
  <div id="icon-edit-pages" class="icon32"></div><h2>Contacts list</h2>
  <table class='widefat'>
  <thead><tr><th>ID</th>{$html_fields}<th>Action</th></tr></thead>
  <tbody>
EOT;
  $contacts_ids = $wpdb->get_results("SELECT distinct id FROM {$table_names['contacts']};");
  $out_fields_count = 0;
  $shortcodes = '';
  foreach($contacts_ids as $contact_id)
  {
    $sql = <<<EOL
  select
      {$table_names['contacts']}.field_id, 
      {$table_names['ftypes']}.vcard_type,
      {$table_names['contacts']}.field_data
  from {$table_names['contacts']}
  left join {$table_names['fields']} on {$table_names['contacts']}.field_id = {$table_names['fields']}.id
  left join {$table_names['ftypes']} on {$table_names['ftypes']}.id = {$table_names['fields']}.vcard_type_id
  where
      {$table_names['contacts']}.id={$contact_id->id}
  order by {$table_names['contacts']}.field_id;
EOL;
    $contacts_data = $wpdb->get_results($sql);
    echo '<tr>';
    echo '<td>'.$contact_id->id.'</td>';
    $shortcodes .= "<li><code>[single_contact id={$contact_id->id}]</code></li>";
    foreach($contacts_data as $contact_data)
    {
      echo '<td>'.format_field_data($contact_data->field_data, $contact_data->vcard_type, 'admin-list').'</td>';
    }
    while($out_fields_count < count($ids))
    {
      echo '<td>&nbsp;</td>';
      $out_fields_count++;
    }
    echo <<<EOL
<td>
    <form method='post' name='contact_form' action='{$forms_actions['contact_edit_request']}'>
      <input type="hidden" name="contact_id" value="{$contact_id->id}" />
      <input type="hidden" name="contact_form_action" value="edit-request" />
      <input type='submit' value='Edit' class='button-secondary' />
    </form>
</td>
</tr>
EOL;
  }
  echo <<<EOT
  </tbody>
  <thead><tr><th>ID</th>{$html_fields}<th>Action</th></tr></thead>
  </table>
  <br />
  <br />
    <form name='new_contact' method='post' action='{$forms_actions['contact_new_request']}'>
      <input type="hidden" name="contact_form_action" value="new-request" />
      <input type='submit' value='Add contact' class='button-secondary' />
    </form>
  <br />
  <h3>Shortcodes</h3><ul>{$shortcodes}</ul>
  </div>
EOT;
}

function administrative_contact_manage_page()
{
  switch($_POST['contact_form_action'])
  {
    case 'new'         : { add_contact()   ; show_contact_edit_form('edit', $_POST['contact_id']); } break;
    case 'edit'        : { edit_contact()  ; show_contact_edit_form('edit', $_POST['contact_id']); } break;
    case 'edit-request': {                   show_contact_edit_form('edit', $_POST['contact_id']); } break;
    case 'delete'      : { delete_contact();                                                       }
    case 'new-request' :
    default            : {                   show_contact_edit_form('new', 0);                     } break;
  }
}

function show_contact_edit_form($action, $contact_id)
{
  global $wpdb;
  global $table_names;
  global $forms_actions;
  $form_action = $forms_actions['contact_'.$action];
  $plank = array(plank_open("Contact {$action}"), plank_open("Contact delete"), plank_close());
  echo <<<EOT
  <div class='wrap'>
  <div id="icon-tools" class="icon32"></div><h2>Contact manage</h2>
  {$plank[0]}
    <form name='contact_form' method='post' action='{$form_action}'>
    <input type='hidden' name='contact_form_action' value='{$action}' />
    <input type='hidden' name='contact_id' value='{$contact_id}' />
    <table>
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
    $element = '';
    $contact_field_data = '';
    if($contact_id != 0)
    {
      $contact_field_data = $wpdb->get_var($wpdb->prepare("SELECT field_data FROM {$table_names['contacts']} where id={$contact_id} and field_id={$field->field_id};"));
    }
    $element = get_field_controls($field->field_id, $field->vcard_type, $contact_field_data);
    echo <<<EOL
    <tr>
      <td align='right'>{$field->field_name}:</td>
      <td>{$element}</td>
    </tr>
EOL;
    
  }
  
  echo <<<EOT
  <tr>
     <td colspan='2'><input class='button-primary' type="submit" name="Submit" value="Submit" /></td>
  </tr>
  </table>
  </form>
  {$plank[2]}
EOT;
  if($contact_id != 0)
  {
    echo <<<EOT
    {$plank[1]}
    <form name='delete_contact' method='post' action='{$forms_actions['contact_delete']}'>
      <input type="hidden" name="contact_id" value="{$contact_id}" />
      <input type="hidden" name="contact_form_action" value="delete" />
      <input type='submit' value='Delete' class='button-secondary' />
    </form>
    {$plank[2]}
  </div>
EOT;
  }
  echo '</div>';
}

function add_contact()
{
  global $wpdb;
  global $table_names;
  
  $sql = <<<EOL
    select {$table_names['fields']}.name as field_name, 
           {$table_names['ftypes']}.vcard_type,
           {$table_names['fields']}.id as field_id
    from {$table_names['fields']} 
    left join {$table_names['ftypes']} on {$table_names['ftypes']}.id = {$table_names['fields']}.vcard_type_id
EOL;
    //echo $sql;
  $fields = $wpdb->get_results($sql);
  $new_id = $wpdb->get_var($wpdb->prepare("SELECT max(id) FROM {$table_names['contacts']};"))+1;
  foreach ( $fields as $field )
  {
    $wpdb->insert($table_names['contacts'], 
          array('id' => $new_id, 'field_id' => $field->field_id, 'field_data' => extract_form_post_contact_field_data($field->field_id, $field->vcard_type)), 
          array('%d', '%d', '%s') );
  }
}

function delete_contact()
{
  global $wpdb;
  global $table_names;
  $sql = <<<EOL
    delete from {$table_names['contacts']}
    where id = '{$_POST['contact_id']}'
EOL;
  $wpdb->query($sql);
}

function edit_contact()
{
  global $wpdb;
  global $table_names;
  
  $sql = <<<EOL
    select {$table_names['fields']}.name as field_name, 
           {$table_names['ftypes']}.vcard_type,
           {$table_names['fields']}.id as field_id
    from {$table_names['fields']} 
    left join {$table_names['ftypes']} on {$table_names['ftypes']}.id = {$table_names['fields']}.vcard_type_id
    order by {$table_names['fields']}.id
EOL;
    //echo $sql;
  $fields = $wpdb->get_results($sql);
  foreach ( $fields as $field )
  {
    $wpdb->update($table_names['contacts'], 
                  array('field_data' => extract_form_post_contact_field_data($field->field_id, $field->vcard_type)),
                  array('id' => $_POST['contact_id'], 'field_id' => $field->field_id),
                  array('%s'),
                  array('%d', '%d'));
  }
}
?>
<?php

require_once('cvc-database.php');
require_once('cvc-data-tools.php');

/*global*/
global $forms_actions;
$forms_actions = array
    (
    'contact_new'          => admin_url().'admin.php?page=administrative_contact_manage',
    'contact_new_request'  => admin_url().'admin.php?page=administrative_contact_manage',
    'contact_edit'         => admin_url().'admin.php?page=administrative_contact_manage',
    'contact_edit_request' => admin_url().'admin.php?page=administrative_contact_manage',
    'contact_delete'       => admin_url().'admin.php?page=administrative_contact_manage',
    'field_new'            => admin_url().'admin.php?page=administrative_field_manage',
    'field_new_request'    => admin_url().'admin.php?page=administrative_field_manage',
    'field_edit'           => admin_url().'admin.php?page=administrative_field_manage',
    'field_edit_request'   => admin_url().'admin.php?page=administrative_field_manage',
    'field_delete'         => admin_url().'admin.php?page=administrative_field_manage',
    'batch_edit'           => admin_url().'admin.php?page=administrative_contact_edit'
    );

function get_field_controls($field_id, $vcard_type, $field_value)
{
  $control = '';
  switch (get_vcard_field_type($vcard_type))
  {
    case 'N': 
    {
      $n = split(';', $field_value);
      $control = <<<EOL
<table>
<tr><td>Name      </td><td><input id='field_{$field_id}_name'       type='text' width='100%' size='50' name='field_{$field_id}_name'       value='{$n[1]}' /></td></tr>
<tr><td>Middlename</td><td><input id='field_{$field_id}_middlename' type='text' width='100%' size='50' name='field_{$field_id}_middlename' value='{$n[2]}' /></td></tr>
<tr><td>Surname   </td><td><input id='field_{$field_id}_surname'    type='text' width='100%' size='50' name='field_{$field_id}_surname'    value='{$n[0]}' /></td></tr>
</table>
EOL;
    } break;
    case 'ADR': 
    {
      $n = split(';', $field_value);
      $control = <<<EOL
<table>
<tr><td>PO Box          </td><td><input id='field_{$field_id}_pobox'   type='text' width='100%' size='50' name='field_{$field_id}_pobox'   value='{$n[0]}' /></td></tr>
<tr><td>Extended address</td><td><input id='field_{$field_id}_exaddr'  type='text' width='100%' size='50' name='field_{$field_id}_exaddr'  value='{$n[1]}' /></td></tr>
<tr><td>House and street</td><td><input id='field_{$field_id}_hs'      type='text' width='100%' size='50' name='field_{$field_id}_hs'      value='{$n[2]}' /></td></tr>
<tr><td>City            </td><td><input id='field_{$field_id}_city'    type='text' width='100%' size='50' name='field_{$field_id}_city'    value='{$n[3]}' /></td></tr>
<tr><td>State, region   </td><td><input id='field_{$field_id}_reg'     type='text' width='100%' size='50' name='field_{$field_id}_reg'     value='{$n[4]}' /></td></tr>
<tr><td>Zip code        </td><td><input id='field_{$field_id}_zip'     type='text' width='100%' size='50' name='field_{$field_id}_zip'     value='{$n[5]}' /></td></tr>
<tr><td>Country         </td><td><input id='field_{$field_id}_country' type='text' width='100%' size='50' name='field_{$field_id}_country' value='{$n[6]}' /></td></tr>
</table>
EOL;
    } break;
    default:    $control = "<input id='field_{$field_id}' type='text' width='100%' size='50' name='field_{$field_id}' value='{$field_value}' />";
  }
  return $control;
}

function extract_form_post_contact_field_data($field_id, $vcard_type)
{
  switch (get_vcard_field_type($vcard_type))
  {
    case 'N':
    {
      return $_POST['field_'.$field_id.'_surname']   .';'.
             $_POST['field_'.$field_id.'_name']      .';'.
             $_POST['field_'.$field_id.'_middlename'];
      break;
    }
    case 'ADR':
    {
      return $_POST['field_'.$field_id.'_pobox']  .';'.
             $_POST['field_'.$field_id.'_exaddr'] .';'.
             $_POST['field_'.$field_id.'_hs']     .';'.
             $_POST['field_'.$field_id.'_city']   .';'.
             $_POST['field_'.$field_id.'_reg']    .';'.
             $_POST['field_'.$field_id.'_zip']    .';'.
             $_POST['field_'.$field_id.'_country'];
      break;
    }
    default: return $_POST['field_'.$field_id];
  }
  return 'wow! o0';
}

function plank_open($title)
{
  return <<<_
  <div class="ui-sortable meta-box-sortables">
  <div class="postbox" >
  <h3 class='hndle'>{$title}</h3>
  <div class="inside">
_;
}

function plank_close()
{
  return "</div></div></div>";
}

?>

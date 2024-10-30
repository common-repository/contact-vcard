<?php

require_once('cvc-database.php');

function administrative_toplevel_page()
{
  global $wpdb;
  global $table_names;
  if( $_POST['vc_o_qr_updated'] == 'Y' )
  {
    update_option('cv_o_qr_ecc_small' , $_POST['cv_o_qr_ecc_small']);
    update_option('cv_o_qr_ecc_big'   , $_POST['cv_o_qr_ecc_big']);
    update_option('cv_o_qr_size_small', $_POST['cv_o_qr_size_small']);
    update_option('cv_o_qr_size_big'  , $_POST['cv_o_qr_size_big']);
    echo '<div class="updated"><p><strong>QR-code options updated</strong></p></div>';
  }
  
  if( $_POST['vc_o_template_updated'] == 'Y' )
  {
    store_shortcode_card_template($_POST['cvovcardtemplate']);
    echo '<div class="updated"><p><strong>Template updated</strong></p></div>';
  }
  
  //delete_option('cv_o_vcard_template');
  $o_qr_ecc_small      = get_option('cv_o_qr_ecc_small' , 'L');
  $o_qr_ecc_big        = get_option('cv_o_qr_ecc_big'   , 'H');
  $o_qr_size_small     = get_option('cv_o_qr_size_small', 5  );
  $o_qr_size_big       = get_option('cv_o_qr_size_big'  , 10 );
  $o_vcard_template    = load_shortcode_card_template();
  $qr_sizes_small_list = '';
  $qr_sizes_big_list   = '';
  foreach(array(1,2,3,4,5,6,7,8,9,10) as $avialable_size)
  {
    $selected_small = '';
    $selected_big   = '';
    if($o_qr_size_small == $avialable_size) { $selected_small = 'selected'; }
    if($o_qr_size_big   == $avialable_size) { $selected_big   = 'selected'; }
    $qr_sizes_small_list .= "<option {$selected_small} value={$avialable_size}>{$avialable_size}</option>";
    $qr_sizes_big_list   .= "<option {$selected_big}   value={$avialable_size}>{$avialable_size}</option>";
  }
  $qr_ecc_small_list = '';
  $qr_ecc_big_list   = '';
  foreach(array('L' => 'L (smallest)', 'M' => 'M', 'Q' => 'Q', 'H' => 'H (best)') as $avialable_ecc => $ecc_name)
  {
    $selected_small = '';
    $selected_big   = '';
    if($o_qr_ecc_small == $avialable_ecc) { $selected_small = 'selected'; }
    if($o_qr_ecc_big   == $avialable_ecc) { $selected_big   = 'selected'; }
    $qr_ecc_small_list .= "<option {$selected_small} value={$avialable_ecc}>{$ecc_name}</option>";
    $qr_ecc_big_list   .= "<option {$selected_big}   value={$avialable_ecc}>{$ecc_name}</option>";
  }
  $form_action = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
  $plank = array(plank_open('QR code image options'), plank_open('vCard template'), plank_open('Avialable templates'), plank_close());
  echo <<<EOT
  <div class='wrap'>
  <div id="icon-options-general" class="icon32"></div><h2>Base options</h2>
  {$plank[0]}
    <form name="cv_o_form_qr" method="post" action="$form_action">
      <input type="hidden" name="vc_o_qr_updated" value="Y" />
      <table>
      <tr><td>QR Code ECC level (page)      </td><td><select name="cv_o_qr_ecc_small"> {$qr_ecc_small_list}  </select></td></tr>
      <tr><td>QR Code size (page)           </td><td><select name="cv_o_qr_size_small">{$qr_sizes_small_list}</select></td></tr>
      <tr><td>QR Code ECC level (fullscreen)</td><td><select name="cv_o_qr_ecc_big">   {$qr_ecc_big_list}    </select></td></tr>
      <tr><td>QR Code size (fullscreen)     </td><td><select name="cv_o_qr_size_big">  {$qr_sizes_big_list}  </select></td></tr>
      <tr><td colspan='2'><input class='button-primary' type="submit" name="Submit" value="Update QR-code options" /></td></tr>
      </table>
    </form>
  {$plank[3]}
  {$plank[1]}
    <form name="cv_o_form_template" method="post" action="$form_action">
      <input type="hidden" name="vc_o_template_updated" value="Y" />
EOT;
  wp_editor($o_vcard_template, 'cvovcardtemplate', array('media_buttons' => 0));
  echo <<<EOT
     <br />
     <input class='button-primary' type="submit" name="Submit" value="Update template" />
  </form>
  {$plank[3]}
  {$plank[2]}
  <ul>
EOT;
  $vcard_types = $wpdb->get_results("select name from {$table_names['fields']}");
  foreach ( $vcard_types as $vcard_type )
  {
    echo "<li>Field name: <code>%!{$vcard_type->name}%</code>, field data:<code>%{$vcard_type->name}%</code></li>";
  }
  foreach (array('QR Code image' => 'qrcode', 'vCard download link' => 'vcard-file') as $key => $value)
  {
    echo "<li>{$key}: <code>%{$value}%</code></li>";
  }
  echo <<<_
</ul>
{$plank[3]}
</div>
_;
}

?>

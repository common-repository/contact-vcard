<?php
require_once('cvc-database.php');
require_once('cvc-data-tools.php');
require_once('cvc-vcard.php');
require_once('cvc-qrcode.php');

function generate_cache($contact_id)
{

  $vcard_data = generate_vcard($contact_id);
  $data_md5 = md5($vcard_data);

  $cache_path = plugin_dir_path(__FILE__)."../../cache/cvc-vcard";
  if(!is_dir($cache_path)) { mkdir($cache_path); }
  
  $result = array();
  
  foreach (array('small', 'big') as $size)
  {
    //echo $size;
    $qr_ecc  = get_option('cv_o_qr_ecc_' .$size, 'L');
    $qr_size = get_option('cv_o_qr_size_'.$size,  5 );
    $qr_filename = $data_md5.'_'.$qr_ecc.'_'.$qr_size.'.png';
    $full_qr_path    = $cache_path.'/'.$qr_filename;
    if (!file_exists($full_qr_path))
    {
      generate_qrcode_image($vcard_data, $full_qr_path, $qr_ecc, $qr_size);
    }
    $result['qr'][$size] = get_site_url().'/wp-content/cache/cvc-vcard/'.$qr_filename;
  }
  $vcf_filename  = $data_md5.'.vcf';
  $full_vcf_path = $cache_path.'/'.$vcf_filename;
  if (!file_exists($full_vcf_path))
  {
    file_put_contents($full_vcf_path, $vcard_data);
  }
  $result['vcf'] = get_site_url().'/wp-content/cache/cvc-vcard/'.$vcf_filename;
  return $result;
}

function generate_card_view($contact_id)
{
  global $wpdb;
  global $table_names;
 
  $result = load_shortcode_card_template();
  
  $files = generate_cache($contact_id);
  
  $sql = <<<EOL
  select
      {$table_names['contacts']}.field_id, 
      {$table_names['ftypes']}.vcard_type,
      {$table_names['contacts']}.field_data,
      {$table_names['fields']}.name as field_name
  from {$table_names['contacts']}
  left join {$table_names['fields']} on {$table_names['contacts']}.field_id = {$table_names['fields']}.id
  left join {$table_names['ftypes']} on {$table_names['ftypes']}.id = {$table_names['fields']}.vcard_type_id
  where
      {$table_names['contacts']}.id={$contact_id}
  order by {$table_names['contacts']}.field_id;
EOL;
  $contact_data = $wpdb->get_results($sql);
  foreach ($contact_data as $cd)
  {
    $result = str_replace("%{$cd->field_name}%", format_field_data($cd->field_data, $cd->vcard_type, 'shortcode'), $result);
    $result = str_replace("%!{$cd->field_name}%", $cd->field_name, $result);
  }
//  $url = plugin_dir_url( __FILE__ );
  $vc = <<<_
<a href='{$files['vcf']}'>
  vcard.vcf
</a>
_;
  
  $qr = <<<_
<a href='{$files['qr']['big']}' target='_blank'>
  <img src='{$files['qr']['small']}' />
</a>
_;
  $result = str_replace("%vcard-file%", $vc, $result);
  $result = str_replace("%qrcode%"    , $qr, $result);
  return $result;
}

function single_card_view($atts)
{
  extract(shortcode_atts(array('id' => 1), $atts));
  return do_shortcode(generate_card_view($id));
}

function all_cards_view($atts)
{
  global $wpdb;
  global $table_names;
  $contacts_ids = $wpdb->get_results("SELECT distinct id FROM {$table_names['contacts']};");
  $result = '';
  foreach($contacts_ids as $contact_id)
  {
    $result .= generate_card_view($contact_id->id);
  }
  return do_shortcode($result);
}

?>
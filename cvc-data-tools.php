<?php

function get_vcard_field_type($vcard_type)
{
  $t = split(';', $vcard_type);
  return $t[0];
}

function fix_url($url)
{
  if(!preg_match('/https?:\/\//', $url)) { return 'http://'.$url; }
  return $url; 
}

function format_field_data($field_data, $vcard_type, $what)
{
  switch($what)
  {
    case 'admin-list':
    {
      switch (get_vcard_field_type($vcard_type))
      {
        case 'N'  : return str_replace(';', ' '     , $field_data); break;
        case 'ADR': return str_replace(';', '<br />', $field_data); break;
        default: return trim($field_data);
      }
    } break;
    case 'shortcode':
    {
      switch (get_vcard_field_type($vcard_type))
      {
        case 'N'  : return str_replace(';', ' '     , $field_data); break;
        case 'ADR': return str_replace(';', '<br />', $field_data); break;
        case 'URL': return fix_url($field_data); break;
        default: return trim($field_data);
      }
    } break;
    case 'vcard':
    {
      switch (get_vcard_field_type($vcard_type))
      {
        case 'N'  : return str_replace(';', ' '     , $field_data); break;
        case 'URL': return fix_url($field_data); break;
        default: return trim($field_data);
      }
    } break;
  }
}

?>


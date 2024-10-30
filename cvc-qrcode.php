<?php
include_once('phpqrcode/qrlib.php');

function generate_qrcode_image($data, $full_path, $qr_ecc, $qr_size)
{
  QRcode::png($data, $full_path, $qr_ecc, $qr_size, 2);
}

?>

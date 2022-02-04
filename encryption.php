<?php
$data_to_encrypt = "31125461037"; // the social security number to encrypt
echo "Data to encrypt: " . $data_to_encrypt;
echo "\n";
$cipher = "aes-128-gcm";
$raw_data = "M2TEW9VL4VsjJddb+s1MLw=="; // value of the rawValue field from the encryption key
$key = base64_decode($raw_data);
if (in_array($cipher, openssl_get_cipher_methods())) {
    $iv_len = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($iv_len);
    $tag = "";
    $ciphertext = openssl_encrypt($data_to_encrypt, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
    echo "Encrypted String: " . base64_encode($ciphertext);
    echo "\n";
    $with_iv = base64_encode($iv . $ciphertext . $tag);
    echo "Encrypted String with IV to be sent to Neonomics: " . $with_iv;
    echo "\n";
}

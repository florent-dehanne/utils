<?php

  /*
   * Framework: Prestashop (tested on 1.7)
   * This script copy product information from a language to another:
   * so, if you're in french, you have english name, description etc... in products.
   */

  require_once('../config.php');

  // Modify those values according to your prestashop.
  $id_shop = 1; // Id of your shop
  $id_lang = 2; // Id of new language
  $id_lang_default = 1; // Id of source language

  // Connection to DB
  $mysqli = new mysqli($config['database_host'], $config['database_user'], $config['database_password'], $config['database_name']);
  $mysqli->set_charset("utf8");

  if ($mysqli->connect_errno)
  {
    printf("Connection failed: %s\n", $mysqli->connect_error);
    exit;
  }

  /*
   * Get all products and insert them in new language.
   */

  $query = $mysqli->query("SELECT * FROM ".$config['database_prefix']."product_lang WHERE id_shop = '".$id_shop."' AND id_lang = '".$id_lang_default."'");

  while ($product = $query->fetch_object())
  {
    $insert = $mysqli->query("REPLACE INTO ".$config['database_prefix']."product_lang (id_product, id_shop, id_lang, description, description_short, link_rewrite, name)
                              VALUES ('".$mysqli->real_escape_string($product->id_product)."', '".$id_shop."', '".$id_lang."', '".$mysqli->real_escape_string($product->description)."', '".$mysqli->real_escape_string($product->description_short)."', '".$mysqli->real_escape_string($product->link_rewrite)."', '".$mysqli->real_escape_string($product->name)."')");

    if (!$insert)
      printf("Error on inserting data : %s<br />", $mysqli->sqlstate);
    else
      echo 'Copy product <strong>'.$product->id_product.'</strong><br />';
  }

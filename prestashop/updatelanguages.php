<?php

  /*
   * Framework: Prestashop (tested on 1.7)
   * This script load CSV files with product informations to insert in database.
   */

  require_once('../config.php');

  $id_shop = 1;
  $id_lang = 2;
  $id_lang_default = 1;

  /* Indicate here, your list of CSV files to import.
   * For each file, you can define the column of the CSV used for name, reference, description and short description. */
  $files = [
    'example.csv' => [
      'reference' => 0,
      'name' => 1,
      'description_short' => 2,
      'description' => 3
    ]
  ];

  // Connection to DB
  $mysqli = new mysqli($config['database_host'], $config['database_user'], $config['database_password'], $config['database_name']);
  $mysqli->set_charset("utf8");

  if ($mysqli->connect_errno)
  {
    printf("Connection failed: %s\n", $mysqli->connect_error);
    exit;
  }


  /*
   * Insert data in new language.
   */

  foreach ($files as $filename => $fields)
  {
    echo 'Open <strong>'.$filename.'</strong><br />';

    $handle = fopen('csv/'.$filename, 'r');
    $fields = $files[$filename];
    $line = 2;

    // Exclude first line
    fgetcsv($handle, 10000, ";");

    while (($data = fgetcsv($handle, 0, ";")) !== FALSE)
    {
      $reference = trim_full($data[$fields['reference']]);

      if (empty($reference))
      {
        echo 'No reference defined for product on <strong>line '.$line.'</strong><br />';
        continue;
      }

      $query = $mysqli->query("SELECT id_product FROM ".$config['database_prefix']."product WHERE reference = '".$reference."'");

      if ($query->num_rows)
      {
        $product = $query->fetch_object();

        $name = trim_full($data[$fields['name']]);
        $description = trim_full($data[$fields['description']]);
        $description_short = trim_full($data[$fields['description_short']]);
        $link_rewrite = slug($data[$fields['name']]);

        // Insert language content.
        $mysqli->query("REPLACE INTO ".$config['database_prefix']."product_lang (id_product, id_shop, id_lang, description, description_short, link_rewrite, name)
                        VALUES ('".$product->id_product."', '".$id_shop."', '".$id_lang."', '".$mysqli->real_escape_string($description)."', '".$mysqli->real_escape_string($description_short)."', '".$mysqli->real_escape_string($link_rewrite)."', '".$mysqli->real_escape_string($name)."')");

        echo $filename.': Update reference <strong>'.$reference.'</strong><br />';
      }
      else
      {
        echo $filename.': No product is matching reference <strong>'.$reference.'</strong> on <strong>line '.$line.'</strong><br />';
        continue;
      }

      $line++;
    }
  }

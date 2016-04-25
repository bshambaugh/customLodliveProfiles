<?php
//Find the values of the entity_type field and the nodes that they correspond to
// This is like b and nbid..
/* Please Create a Content Type (bundle) containing a fields for the assigned names of
 * the text_field_name and term_reference_field_name variables.
 * If you create a new Content Type containing fields for the assigned names
 * and wish to migrate content to these fields from a content Type
 * containing only a text_field_name name please create a Node Convert Template.
 * This node convert template should map the field name assigned to text_field_name from
 * the source to target content type.
 * If you choose to work with a new content type, please go to Home>Administration>Content in your
 * Drupal 7 Installation after you have created  your Node Convert Template and select
 * your template name in the Update Options along with any checkboxes for the nodes
 * you wish to target for your new content type before running this script.
 */
// Access control to the database
$user = 'your_username';
$pass = 'your_password';
// Specifiy the node table
$pn = 'node';
// Specify the url alias table
$urlalias = 'url_alias';
// specify the machine name for the link field
$linkfieldmachinename = 'field_examplelink';
// create the table name for the link field
$linkfieldmachinename_table = 'field_data_'.$linkfieldmachinename;
// The stuff below (the next two variables) needs to be consistent with what the portal page script  	c2-multiimplodewkey.php found
// among other places https://github.com/bshambaugh/movedrupalpagestomarmottaldp
// Specify a portal page LDP container
$LDPCPortal = 'Portal';
//Specify the container Marmotta is posting to
$LDPpostcontainer = 'http://localhost:8080/marmotta/ldp/';
// Specify the LodLive Path (this assumes a default LDP container for portal pages)
$lodlivepath = 'http://localhost/LodLive/app_en.html?'.$LDPpostcontainer.$LDPCPortal.'/';
// The content types of the nodes that you wish to convert. Please change to match your needs.
 $bundle_type = array('semantic_portal_page');
// Set dbg to the integer 1 to allow for debugging
$dbg = 1;
// change dbname to your database name for all instances in the code 
// Enter nid array, type, and vid
$nid_array = array();
$vid_array = array();
$type_array = array();
$alias_array = array();
///
$field_field_name_url_col = $linkfieldmachinename.'_url';
$field_field_name_title_col = $linkfieldmachinename.'_title';
$field_field_name_attributes_col = $linkfieldmachinename.'_attributes';
// ...

foreach($bundle_type as $k => $type) {
  try {
      $dbh = new PDO('mysql:host=localhost;dbname=drupal-7.42', $user, $pass);
        foreach($dbh->query("SELECT `$pn`.`nid`, `$pn`.`type`, `$pn`.`vid` from `$pn`
  WHERE `$pn`.`type` LIKE 'semantic_portal_page'") as $row) {
  //    foreach($dbh->query('SELECT * from `pan_node` LIMIT 10') as $row) {
//          print_r($row);
          if($row['nid'] !== NULL) {
          echo($row['nid']);
          array_push($nid_array, $row['nid']);
          }
          if($row['type'] !== NULL) {
          echo($row['type']);
          array_push($type_array, $row['type']);
          }
          if($row['vid'] !== NULL) {
          echo($row['vid']);
          array_push($vid_array, $row['vid']);
          }
      }
      $dbh = null;
  } catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
  }


$nid_source_array = array();
$link_title_array = array();

foreach($nid_array as $key => $value) {
    echo 'The nid array is: '.$nid_array[$key]."\r\n";
    echo 'The vid array is: ',$vid_array[$key]."\r\n";
    echo 'The type array is: ',$type_array[$key]."\r\n";
    $source_from_nid = 'node/'.$nid_array[$key];
    $nid_source_array[$key] = $source_from_nid;
}


  foreach($nid_source_array as $key => $value) {
//     echo $nid_source_array[$key]."\r\n";
  


  try {
      $dbh = new PDO('mysql:host=localhost;dbname=drupal-7.42', $user, $pass);
        foreach($dbh->query("SELECT `$urlalias`.`alias` FROM `$urlalias`
       WHERE `$urlalias`.`source` LIKE '$nid_source_array[$key]'") as $row) {
  //    foreach($dbh->query('SELECT * from `pan_node` LIMIT 10') as $row) {
//          print_r($row);
          if($row['alias'] !== NULL) {
       //   echo($row['alias']);
          array_push($alias_array, $row['alias']);
          }
      }
      $dbh = null;
  } catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
  }
 }

  foreach($nid_array as $key => $value) {
       echo 'The nid is: '.$nid_array[$key].' '.'The alias array is: '.$alias_array[$key]."\r\n";   
       preg_match('/\/[a-z0-9A-Z_-]*/',$alias_array[$key],$matches);
       $substring = $matches[0];
//       echo $substring;
//       print_r($substring);
       $cinders = preg_replace('/\//','',$substring);
//       preg_match('/[a-z-]*/',$substring,$matches);
//       print_r($matches);
       echo $cinders.' is the cleaned one'."\r\n";
       $link_title_array[$key] = $cinders;
  }

// Try to print all of the things for step 3 here..
  foreach($link_title_array as $key => $value) {
        echo 'node '.$bundle_type[$k].' '.$link_title_array[$key].' '.$nid_array[$key].' '.$vid_array[$key].' '.'und'.' '.'0'.' '.$lodlivepath.$link_title_array[$key].' '.'View in LodLive'.' '.'a:0:{}'."\r\n";
  insert_lodlive_link('node',$bundle_type[$k],0,$nid_array[$key],$vid_array[$key],'und',0,$lodlivepath.$link_title_array[$key],'View in LodLive','a:0:{}',$linkfieldmachinename_table, 
$field_field_name_url_col, $field_field_name_title_col, $field_field_name_attributes_col , $user, $pass);

   }

}

//insert_lod_live_link('node',$bundle_type[$k],$link_title_array[$key],$nid_array[$key],$vid_array[$key],'und','0',$lodlivepath.$link_title_array[$key],'View in LodLive','a:0:{}');

function insert_lodlive_link($entity_type, $bundle, $deleted, $entity_id, $revision_id, $language, $delta, $field_field_name_url, $field_field_name_title, $field_field_name_attributes, $linkfieldmachinename_table, 
$field_field_name_url_col, $field_field_name_title_col, $field_field_name_attributes_col , $user, $pass) {
  try {
      $dbh = new PDO('mysql:host=localhost;dbname=drupal-7.42', $user, $pass);
        foreach($dbh->query("INSERT INTO `$linkfieldmachinename_table` (entity_type, bundle, deleted, entity_id, revision_id, language, delta, $field_field_name_url_col, $field_field_name_title_col, $field_field_name_attributes_col) VALUES ('$entity_type', '$bundle', $deleted , $entity_id , $revision_id , '$language' , $delta , '$field_field_name_url', '$field_field_name_title', '$field_field_name_attributes')") as $row) {
      }
      $dbh = null;
  } catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
  }
}

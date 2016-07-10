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
$user = 'username';
$pass = 'password';
// Specifiy the node table
$pn = 'pan_node';
// Specify the url alias table
$urlalias = 'pan_url_alias';
// specify the machine name for the link field
$linkfieldmachinename = 'field_ldbrowser_view';
// create the table name for the link field
$linkfieldmachinename_table = 'pan_'.'field_data_'.$linkfieldmachinename;
$linkfieldmachinename_revisiontable = 'pan_'.'field_revision_'.$linkfieldmachinename;
// The stuff below (the next two variables) needs to be consistent with what the portal page script  	c2-multiimplodewkey.php found
// among other places https://github.com/bshambaugh/movedrupalpagestomarmottaldp
// Specify a portal page LDP container
$LDPCPortal = 'contentandportalpages';
//Specify the container Marmotta is posting to
$LDPpostcontainer = 'http://investors.ddns.net:8080/marmotta/ldp/';
// Specify the LodLive Path (this assumes a default LDP container for portal pages)
$lodlivepath = 'http://data.thespaceplan.com/LodLive2/app_en.html?'.$LDPpostcontainer.$LDPCPortal.'/';
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
echo "hello";
$nodename = array();
$nodeid = array();
/// dbname=drupal-7.42
foreach($bundle_type as $k => $type) {
  try {
      $dbh = new PDO('mysql:host=localhost;dbname=spacefin_staging', $user, $pass);
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

}

//$nid_input = array(1239,1240,1241);
foreach($nid_array as $key => $value) {
$source_array = array();
$alias_array = array();
echo 'The present value of the nid array is: '.$nid_array[$key]."\n";
$present_nid_array = $nid_array[$key];
//SELECT `pan_url_alias`.`source`, `pan_url_alias`.`alias` FROM `pan_url_alias` WHERE `pan_url_alias`.`source` LIKE '%1240%'
//foreach($bundle_type as $k => $type) {
  try {
      $dbh = new PDO('mysql:host=localhost;dbname=spacefin_staging', $user, $pass);
        foreach($dbh->query("SELECT `pan_url_alias`.`source`, `pan_url_alias`.`alias` FROM `pan_url_alias` WHERE `pan_url_alias`.`source` LIKE 'node/%$nid_array[$key]%'") as $row) {
  //    foreach($dbh->query('SELECT * from `pan_node` LIMIT 10') as $row) {
//          print_r($row);
          if($row['source'] !== NULL) {
          echo($row['source']);
          array_push($source_array, $row['source']);
          }
          if($row['alias'] !== NULL) {
          echo($row['alias']);
          array_push($alias_array, $row['alias']);
          }
      }
     $dbh = null;
  } catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
  }


//$nid_source_array = array();
//$link_title_array = array();

foreach($source_array as $key => $value) {
    echo 'The source array is: '.$source_array[$key]."\r\n";
//    echo 'The vid array is: ',$vid_array[$key]."\r\n";
    echo 'The alias array is: ',$alias_array[$key]."\r\n";
 //   $source_from_nid = 'node/'.$nid_array[$key];
 //   $nid_source_array[$key] = $source_from_nid;
}

$new_source_array = array_unique($source_array);
$new_alias_array = array();

print_r($new_source_array);

foreach($new_source_array as $key => $value) {
    echo $new_source_array[$key].' '.$alias_array[$key]."\n";
  $string_one = $alias_array[$key];
 preg_match('/[-A-Z0-9a-z\/]*[^-0-9]/',$string_one,$matches);
 echo $matches[0]."\n";
 $string_two = $matches[0];
 preg_match('/[\/][-A-Z0-9a-z]*/',$string_two,$matches);
 $string_four = $matches[0];
 echo $string_four."\n";
 $string_five = preg_replace('/\//','',$string_four);
 //$string_five;
echo $string_five."\n";
array_push($new_alias_array,$string_five);
}

echo $string_five.' '.$present_nid_array."\n";
array_push($nodename,$string_five);
array_push($nodeid,$present_nid_array);

foreach($new_alias_array as $key2 => $value) {
  echo $new_source_array[$key2].' '.$new_alias_array[$key2].' '.$nid_array[$key].' '.$key.' '.$present_nid_array."\n";
}
$new_source_array = array();
$new_alias_array = array();
}
//       echo 'node '.$bundle_type[$k].' '.$link_title_array[$key].' '.$nid_array[$key].' '.$vid_array[$key].' '.'und'.' '.'0'.' '.$lodlivepath.$link_title_array[$key].' '.'View in LodLive'.' '.'a:0:{}'."\r\n";
foreach($nodename as $key => $value) {
  echo 'node'.' '.$type_array[$key].' '.'0'.' '.$nid_array[$key].' '.$vid_array[$key].' '.'und'.' '.'0'.' '.$lodlivepath.$nodename[$key].' '.'View in LodLive'.' '.'a:0:{}'."\n";
insert_lodlive_link('node',$type_array[$key],0,$nid_array[$key],$vid_array[$key],'und',0,$lodlivepath.$nodename[$key],'View in LodLive','a:0:{}',$linkfieldmachinename_table,
$field_field_name_url_col, $field_field_name_title_col, $field_field_name_attributes_col , $user, $pass);
// echo $nodename[$key].' '.$nodeid[$key].' '.$nid_array[$key].' '.$vid_array[$key].' '.$type_array[$key]."\n";
}

$dbh = null;
function insert_lodlive_link($entity_type, $bundle, $deleted, $entity_id, $revision_id, $language, $delta, $field_field_name_url, $field_field_name_title, $field_field_name_attributes, $linkfieldmachinename_table,
$field_field_name_url_col, $field_field_name_title_col, $field_field_name_attributes_col , $user, $pass) {
  try {
      $dbh = new PDO('mysql:host=localhost;dbname=spacefin_staging', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
        foreach($dbh->query("INSERT INTO `$linkfieldmachinename_table` (entity_type, bundle, deleted, entity_id, revision_id, language, delta, $field_field_name_url_col, $field_field_name_title_col, $field_field_name_attributes_col) VALUES ('$entity_type', '$bundle', $deleted , $entity_id , $revision_id , '$language' , $delta , '$field_field_name_url', '$field_field_name_title', '$field_field_name_attributes')") as $row) {
      }
     // $dbh = null;
  } catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
  }

  try {
	  $dbh = new PDO('mysql:host=localhost;dbname=spacefin_staging', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
	  foreach($dbh->query("INSERT INTO `$linkfieldmachinename_revisiontable` (entity_type, bundle, deleted, entity_id, revision_id, language, delta,               -->$field_field_name_url_col, $field_field_name_title_col, $field_field_name_attributes_col) VALUES ('$entity_type', '$bundle', $deleted , $entity_id ,      -->$revision_id , '$language' , $delta , '$field_field_name_url', '$field_field_name_title', '$field_field_name_attributes')") as $row) {
		        }
	  // $dbh = null;
	     } catch (PDOException $e) {
	           print "Error!: " . $e->getMessage() . "<br/>";
	                die();
	                   }

}

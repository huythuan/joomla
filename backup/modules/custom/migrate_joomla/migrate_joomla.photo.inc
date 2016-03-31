<?php

function migrate_joomla_create_content_photo($data_joomla, $content_map) {
  
  $author_uid = db_query('SELECT uid FROM {joomla_users} WHERE juid = :juid', array(':juid' => $data_joomla->created_by))->fetch();
  $created = strtotime($data_joomla->created);
  //if created is negative drupal will throw an error so:
  if ($node->created < 0) {
    $created = 0;
  }
  
  if (!empty($data_joomla->introtext)) {
    $joomla_body = $data_joomla->introtext . "<!--break-->" . $data_joomla->fulltext;
    $joomla_teaser = $data_joomla->introtext;
  }
  else {
    $joomla_body = $data_joomla->fulltext;
    $joomla_teaser = text_summary($joomla_body);
  }
  $joomla_body = str_replace("{mospagebreak}", "", $joomla_body);

  $values = array(
      'nid' => NULL,
      'type' => 'photo',
      'title' => $data_joomla->title,
      'body' => array(
          'format' => 'full_html',
          'value' => $joomla_body,
      ),
      'uid' => $author_uid->uid,
      'created' => (string)$created,
      'status' => (bool)$data_joomla->state,
      'field_date' => array(date('Y-m-d', (string)$created)),
  );
  // get image info for this content type
  $image_info = joomla_get_image($joomla_body);

  if (!empty($image_info)) {
    $pattern = '/{gallery}(.*){\/gallery}/';
    preg_match($pattern, $joomla_body, $matches);
    $joomla_body = str_replace('{gallery}' . $matches[1] . '{/gallery}', "", $joomla_body);
    $joomla_body = str_replace('<p></p>', "", $joomla_body);
    $values['body']['value'] = $joomla_body;
  
    for ($i = 0; $i < count($image_info); $i++) {
      $data = file_get_contents($image_info[$i][2]); 
      $file = file_save_data($data, 'public://' . $image_info[$i][0]);
      
      if (!isset($image_info[$i][1])) {
        $image_title =  $values['title'];
      }
      else {
        $image_title = $image_info[$i][1];
      }
      
      $values['field_photo'][$i] = [
          'target_id' => $file->id(),
          'title' => $image_title,
      ];
    }
    $node = \Drupal::entityManager()->getStorage('node')->create($values);
    $node->save();
    $nid = $node->id();
  }
  else {
    $values['status'] = 0;
    $values['summary'] = $joomla_teaser;
    $node = \Drupal::entityManager()->getStorage('node')->create($values);
    $node->save();
    $nid = $node->id();
    \Drupal::logger('migrate_joomla')->notice('node: @node cannot add photos', array(
        '@node' => $nid,
    ));
    //watchdog('Joomla Migrate', 'Cannot import images for node id: %id', array('%id' => $node->nid), WATCHDOG_NOTICE, 'node/' . $node->nid);
  } 
  if (isset($nid)) {
    return $nid;
  }
  else {
    return FALSE;
  }
}




/**
 * Get image link and description
 * return array
 */
function joomla_get_image($string) {
  $joomla_config = \Drupal::configFactory()->getEditable('migrate_joomla.settings');
  $image = array();
  $pattern = '/{gallery}(.*){\/gallery}/';
  preg_match($pattern, $string, $matches);
  $matches[1] = str_replace(' ', '%20', $matches[1]);

  $path = $joomla_config->get('joomla_path') . '/images/galleries/' . $matches[1];
  
  if (_joomla_check_file_exist($path . '/labels.txt')) {
    $content = file_get_contents($path . '/labels.txt');
  }
  if (_joomla_check_file_exist($path . '/en-GB.labels.txt')) {
    $content = file_get_contents($path . '/en-GB.labels.txt');
  }
  if (isset( $content)) {
    $array_content = preg_split("/\\r\\n|\\r|\\n/", $content);
    foreach($array_content as $file_info) {
      $image_info = explode('||', $file_info);
      if(_joomla_check_file_exist($path . '/'. $image_info[0])) {
        $image_info[2] = $path . '/' . $image_info[0];
        $image[] = $image_info;
      }
    }
  }
  return $image;
}

/**
 * Check image file exist
 * return TRUE if exist, FALSE othewise
 * host supports curl
 */
function _joomla_check_file_exist($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  // don't download content
  curl_setopt($ch, CURLOPT_NOBODY, 1);
  curl_setopt($ch, CURLOPT_FAILONERROR, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  if(curl_exec($ch)!==FALSE) {
    return TRUE;
  }
  else {
    return false;
  }
}
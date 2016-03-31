<?php 
function migrate_joomla_create_content_video($data_joomla, $content_map) {
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
      'type' => 'video',
      'title' => $data_joomla->title,
      'body' => array(
          'format' => 'full_html',
          'value' => $joomla_body,
      ),
      'uid' => $author_uid->uid,
      'created' => (string)$created,
      'status' => (bool)$data_joomla->state,
      'field_video_date' => array(date('Y-m-d', (string)$created)),
  );
  
  // Promote to front page?
  if ($data_joomla->content_id) {
    $joomla_promote = 1;
  }
  else {
    $joomla_promote = 0;
  }
  $values['promote'] = $joomla_promote;
  
  // get video info for this content type
  $video_info = joomla_get_video($joomla_body);
  if (!empty($video_info)) {
    // remove iframe tag from body fiend
   //print_r($video_info);
    $joomla_body = preg_replace("/<iframe[^>]*src=[\"|']([^'\"]+)[\"|'][^>]*>/i", "", $joomla_body);
    //print_r($joomla_body);
    $joomla_body = preg_replace('<p class="ye-container">', "", $joomla_body);
    $joomla_body = preg_replace('</iframe>', "", $joomla_body);
    $values['body']['value'] = $joomla_body;
    $node->body[$lang['body']][0]['value'] = $joomla_body;
  //  $values['field_you_tube_url'] = 'https://www.youtube.com/watch?v=y7Qecd9yNGs&feature=youtu.be';
    // add link to video field

    for ($i = 0; $i < count($video_info); $i++) {
      $values['field_you_tube_url'][$i] = $video_info[$i];
      // print_r($node);
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
    \Drupal::logger('migrate_joomla')->notice('Cannot get video link for node id: %id', array(
        '%id' => $nid,
    ));
  }
  
  if (isset($nid)) {
    return $nid;
  }
  else {
    return FALSE;
  }
}

/**
 * Get video link for content
 * @param string content
 * return array link
 */
function joomla_get_video($content) {
  preg_match_all("/<iframe[^>]*src=[\"|']([^'\"]+)[\"|'][^>]*>/i", $content, $output );
  $return = array();
  if ( isset( $output[1][0] ) ) {
    foreach ($output[1] as $link) {
      $youtube = explode('www.', $link);
      if (isset($youtube[1])) {
        $code_array = explode('/', $youtube[1]);
        if(isset($code_array[2])) {
          $link = 'https://www.youtube.com/watch?v=' . $code_array[2];
          $return[] =  $link;
        }
      }
    }
  }
  //print_r($return);
  return $return;
}

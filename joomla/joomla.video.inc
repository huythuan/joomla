<?php 
/**
 * Create node of content type video
 * Return true if success or false otherwise. 
 */
function joomla_create_content_video($data_joomla, $content_map, $cat_alias) {
$node = new stdClass();
  if ($content_map) {
    $node->nid = $content_map->nid;
    $node = node_load($node->nid);
    $node->revision = 1;
    $node->log = 'This node was programmatically updated at ' . format_date(REQUEST_TIME, 'short', NULL, variable_get('joomla_default_language', LANGUAGE_NONE));
  } 
  else {
    $node->is_new = TRUE;
    $node->log = 'Initial creation from Joomla module';
  }
  $author_uid = db_query('SELECT uid FROM {joomla_users} WHERE juid = :juid', array(':juid' => $data_joomla->created_by))->fetch();
  if ($author_uid) {
    $node->uid = intval($author_uid->uid);
  }
  $node->title = $data_joomla->title;
  $node->status = (bool)$data_joomla->state;
  $node->created = strtotime($data_joomla->created);
  //if created is negative drupal will throw an error so:
  if ($node->created < 0) {
    $node->created = 0;
  }
  $node->language = variable_get('joomla_default_language', LANGUAGE_NONE);
  $node->changed = $joomla_changed;
  // Set content type
  $node->type = $cat_alias;

  if (!empty($data_joomla->introtext)) {
    $joomla_body = $data_joomla->introtext . "<!--break-->" . $data_joomla->fulltext;
    $joomla_teaser = $data_joomla->introtext;
  } 
  else {
    $joomla_body = $data_joomla->fulltext;
    $joomla_teaser = text_summary($joomla_body);
   }
  $joomla_body = str_replace("{mospagebreak}", "", $joomla_body);
  //images
  if ($data_joomla->images) {
    $joomla_teaser = joomla_replace_mos_image($data_joomla->images, $joomla_teaser);
    $joomla_body = joomla_replace_mos_image($data_joomla->images, $joomla_body);
  }
  $joomla_teaser = joomla_replace_image_link($joomla_teaser);
  $joomla_body = joomla_replace_image_link($joomla_body);
  $lang = field_language('node', $node, NULL, $node->language);
  $node->body[$lang['body']][0]['value'] = $joomla_body;
  $node->body[$lang['body']][0]['format'] = variable_get('joomla_input_format', JOOMLA_INPUT_FORMAT);
  $tid = db_query('SELECT tid FROM {joomla_categories} WHERE jcategoryid = :jcategoryid', array(':jcategoryid' => $data_joomla->catid))->fetchField();
  if ($tid) {
    $vid = db_query('SELECT vid FROM {taxonomy_term_data} WHERE tid = :tid', array(':tid' => $tid))->fetchField();
    $vocabulary = taxonomy_vocabulary_load($vid);
    $field_name = variable_get('joomla_field_' . $vocabulary->machine_name, FALSE);
    if ($field_name) {
      $node->{$field_name}[$lang[$field_name]][]['tid'] = $tid;
    }
  }
  // Promote to front page?
  if ($data_joomla->content_id) {
    $joomla_promote = 1;
  } 
  else {
    $joomla_promote = 0;
  }
  $node->promote = $joomla_promote;
  // get video info for this content type
  $video_info = joomla_get_video($joomla_body);
  if (!empty($video_info)) {
	 // remove iframe tag from body fiend
	 $joomla_body = preg_replace("/<iframe[^>]*src=[\"|']([^'\"]+)[\"|'][^>]*>/i", "", $joomla_body);	 
	 $joomla_body = preg_replace('<p class="ye-container"></iframe></p>', "", $joomla_body);
	 $node->body[$lang['body']][0]['value'] = $joomla_body;
	// add link to video field
    for ($i = 0; $i < count($video_info); $i++) {
	  $node->field_you_tube_url['und'][$i]['video_url'] = $video_info[$i];
	   // print_r($node);
	}	
    node_save($node);
  }
  else {
    node_save($node);
	watchdog('Joomla Migrate', 'Cannot get video link for node id: %id', array('%id' => $node->nid), WATCHDOG_NOTICE, 'node/' . $node->nid);
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
	    $return[] =  $youtube[1];
	  }
	}
  }
  //print_r($return);
  return $return;
}
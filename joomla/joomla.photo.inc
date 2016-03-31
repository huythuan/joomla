<?php
/**
 * Create node of content type photo
 * Return true if success or false otherwise. 
 */
function joomla_create_content_photo($data_joomla, $content_map, $cat_alias) { 
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
  if ($data_joomla->sectionid == 0) {
    $joomla_type = variable_get('joomla_default_static_nodetype', JOOMLA_DEFAULT_STATIC_NODETYPE);
  } 
  else {
    $joomla_type = variable_get('joomla_default_blog_nodetype', JOOMLA_DEFAULT_BLOG_NODETYPE);
  }
  $node->type = $cat_alias;
  if (module_exists('comment')) {
    $node->comment = variable_get('comment_' . $node->type, COMMENT_NODE_OPEN);
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
  // date create album
  $node->field_photo_date['und'][0] = array(
      'value' => date('Y-m-d H:i:s', $node->created),
  );
  // get image info for this content type
  $image_info = joomla_get_image($joomla_body);
  if (!empty($image_info)) {
	$node->status = 1;
    $pattern = '/{gallery}(.*){\/gallery}/';
    preg_match($pattern, $joomla_body, $matches);
	$joomla_body = str_replace('{gallery}' . $matches[1] . '{/gallery}', "", $joomla_body);
	$joomla_body = str_replace('<p></p>', "", $joomla_body);	
	$node->body[$lang['body']][0]['value'] = $joomla_body;
	for ($i = 0; $i < count($image_info); $i++) {
	  $file = xml_to_content_system_retrieve_file($image_info[$i][0], NULL, TRUE, FILE_EXISTS_RENAME);
	  if (!isset($image_info[$i][1])) {
	    $image_title = $node->title;
	  }
	  else {
	    $image_title = $image_info[$i][1];
	  }
	  $node->field_photo['und'][$i] = array(
       'fid' => $file->fid,
	   'title' => $image_title,
      );
	}
	print_r($node);
    //node_save($node);
  }
  else {
	$node->status = 0;
	$node->body[$lang['body']][0]['summary'] = $joomla_teaser;
   // node_save($node);
	watchdog('Joomla Migrate', 'Cannot import images for node id: %id', array('%id' => $node->nid), WATCHDOG_NOTICE, 'node/' . $node->nid);
  }
}

/**
 * Get image link and description
 * return array
 */
function joomla_get_image($string) {
$image = array();
  $pattern = '/{gallery}(.*){\/gallery}/';
  preg_match($pattern, $string, $matches);
  $matches[1] = str_replace(' ', '%20', $matches[1]);
  $path = variable_get('joomla_img_folder_photo_gallery', '') . $matches[1];
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
		$image_info[0] = $path . '/' . $image_info[0];
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

/*
*Attempts to get a file using drupal_http_request and to store it locally.
*Use:https://api.drupal.org/api/drupal/modules!system!system.module/function/system_retrieve_file/7 
*/
function xml_to_content_system_retrieve_file($url, $destination = NULL, $managed = FALSE, $replace = FILE_EXISTS_RENAME) {
  $parsed_url = parse_url($url);
  if (!isset($destination)) {
    $path = file_build_uri(drupal_basename($parsed_url['path']));
  }
  else {
    if (is_dir(drupal_realpath($destination))) {
      // Prevent URIs with triple slashes when glueing parts together.
      $path = str_replace('///', '//', "$destination/") . drupal_basename($parsed_url['path']);
    }
    else {
      $path = $destination;
    }
  }
  $result = drupal_http_request($url);
  if ($result->code != 200) {
    //drupal_set_message(t('HTTP error @errorcode occurred when trying to fetch @remote.', array('@errorcode' => $result->code, '@remote' => $url)), 'error');
    return FALSE;
  }
  $local = $managed ? file_save_data($result->data, $path, $replace) : file_unmanaged_save_data($result->data, $path, $replace);
  if (!$local) {
    drupal_set_message(t('@remote could not be saved to @path.', array('@remote' => $url, '@path' => $path)), 'error');
  }

  return $local;
}
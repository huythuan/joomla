<?php 
function migrate_joomla_create_content_news($data_joomla, $content_map) {
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
  
  // Remove redirect link in the link of news article for drupal site
  $joomla_body = str_replace("http://asmdc.org/redirect/redirect.php?url=", "", $joomla_body);
  $joomla_body = str_replace("http://democrats.assembly.ca.gov/members/scripts/redirect.asp?URL=", "", $joomla_body);
  $values = array(
      'nid' => NULL,
      'type' => 'news',
      'title' => $data_joomla->title,
      'body' => array(
          'format' => 'full_html',
          'value' => $joomla_body,
      ),
      'uid' => $author_uid->uid,
      'created' => (string)$created,
      'status' => (bool)$data_joomla->state,
      'field_news_date' => array(date('Y-m-d', (string)$created)),
  );
  
  // Promote to front page?
  if ($data_joomla->content_id) {
    $joomla_promote = 1;
  }
  else {
    $joomla_promote = 0;
  }
  $values['promote'] = $joomla_promote;
  
  $node = \Drupal::entityManager()->getStorage('node')->create($values);
  $node->save();
  $nid = $node->id();
  
  if (isset($nid)) {
    return $nid;
  }
  else {
    return FALSE;
  }
}
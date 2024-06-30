<?php

/*-------------------------------------------------------------------------------
投稿画面タグをカテゴリーと同じ形式で表示
--------------------------------------------------------------------------------*/
function _re_register_post_tag_taxonomy() {
  $tag_slug_args = get_taxonomy('post_tag'); // returns an object
  $tag_slug_args -> hierarchical = true;
  $tag_slug_args -> meta_box_cb = 'post_categories_meta_box';
  register_taxonomy( 'post_tag', 'post',(array) $tag_slug_args);
}
add_action( 'init', '_re_register_post_tag_taxonomy', 1 );

/*-------------------------------------------------------------------------------
日本語パーマリンクを変更
--------------------------------------------------------------------------------*/
function auto_post_slug( $slug, $post_ID, $post_status, $post_type ) {
  if ( preg_match( '/(%[0-9a-f]{2})+/', $slug ) ) {
    $slug = utf8_uri_encode( $post_type ) . '-' . $post_ID;
  }
  return $slug;
}
add_filter( 'wp_unique_post_slug', 'auto_post_slug', 10, 4  );

/*-------------------------------------------------------------------------------
投稿機能メモ追加
--------------------------------------------------------------------------------*/
// メモ表示行を投稿一覧に追加する
function memo_columns_head($defaults) {
  $defaults['WP_PostMemo'] = 'メモ';
  return $defaults;
}
function memo_columns_content($column_name, $post_ID) {
  if ($column_name == 'WP_PostMemo') {
    $memocontent = get_post_meta( $post_ID, 'mypostmemo', true );
    echo nl2br($memocontent);
  }
}
add_filter('manage_posts_columns', 'memo_columns_head');
add_action('manage_posts_custom_column', 'memo_columns_content', 10, 2);


// メモ入力欄を投稿編集画面に追加する
function _add_custom_mypostmemo(){
  add_meta_box( 'mymemomypostmemo', 'メモ', '_add_memometa_box', 'post', 'advanced', 'default', NULL );
}
add_action( 'add_meta_boxes', '_add_custom_mypostmemo' );

function _add_memometa_box( $post ){
  $value = get_post_meta( $post->ID, 'mypostmemo', TRUE );
  echo '<input type="text" name="mypostmemo" value="'.esc_attr( $value ).'" style="width:100%">';
}

//メモの保存処理
function _save_custom_mypostmemo( $post_id ){
  update_post_meta( $post_id, 'mypostmemo', $_POST[ 'mypostmemo' ] );
  return $post_id;
}
add_action( 'save_post', '_save_custom_mypostmemo' );

/*-------------------------------------------------------------------------------
コメントのURL対策
--------------------------------------------------------------------------------*/
/*コメント欄にHTMLタグエスケープ*/
function html_to_text($comment_content) {
  if ( get_comment_type() == 'comment' ) {
    $comment_content = htmlspecialchars($comment_content, ENT_QUOTES);
  }
  return $comment_content;
}
add_filter('comment_text', 'html_to_text', 9);
// オートリンク無効
remove_filter('comment_text', 'make_clickable', 9);

/*-------------------------------------------------------------------------------
投稿IDを表示
--------------------------------------------------------------------------------*/
/* 投稿一覧にIDの列を追加 */
function add_posts_columns_postid($columns) {
  $columns['postid'] = 'ID';
  echo '';
  return $columns;
}
add_filter( 'manage_posts_columns', 'add_posts_columns_postid' );
add_filter( 'manage_pages_columns', 'add_posts_columns_postid' );
  
  //IDを表示
function custom_posts_columns_postid($column_name, $post_id) {
  if( 'postid' == $column_name ) {
    echo $post_id;
  }
}
add_action( 'manage_posts_custom_column', 'custom_posts_columns_postid', 10, 2 );
add_action( 'manage_pages_custom_column', 'custom_posts_columns_postid', 10, 2 );
  
//ソート可能にする
function sort_posts_columns_postid($columns) {
  $columns['postid'] = 'ID';
  return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'sort_posts_columns_postid' );
add_filter( 'manage_edit-page_sortable_columns', 'sort_posts_columns_postid' );
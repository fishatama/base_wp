<?php

/*-------------------------------------------------------------------------------
PHPのメモリー上限の書き換え
--------------------------------------------------------------------------------*/
ini_set('memory_limit', '256M');

/*-------------------------------------------------------------------------------
wp_headタグの不要箇所削除
--------------------------------------------------------------------------------*/
remove_action( 'wp_head', 'wp_generator' ); //WordPressのバージョン情報
remove_action( 'wp_head', 'rsd_link' ); //外部アプリケーションから情報を取得するタグ
remove_action( 'wp_head', 'wlwmanifest_link' ); //Windows Live Writer用のタグ
remove_action( 'wp_head', 'index_rel_link' ); //現在の文書に対する「索引」であることを示すタグ
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 ); //「?p=投稿ID」形式のデフォルトパーマリンクタグ
//「link rel=next」等のタグ
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
//フィード関連のタグ
remove_action( 'wp_head', 'feed_links', 2);
remove_action( 'wp_head', 'feed_links_extra', 3);
//絵文字関連タグ
remove_action( 'wp_head', 'print_emoji_detection_script', 7);
remove_action( 'admin_print_scripts', 'print_emoji_detection_script');
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles');
add_filter( 'emoji_svg_url', '__return_false' );

/*-------------------------------------------------------------------------------
WPの機能の追加
--------------------------------------------------------------------------------*/
// add_theme_support
add_action( 'after_setup_theme', function(){
  add_theme_support( 'title-tag' ); // tiltleタグの追加
  add_theme_support( 'post-thumbnails' ); //サムネイル機能の追加
  add_theme_support('menus'); // カスタムメニューの追加
  add_theme_support('widgets'); // ウィジェットの追加
});

// session_start
add_action('init', function(){
  session_start();
});
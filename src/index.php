<?php get_header(); ?>
<main id="primary" class="site-main">
  <?php
    if ( have_posts() ) :
      while ( have_posts() ) :
        the_post();
        // ループ内のコンテンツを表示
        get_template_part( 'template-parts/content', get_post_format() );
      endwhile;
      // ページネーションの表示
      the_posts_pagination();
    else :
      // コンテンツが見つからない場合の表示
      get_template_part( 'template-parts/content', 'none' );
    endif;
  ?>
</main>
<?php
  get_sidebar();
  get_footer();
?>

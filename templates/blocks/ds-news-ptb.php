<?php
/**
 * ds-news-ptb.php
 */

 $title_base = __( 'Latest news', 'desymphony' );
 
 if ( is_category() ) {
     $term       = get_queried_object();
     $full_title = '<span class="wv-color-e">#' . esc_html( $term->slug ) . '</span>';
 } else {
     $current_term = '';
 
     if ( is_singular( 'post' ) ) {
         $categories_obj = get_the_category();
         if ( ! empty( $categories_obj ) ) {
             $current_term = '<span class="wv-color-e">#' . esc_html( $categories_obj[0]->slug ) . '</span>';
         }
     }
 
     $full_title = $title_base;
     if ( $current_term ) {
         $full_title .= ' › ' . $current_term;
     }
 }
 

// Fetch all post tags (non-empty)
$categories = get_terms( [
    'taxonomy'   => 'category',
    'orderby'    => 'name',
    'order'      => 'ASC',
    'hide_empty' => true,
] );
?>
<div id="ds-news-ptb" class="d-block w-100">
    <div class="container container-1024">
        <div class="d-flex align-items-center justify-content-between">
            <div class="py-12 py-lg-24">
                <h2 class="my-0 text-white"><?php echo $full_title; ?></h2>
            </div>
            <button id="ds-news-tags-toggle" class="wv-button wv-button-pill wv-button-sm wv-button-edit-dark align-items-center px-8 ls-2">
                <?php esc_html_e( 'TAGS', 'desymphony' ); ?>
                <i class="wv wv_tags-i ms-4 fs-20 d-flex" style="maring-right: -4px"></i>
                <!-- <i class="wv wv_tags-a ms-4 fs-20 d-flex">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i> -->
            </button>

        </div>
    </div>
    
    <?php if ( ! empty( $categories ) ) : ?>
        <div id="ds-news-tags" class="d-none">
            <div class="container container-1024 py-16">
                    <div class="d-flex flex-wrap gap-8 align-items-center justify-content-center">
                        <?php foreach ( $categories as $category ) :
                            $slug      = $category->slug;
                            $link      = get_term_link( $category );
                            $is_active = is_category( $category->term_id ) || ( is_singular( 'post' ) && has_category( $category->term_id ) );
                        ?>
                            <a href="<?php echo esc_url( $link ); ?>"
                            class="wv-button wv-button-md wv-button-edit-dark align-items-center fs-14 fw-400 px-16<?php echo $is_active ? ' active' : ''; ?>">
                                #<?php echo esc_html( $slug ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
            </div>
        </div>    
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const toggle = document.getElementById('ds-news-tags-toggle');
    const list   = document.getElementById('ds-news-tags');
    if ( toggle && list ) {
        toggle.addEventListener('click', () => {
            // show/hide the list
            list.classList.toggle('d-none');
            // toggle the 'active' class on the button
            toggle.classList.toggle('active');
        });
    }
});
</script>


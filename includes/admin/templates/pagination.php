<div class="tablenav top">

    <div class="alignleft actions bulkactions">

        <?php

        include "partials/search_by_name.php";

        ?>

    </div>

    <div class="tablenav-pages">
        <span class="displaying-num"><?=$pagination['count']?> items</span>

        <span class="pagination-links">
            <?php if ($pagination['current_page'] > 1) : ?>
            <a class="first-page" href="<?=esc_url ( admin_url( 'admin.php?page=' .  esc_attr(wp_unslash($_GET['page']))) )?>"><span class="screen-reader-text">First Page</span><span aria-hidden="true">«</span></a>
            <a class="prev-page" href="<?=esc_url ( admin_url( 'admin.php?page=' . esc_attr(wp_unslash($_GET['page'])) . '&paged=' . ($pagination['current_page'] - 1)) )?>">
                <span class="screen-reader-text">Previous page</span>
                <span aria-hidden="true">‹</span>
            </a>
            <?php
                  else : ?>
                <span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
            <?php
                  endif;
            ?>
            <span class="paging-input">
                <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                <input class="current-page" id="current-page-selector" type="text" name="paged" value="<?=$pagination['current_page']?>" size="1" aria-describedby="table-paging">
                <span class="tablenav-paging-text"> of <span class="total-pages"><?=$pagination['pages_count']?></span>
                </span>
            </span>
            <a class="next-page" href="<?=esc_url (admin_url( 'admin.php?page=' . esc_attr(wp_unslash($_GET['page'])) . '&paged=' . ($pagination['current_page'] + 1)) ) ?>">
                <span class="screen-reader-text">Next page</span>
                <span aria-hidden="true">›</span>
            </a>
            <a class="last-page" href="<?=esc_url (admin_url( 'admin.php?page=' . esc_attr(wp_unslash($_GET['page'])) . '&paged=' . $pagination['pages_count']) ) ?>">
                <span class="screen-reader-text">Last page</span>
                <span aria-hidden="true">»</span>
            </a>
        </span>
    </div>
    <br class="clear">
</div>

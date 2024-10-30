<form method="get" style="display: inline-block;">
    <input type="hidden" name="page" value="search_and_reports_pro"/>
    <label class="screen-reader-text" for="post-search-input">Search:</label>
    <input type="search" id="post-search-input" name="s" value="<?=isset($_GET['s']) ? esc_attr( wp_unslash($_GET['s'])) : ''?>">
    <input type="submit" id="search-submit" class="button" value="Search">
</form>
<button id="to_csv" class="button">Export to CSV</button>
<div class="ajax_status">
    <span class="ajax_loader"></span>
    <span class="ajax_success">Downloaded</span>
    <span class="ajax_error">Failed</span>
</div>

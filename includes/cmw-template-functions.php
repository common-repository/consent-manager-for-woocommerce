<?php

function cmw_get_route_name($page)
{
    $current_page = explode('/', $page);

    return isset($current_page[1]) ? $current_page[1] : 'home';
}

function cmw_parent_details_page()
{
    global $current_user;
    get_currentuserinfo();

    $parent_info = cmw_get_parent_info(get_current_user_id());

    cmw_get_template(
        'parent-details/info.php',
        array(
            'parent_info'      => $current_user->data,
            'parent_meta'      => get_user_meta($current_user->data->ID),
            'parent_extra'     => $parent_info
        )
    );
}


function cmw_adult_details_page($page)
{

    cmw_edit_adult_info($page);

}

function cmw_edit_adult_info($current_page)
{

    $user_info = get_userdata(get_current_user_id());
    $parent_info = cmw_get_parent_info(get_current_user_id());
    $route_name = cmw_get_route_name($current_page);

    cmw_get_template(
        'adult-details/edit-layout.php',
        compact('user_info', 'parent_info', 'route_name')
    );

    return;
}

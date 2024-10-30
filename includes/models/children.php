<?php

class CMW_Children {

    protected $excluded_fields = ['child_id', 'route_name', 'save'];

    public function getChildren($args)
    {
        global $wpdb;

        $where = isset($args['has_emergency'])
            ? ' AND medical_agree = 1'
            : '';

        $query = "SELECT * FROM {$wpdb->prefix}cmw_children_info WHERE user_id = " . $args['parent_id'] . $where;

        $result = $wpdb->get_results($query);

        foreach ($result as $row) {
            $row->years = cmw_get_child_years($row->DOB);
        }

        return $result;
    }

    public function getChild($id)
    {
        global $wpdb;

        $query = "SELECT * from {$wpdb->prefix}cmw_children_info where id = {$id}";

        return $wpdb->get_results($query)[0];

    }

    public function getChildByParentId($child_id, $parent_id)
    {
        global $wpdb;

        return $wpdb->query("SELECT * FROM {$wpdb->prefix}cmw_children_info WHERE id = {$child_id} AND user_id = {$parent_id}");

    }


    public function addChild($request)
    {
        global $wpdb;

        $data = [
            'user_id' => get_current_user_id(),
            'name' => sanitize_text_field($request['name']),
            'gender' => sanitize_text_field($request['gender']),
            'DOB' => date("Y-m-d", strtotime($request['dob'])),
            'school' => sanitize_text_field($request['school']),
            'address' => sanitize_text_field($request['address']),
            'postcode' => sanitize_text_field($request['postcode']),
            'medical_agree' => $request['medical_agree'] ? 1 : 0,
            'share_agree' => $request['sharing_agree'] ? 1 : 0
        ];

        $wpdb->insert(
            $wpdb->prefix . 'cmw_children_info',
            $data
        );

        if ($_FILES) {

            $this->uploadPhoto($_FILES, $wpdb->insert_id);
        }

        return $wpdb->insert_id;
    }

    public function updateAgreeInfo($request)
    {
        global $wpdb;

        $wpdb->update($wpdb->prefix . 'cmw_children_info', [
            'medical_agree' => $request['medical_agree'] ? 1 : 0,
            'share_agree' => $request['sharing_agree'] ? 1 : 0
        ], [
            'id' => $request['child_id']
        ]);
    }


    public function hasRequired($id)
    {
        global $wpdb;

        return $wpdb->query("SELECT name, DOB FROM {$wpdb->prefix}cmw_children_info WHERE id = {$id}");

    }

    public function hasConsent($id)
    {
        global $wpdb;

        return $wpdb->get_row("SELECT medical_agree FROM {$wpdb->prefix}cmw_children_info WHERE id = {$id}");

    }

    public function uploadPhoto($file, $child_id)
    {
        $user_id = get_current_user_id();

        // If there is no photo to be upload skip this step
        if (empty($file["child_photo"]["name"])) return;

        $target_dir = WP_CONTENT_DIR . "/uploads/children_photo/{$user_id}/";
        if (!is_dir($target_dir)) mkdir( $target_dir, 0755, true );
        $target_file = $target_dir . $child_id . '.jpg';
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);


        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            wc_add_notice( __( 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.', 'woocommerce' ) );

            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            wc_add_notice( __( 'Sorry, your file was not uploaded.', 'woocommerce' ) );

            // if everything is ok, try to upload file
        } else {

            if (move_uploaded_file($file["child_photo"]["tmp_name"], $target_file)) {

            } else {
                wc_add_notice( __( 'Sorry, there was an error uploading your file.', 'woocommerce' ) );

            }
        }
    }

    public function getChildrenToOrder($order_id)
    {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cmw_child_to_order WHERE order_id = {$order_id}");
    }


}

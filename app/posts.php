<?php
namespace poshtvan\app;
class posts
{
    static function searchInPosts($needle, $postTypes=['post'], $limit=10)
    {
        global $wpdb;
        $postTypesValue = implode('", "', $postTypes);
        $needle = str_replace(" ", "%", $needle);
        
        // check post_status
        // check post_type
        // check post_title
        // check post_content
        // check post_password
        // handle order by
        // handle limit
        $sql = 'SELECT * FROM '.$wpdb->posts.'
                where post_status="publish"
                AND post_type IN ("'.$postTypesValue.'")
                AND (post_title like "%1$s" OR post_content like "%1$s")
                AND post_password=""
                ORDER BY post_date DESC
                LIMIT %2$d';
        return $wpdb->get_results($wpdb->prepare($sql, '%' . $needle . '%', $limit));
    }
}
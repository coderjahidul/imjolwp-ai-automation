<?php
/**
 * AI Content Automation Class
 *
 * @package Imjolwp\Automation
 */

namespace Imjolwp\Automation;
use Imjolwp\Ai\Imjolwp_Ai_Automation_For_Wordpress_Ai_Description;
use Imjolwp\Ai\Imjolwp_Ai_Automation_For_Wordpress_Ai_Image;

class Imjolwp_Ai_Automation_For_Wordpress_Automation {
    public function __construct() {
        add_action('ai_content_generate_event', [$this, 'generate_scheduled_content'], 10, 9);
    }

    /**
     * Schedule AI content generation.
     *
     * @param string $title
     * @param int $word_count
     * @param string $language
     * @param string $focus_keywords
     * @param string $post_status
     * @param string $post_type
     * @param int $author_id
     * @param bool $post_tags
     * @param string $schedule_time
     */

    public function schedule_ai_content_generation($title, $word_count, $language, $focus_keywords, $post_status, $post_type, $author_id, $post_tags, $schedule_time) {
        // Schedule the task - 6 hours from now
        $timestamp = strtotime($schedule_time) - 6 * 60 * 60;
        if ($timestamp) {
            wp_schedule_single_event($timestamp, 'ai_content_generate_event', [$title, $word_count, $language, $focus_keywords, $post_status, $post_type, $author_id, $post_tags]);
            echo '<div class="updated"><p>AI Content Generation Scheduled!</p></div>';
        }
    }

    /**
     * Generate AI-generated content and create a WordPress post.
     *
     * @param string $title
     * @param int $word_count
     * @param string $language
     * @param string $focus_keywords
     * @param string $post_status
     * @param string $post_type
     * @param int $author_id
     * @param bool $post_tags
     */

    public function generate_scheduled_content($title, $word_count, $language, $focus_keywords, $post_status, $post_type, $author_id, $post_tags) {
        if(get_option('ai_post_description') == 1){
            // Call the generate_description function
            $generated_content = new Imjolwp_Ai_Automation_For_Wordpress_Ai_Description();
            $generated_content = $generated_content->generate_description($title, $word_count, $language, $focus_keywords);

            preg_match('/<strong>Tags:<\/strong>(.*)/', $generated_content, $matches);

            // Apply str_replace to modify the tags part
            if (isset($matches[1])) {
                // Split the tags into an array using a comma as the delimiter
                $tags_array = explode(', ', $matches[1]);

                // Rebuild the modified tags part in the HTML content
                str_replace($matches[1], implode(', ', $tags_array), $generated_content);
            }
        }else{
            $generated_content = '';
            $tags_array = null;
        }

        // Save the AI-generated content as a post
        $post_id = wp_insert_post([
            'post_title'   => $title,
            'post_content' => $generated_content,
            'post_status'  => $post_status,
            'post_type'    => $post_type,
            'post_author'  => $author_id
        ]);

        // Set post tags (this is handled separately)
        if ($post_tags == true && !empty($tags_array)) {
            wp_set_post_tags($post_id, $tags_array);
        }

        // Set featured image
        if(get_option('ai_post_image') == 1) {
            $set_featured_image = new Imjolwp_Ai_Automation_For_Wordpress_Ai_Image();
            $set_featured_image->generate_image($title, $post_id);
        }

        if (is_wp_error($post_id)) {
            // Log error if post creation fails
            error_log('AI Content Generation Error: ' . $post_id->get_error_message());
        } else {
            // Log success
            error_log('AI Content Successfully Generated: Post ID ' . $post_id);

            // Optionally, send an email notification
            $admin_email = get_option('admin_email');
            $post_edit_link = get_edit_post_link($post_id);
            $message = "A new AI-generated post has been created.\n\nTitle: {$title}\n\nView/Edit: {$post_edit_link}";

            wp_mail($admin_email, 'New AI-Generated Post Created', $message);
        }
    }
    
}


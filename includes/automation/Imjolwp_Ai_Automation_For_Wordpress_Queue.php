<?php
/**
 * Summary of namespace Imjolwp\Automation
 */
namespace Imjolwp\Automation;
use Imjolwp\Ai\Imjolwp_Ai_Automation_For_Wordpress_Ai_Description;
use Imjolwp\Ai\Imjolwp_Ai_Automation_For_Wordpress_Ai_Image;

class Imjolwp_Ai_Automation_For_Wordpress_Queue{

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

    public function queue_ai_content_generation($title, $word_count, $language, $focus_keywords, $post_status, $post_type, $author_id, $post_tags){
        // Generate AI Description if enabled.
        if(get_option('ai_post_description') == 1){
            // Call the generate_description function
            $generated_content = new Imjolwp_Ai_Automation_For_Wordpress_Ai_Description();
            $generated_content = $generated_content->generate_description($title, $word_count, $language, $focus_keywords);

            // Call the post_tags_function
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
        
        // Save as Post immediately
        $post_id = wp_insert_post([
            'post_title'   => $title,
            'post_content' => $generated_content,
            'post_status'  => $post_status,
            'post_type'    => $post_type
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

        if ($post_id) {
            echo '<div class="updated"><p>' . esc_html__('AI Content Generated!', 'imjolwp-ai-automation') . 
                 ' <a href="' . esc_url(get_edit_post_link($post_id)) . '">' . esc_html__('Edit Post', 'imjolwp-ai-automation') . '</a></p></div>';
        } else {
            echo '<div class="error"><p>' . esc_html__('Failed to generate content.', 'imjolwp-ai-automation') . '</p></div>';
        }
        
    }
}
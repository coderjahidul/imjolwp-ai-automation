<?php
/**
 * Summary of namespace Imjolwp\Admin\Settings
 */
namespace Imjolwp\Admin\Settings;

class Imjolwp_Ai_Automation_For_Wordpress_Dashboard {
    /**
     * Display the admin dashboard page.
     *
     * @since 1.0.0
     */
    public function display_dashboard_page() {
        ?>
        <div class="ai-content-generator-wrap">
            <h1><?php esc_html_e('Welcome to ImjolWP AI Automation', 'imjolwp-ai-automation'); ?></h1>
            <div class="ai-content-generator-container">
                <div class="ai-content-generator-section">
                    <h2><?php esc_html_e('AI Features', 'imjolwp-ai-automation'); ?></h2>
                    <div class="ai-features-grid">
                    <?php
                    $features = [
                        'ai_post_title' => __('Post Title', 'imjolwp-ai-automation'),
                        'ai_post_description' => __('Post Description', 'imjolwp-ai-automation'),
                        'ai_post_tags' => __('Post Tags', 'imjolwp-ai-automation'),
                        'ai_post_image' => __('Post Image', 'imjolwp-ai-automation'),
                        'ai_post_seo_meta_description' => __('Post SEO Meta Description', 'imjolwp-ai-automation'),
                        'ai_post_audio' => __('Post Audio', 'imjolwp-ai-automation'),
                        'ai_post_video' => __('Post Video', 'imjolwp-ai-automation'),
                    ];

                    $pro_features = ['ai_post_audio', 'ai_post_video']; // Features for Pro version

                    foreach ($features as $key => $label) {
                        $enabled = get_option($key, '0'); // Default to disabled
                        ?>
                        <div class="ai-feature-card">
                            <h3><?php echo esc_html($label); ?></h3>

                            <?php
                            /* translators: %s represents the label name in lowercase. */
                            echo '<p>' . esc_html(sprintf(__('Generate %s using AI.', 'imjolwp-ai-automation'), strtolower($label))) . '</p>';
                            ?>
                            
                            <?php if (in_array($key, $pro_features)) { ?>
                                <span class="pro-badge"><?php esc_html_e('Pro Feature', 'imjolwp-ai-automation'); ?></span>
                            <?php } else { ?>
                                <label class="switch">
                                    <input type="checkbox" class="toggle-feature" data-feature="<?php echo esc_attr($key); ?>" <?php checked($enabled, '1'); ?>>
                                    <span class="slider"></span>
                                </label>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue admin scripts and add inline JavaScript.
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_script('jquery');

        // Enqueue a dummy script (WordPress requires an existing script handle to attach inline script)
        wp_register_script('imjolwp-ai-admin-script', '');
        wp_enqueue_script('imjolwp-ai-admin-script');

        // Add inline script
        wp_add_inline_script('imjolwp-ai-admin-script', '
            jQuery(document).ready(function($) {
                $(".toggle-feature").on("change", function() {
                    let feature = $(this).data("feature");
                    let status = $(this).is(":checked") ? 1 : 0;

                    $.ajax({
                        url: ajaxurl,
                        method: "POST",
                        data: {
                            action: "toggle_ai_feature",
                            feature: feature,
                            status: status,
                            _ajax_nonce: "' . esc_js(wp_create_nonce('toggle_ai_feature_nonce')) . '"
                        },
                        success: function(response) {
                            if (response.success) {
                                console.log(feature + " updated successfully");
                            } else {
                                alert("Failed to update setting.");
                                $(this).prop("checked", !status);
                            }
                        },
                        error: function() {
                            alert("An error occurred.");
                            $(this).prop("checked", !status);
                        }
                    });
                });
            });
        ');
    }
}

// Hook the script enqueue function
add_action('admin_enqueue_scripts', [new Imjolwp_Ai_Automation_For_Wordpress_Dashboard(), 'enqueue_admin_scripts']);

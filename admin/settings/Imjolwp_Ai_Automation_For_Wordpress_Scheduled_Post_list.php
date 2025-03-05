<?php 
/**
 * Summary of namespace Imjolwp\Admin\Settings
 */
namespace Imjolwp\Admin\Settings;

class Imjolwp_Ai_Automation_For_Wordpress_Scheduled_Post_list{
    /**
	 * Display The Scheduled Post List.
	 *
	 * @since 1.0.0
	 */

     public function imjolwp_ai_scheduled_events_list() {
		?>
		<div class="wrap">
			<h1>ImjolWP AI Post Schedule Event List</h1>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th>Title</th>
						<th>Word Count</th>
						<th>Language</th>
						<th>Focus Keywords</th>
						<th>Post Status</th>
						<th>Post Type</th>
						<th>Author</th>
						<th>Scheduled Time</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$cron_jobs = _get_cron_array();
					$found = false;
	
					foreach ($cron_jobs as $timestamp => $events) {
						foreach ($events as $hook => $details) {
							if ($hook === 'ai_content_generate_event') {
								foreach ($details as $event) {
									$args = $event['args'];
									$found = true;
									?>
									<tr>
										<td><?php echo esc_html($args[0]); ?></td>
										<td><?php echo esc_html($args[1]); ?></td>
										<td><?php echo esc_html($args[2]); ?></td>
										<td><?php echo esc_html($args[3]); ?></td>
										<td><?php echo esc_html($args[4]); ?></td>
										<td><?php echo esc_html($args[5]); ?></td>
										<td><?php echo esc_html(get_userdata($args[6])->display_name); ?></td>
										<td><?php echo date('Y-m-d H:i:s', $timestamp + 6 * 60 * 60); ?></td>
										<td>
											<a href="?page=imjolwp-ai-scheduled-posts&edit=<?php echo esc_attr($timestamp); ?>" class="button">Edit</a>
											<a href="?page=imjolwp-ai-scheduled-posts&delete=<?php echo esc_attr($timestamp); ?>" class="button button-danger">Delete</a>
										</td>
									</tr>
									<?php
								}
							}
						}
					}
	
					if (!$found) {
						echo '<tr><td colspan="9">No scheduled posts found.</td></tr>';
					}
					?>
				</tbody>
			</table>
	
			<?php
			// Check if editing
			if (isset($_GET['edit'])) {
				$edit_timestamp = intval($_GET['edit']);
				foreach ($cron_jobs as $timestamp => $events) {
					if ($timestamp == $edit_timestamp) {
						foreach ($events as $hook => $details) {
							if ($hook === 'ai_content_generate_event') {
								$args = reset($details)['args'];
								print_r($args);
								?>
								<h2>Edit Scheduled Post</h2>
                                <form method="post" class="ai-content-form">
                                    <input type="hidden" name="edit_timestamp" value="<?php echo esc_attr($edit_timestamp); ?>">

                                    <table class="form-table">
                                        <tr>
                                            <th scope="row"><label for="title">Title</label></th>
                                            <td><input type="text" id="title" name="title" value="<?php echo esc_attr($args[0]); ?>" required class="regular-text"></td>
                                        </tr>

										<tr>
                                            <th scope="row"><label for="focus_keywords">Focus Keywords</label></th>
                                            <td><input type="text" id="focus_keywords" name="focus_keywords" value="<?php echo esc_attr($args[3]); ?>" required class="regular-text"></td>
                                        </tr>

                                        <tr>
                                            <th scope="row"><label for="word_count">Word Count</label></th>
                                            <td><input type="number" id="word_count" name="word_count" value="<?php echo esc_attr($args[1]); ?>" required class="small-text"></td>
                                        </tr>

										<tr>
											<th scope="row"><label for="language">Language</label></th>
											<td>
												<select id="language" name="language" class="regular-select">
													<option value="en" <?php selected( $args[2], 'en' )?>>English</option>
													<option value="es" <?php selected( $args[2], 'es' )?>>Spanish</option>
													<option value="fr" <?php selected( $args[2], 'fr' )?>>French</option>
													<option value="de" <?php selected( $args[2], 'de' )?>>German</option>
													<option value="bn" <?php selected( $args[2], 'bn' )?>>বাংলা (Bangla)</option>
												</select>
											</td>
										</tr>

                                        <tr>
                                            <th scope="row"><label for="post_status">Post Status</label></th>
                                            <td>
                                                <select id="post_status" name="post_status" class="regular-select">
                                                    <option value="draft" <?php selected($args[4], 'draft'); ?>>Draft</option>
                                                    <option value="publish" <?php selected($args[4], 'publish'); ?>>Publish</option>
													<option value="pending" <?php selected($args[4], 'pending'); ?>>Pending Review</option>
													<option value="private" <?php selected($args[4], 'private'); ?>>Private</option>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th scope="row"><label for="post_type">Post Type</label></th>
                                            <td>
												<select id="post_type" name="post_types" class="regular-select">
													<?php
														$post_types = get_post_types(['public' => true], 'objects');
														foreach ($post_types as $post_type) {
															echo '<option value="' . esc_attr($post_type->name) . '" ' . selected($args[5], $post_type->name) . '>' . esc_html($post_type->labels->singular_name) . '</option>';
														}
													?>
												</select>
											</td>
                                        </tr>

                                        <tr>
                                            <th scope="row"><label for="author_id">Author ID</label></th>
                                            <td><input type="number" id="author_id" name="author_id" value="<?php echo esc_attr($args[6]); ?>" required class="small-text"></td>
                                        </tr>

                                        <tr>
                                            <th scope="row">&nbsp;</th>
                                            <td><input type="submit" name="update_schedule" value="Update Schedule" class="button button-primary"></td>
                                        </tr>
                                    </table>
                                </form>
								<?php
							}
						}
					}
				}
			}
	
			// Update scheduled event
			if (isset($_POST['update_schedule'])) {
				$old_timestamp = intval($_POST['edit_timestamp']);
				$new_args = [
					sanitize_text_field($_POST['title']),
					intval($_POST['word_count']),
					sanitize_text_field($_POST['language']),
					sanitize_text_field($_POST['focus_keywords']),
					sanitize_text_field($_POST['post_status']),
					sanitize_text_field($_POST['post_type']),
					intval($_POST['author_id'])
				];
	
				// Remove old event
				wp_unschedule_event($old_timestamp, 'ai_content_generate_event');
	
				// Schedule new event
				$new_timestamp = time() + 300; // 5 minutes later
				wp_schedule_single_event($new_timestamp, 'ai_content_generate_event', $new_args);
	
				echo '<div class="updated"><p>Scheduled post updated.</p></div>';
			}
	
			// Delete scheduled event
			if(isset($_GET['delete'])){
				$delete_timestamp = intval($_GET['delete']);
				$args = isset($args) ? $args : [];

				// Unschedule the event using the timestamp and arguments
				wp_unschedule_event($delete_timestamp, 'ai_content_generate_event', $args);
				echo '<div class="updated"><p>Scheduled post deleted.</p></div>';
			}
			?>
		</div>
		<?php
	}
}
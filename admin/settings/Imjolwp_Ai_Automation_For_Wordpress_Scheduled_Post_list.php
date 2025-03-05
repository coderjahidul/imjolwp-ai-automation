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
								?>
								<h2>Edit Scheduled Post</h2>
								<form method="post">
									<input type="hidden" name="edit_timestamp" value="<?php echo esc_attr($edit_timestamp); ?>">
									<p>
										<label>Title:</label>
										<input type="text" name="title" value="<?php echo esc_attr($args[0]); ?>" required>
									</p>
									<p>
										<label>Word Count:</label>
										<input type="number" name="word_count" value="<?php echo esc_attr($args[1]); ?>" required>
									</p>
									<p>
										<label>Language:</label>
										<input type="text" name="language" value="<?php echo esc_attr($args[2]); ?>" required>
									</p>
									<p>
										<label>Focus Keywords:</label>
										<input type="text" name="focus_keywords" value="<?php echo esc_attr($args[3]); ?>" required>
									</p>
									<p>
										<label>Post Status:</label>
										<select name="post_status">
											<option value="draft" <?php selected($args[4], 'draft'); ?>>Draft</option>
											<option value="publish" <?php selected($args[4], 'publish'); ?>>Publish</option>
										</select>
									</p>
									<p>
										<label>Post Type:</label>
										<input type="text" name="post_type" value="<?php echo esc_attr($args[5]); ?>" required>
									</p>
									<p>
										<label>Author ID:</label>
										<input type="number" name="author_id" value="<?php echo esc_attr($args[6]); ?>" required>
									</p>
									<p>
										<input type="submit" name="update_schedule" value="Update Schedule" class="button button-primary">
									</p>
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
            if (isset($_GET['delete'])) { // Corrected from 'cancel' to 'delete'
                $timestamp = intval($_GET['delete']);
                wp_unschedule_event($timestamp, 'ai_content_generate_event');
                echo '<div class="updated"><p>Scheduled post deleted.</p></div>';
            }

			?>
		</div>
		<?php
	}
}
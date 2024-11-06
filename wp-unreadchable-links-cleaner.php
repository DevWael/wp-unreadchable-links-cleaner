<?php
/**
 * Plugin Name: WP Unreachable Links Cleaner
 * Description: A WP-CLI command to remove unreachable links from all posts in WordPress based on a provided list. The log file path is customizable.
 * Version: 1.0
 * Author: Ahmad Wael
 * License: GPL2+
 * Text Domain: unreachable-links-cleaner
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load WP-CLI command only in WP-CLI context.
if ( defined( 'WP_CLI' ) && WP_CLI ) {

	/**
	 * WP-CLI command to remove unreachable URLs from all posts.
	 */
	class Remove_Unreachable_Links_Command {

		/**
		 * Log file path.
		 *
		 * @var string
		 */
		private $log_file;

		/**
		 * List of URLs to remove.
		 *
		 * @var array
		 */
		private $urls_to_remove = [];

		/**
		 * Initialize command, including setting the log file path.
		 *
		 * @param string|null $log_file_path Optional path to the log file.
		 */
		public function __construct( $log_file_path = null ) {
			// Define a dynamic log file path if none is provided.
			$this->log_file = $log_file_path ? $log_file_path : ABSPATH . 'wp-content/unreachable_links_log_' . gmdate( 'Y-m-d_H-i-s' ) . '.txt';
			// Initialize the log file.
			file_put_contents( $this->log_file, "Unreachable Links Log\n\n" );
		}

		/**
		 * Remove unreachable links from all posts.
		 *
		 * ## OPTIONS
		 *
		 * <file>
		 * : Path to the text file containing URLs to remove.
		 *
		 * [--log=<log_file>]
		 * : Optional path for the log file. If not provided, a dynamic file is created in wp-content.
		 *
		 * ## EXAMPLES
		 *     wp remove_unreachable_links run /path/to/urls-to-remove.txt --log=/path/to/logfile.txt
		 *
		 * @when after_wp_load
		 */
		public function __invoke( $args, $assoc_args ) {
			$file_path = $args[0];
			if ( ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
				WP_CLI::error( "File not found or not readable: $file_path" );
			}
			$this->urls_to_remove = array_map( 'trim', file( $file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) );

			$log_file_path = $assoc_args['log'] ?? null;
			$this->__construct( $log_file_path );

			global $wpdb;
			$total_posts = (int) $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_status = 'publish'" );
			$batch_size  = 1000;
			$offset      = 0;

			$progress = \WP_CLI\Utils\make_progress_bar( 'Processing posts', $total_posts );

			while ( $posts = $wpdb->get_results( "SELECT ID, post_content FROM {$wpdb->posts} WHERE post_status = 'publish' LIMIT {$offset}, {$batch_size}" ) ) {
				foreach ( $posts as $post ) {
					$updated_content = $this->check_and_remove_unreachable_links( $post->post_content, $post->ID );

					if ( $updated_content !== $post->post_content ) {
						$wpdb->update(
							$wpdb->posts,
							[ 'post_content' => $updated_content ],
							[ 'ID' => $post->ID ]
						);
						clean_post_cache( $post->ID );
					}

					$progress->tick();
				}

				$offset += $batch_size;
				usleep( 10000 );
			}

			$progress->finish();
			WP_CLI::success( 'Finished removing unreachable links from posts.' );
		}

		/**
		 * Checks each link in the content for reachability and removes it if unreachable or in the list of URLs to remove.
		 *
		 * @param string $content Post content to process.
		 * @param int    $post_id The ID of the post for logging.
		 * @return string Modified post content.
		 */
		private function check_and_remove_unreachable_links( $content, $post_id ) {
			$pattern = '/<a\s+[^>]*href=(["\'])(.*?)\1[^>]*>(.*?)<\/a>/i';

			return preg_replace_callback( $pattern, function ( $matches ) use ( $post_id ) {
				$url       = $matches[2];
				$link_text = $matches[3];

				if ( in_array( $url, $this->urls_to_remove, true ) || ! $this->is_url_reachable( $url ) ) {
					file_put_contents( $this->log_file, "Removed URL in Post ID {$post_id}: {$url}\n", FILE_APPEND );
					return $link_text;
				}

				return $matches[0];
			}, $content );
		}

		/**
		 * Check if a URL is reachable.
		 *
		 * @param string $url URL to check.
		 * @return bool True if reachable, false otherwise.
		 */
		private function is_url_reachable( $url ) {
			$response = wp_remote_head( $url );
			return ! is_wp_error( $response ) && in_array( wp_remote_retrieve_response_code( $response ), [ 200, 301, 302 ], true );
		}
	}

	// Register the command with WP-CLI.
	WP_CLI::add_command( 'remove_unreachable_links', 'Remove_Unreachable_Links_Command' );
}

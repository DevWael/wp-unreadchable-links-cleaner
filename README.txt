=== WP Unreachable Links Cleaner ===
Contributors: bbioon
Tags: wp-cli, link removal, content management
Requires at least: 5.0
Tested up to: 6.3
Stable tag: 1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A WP-CLI command to remove unreachable links from posts based on a provided list or URL reachability.

== Description ==

This plugin adds a WP-CLI command that checks posts for unreachable links and removes them, retaining the link text. It accepts an optional log file path or creates a timestamped log file by default.

== Installation ==

1. Upload `remove-unreachable-links` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Run the WP-CLI command to start removing links.

== Usage ==

Run the command with the URL file path and optional log path:

```bash
wp remove_unreachable_links run /path/to/urls-to-remove.txt --log=/path/to/logfile.txt

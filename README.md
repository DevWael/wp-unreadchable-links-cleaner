# Remove Unreachable Links

A WP-CLI command that checks all posts for unreachable links or links from a provided list and removes them, leaving the link text intact.

## Installation

1. Download the plugin files.
2. Place them in the `wp-content/plugins/remove-unreachable-links` directory.
3. Activate the plugin in the WordPress admin dashboard.

## Usage

This plugin registers a WP-CLI command `remove_unreachable_links` with options for removing unreachable URLs.

### Command Example

```bash
wp remove_unreachable_links run /path/to/urls-to-remove.txt --log=/path/to/logfile.txt

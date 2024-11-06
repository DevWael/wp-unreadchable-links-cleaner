# Remove Unreachable Links

A WP-CLI command that checks all posts for unreachable links or links from a provided list and removes them, leaving the link text intact.

## Key Features

- **Detect Unreachable Links**: Identifies URLs that return error statuses (4xx or 5xx) across all WordPress posts.
- **Customizable Link Removal**: Optionally remove specific URLs by supplying a list in a text file.
- **Maintain Content Integrity**: Removes only the link while retaining the text within each hyperlink.
- **Logging Functionality**: Records all changes in a customizable log file.
- **Batch Processing for Performance**: Processes posts in manageable batches to maintain optimal performance on large databases.
- **Progress Tracking**: Provides real-time progress feedback via a WP-CLI progress bar.

## Installation

1. Download the plugin files.
2. Place them in the `wp-content/plugins/remove-unreachable-links` directory.
3. Activate the plugin in the WordPress admin dashboard.

## Usage

This plugin registers a WP-CLI command `remove_unreachable_links` with options for removing unreachable URLs.

### Command Example

```bash
wp remove_unreachable_links run /path/to/urls-to-remove.txt --log=/path/to/logfile.txt
```

## Benefits for SEO and User Experience

Removing unreachable links is essential for maintaining SEO integrity, avoiding potential penalties from search engines for broken links, and providing users with a more seamless navigation experience. WP Unreachable Links Cleaner simplifies this process, especially for sites with high volumes of content.

## License

WP Unreachable Links Cleaner is licensed under the GPLv2 (or later) license, ensuring it’s free and open-source for personal and commercial use.
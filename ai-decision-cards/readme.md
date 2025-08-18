# AI Decision Cards

A WordPress plugin that converts Slack-style conversations into AI-generated Decision Cards with summaries and action items using OpenAI-compatible APIs.

## Description

This plugin allows teams to easily document important decisions made in Slack conversations by:
- Pasting conversation transcripts into a WordPress admin interface
- Using AI (OpenAI or compatible APIs) to generate structured summaries
- Creating Decision Cards as custom WordPress posts with metadata (status, owner, due date)
- Preserving decision context and action items for future reference

## Features

- **Custom Post Type**: `decision_card` for organizing decision records
- **AI-Powered Summarization**: Uses OpenAI or compatible APIs to analyze conversations
- **Structured Metadata**: Track decision status (Proposed/Approved/Rejected), owner, and due dates
- **Admin Interface**: Simple form-based interface for inputting conversations
- **Draft Workflow**: Generated cards are saved as drafts for review before publishing
- **Security**: Proper nonce verification, input sanitization, and capability checks
- **Internationalization Ready**: Supports translation with `ai-decision-cards` text domain

## Installation

### Method 1: Manual Installation
1. Download or clone this repository
2. Copy the `ai-decision-cards` folder to your `/wp-content/plugins/` directory
3. Activate the plugin through the **Plugins** menu in WordPress

### Method 2: ZIP Upload
1. Create a ZIP file of the `ai-decision-cards` folder
2. Go to **Plugins → Add New → Upload Plugin** in WordPress admin
3. Upload the ZIP file and activate

## Configuration

1. Navigate to **Decision Cards → Settings** in your WordPress admin
2. Enter your OpenAI API key (required)
3. Optionally configure:
   - **API Base URL**: Default is `https://api.openai.com/` (change for OpenRouter or other compatible services)
   - **Model**: Default is `gpt-3.5-turbo` (can use `gpt-4` or other chat models)

## Usage

1. Go to **Decision Cards → Generate** in WordPress admin
2. Paste your Slack conversation transcript in the text area
3. Set the decision metadata:
   - **Status**: Proposed, Approved, or Rejected
   - **Owner**: Person responsible for the decision
   - **Due Date**: Deadline for action items (if applicable)
4. Click **Generate Summary & Create Draft**
5. Review the generated Decision Card in the post editor
6. Edit if needed and publish

## API Requirements

- Valid OpenAI API key with active credits/subscription
- Internet connection for API calls
- Each generation uses your OpenAI credits (typically a few cents per conversation)

## Limitations

- Conversation length limited by API context window (~4000 tokens for GPT-3.5)
- Manual input only (no direct Slack integration in this version)
- AI summaries should be reviewed for accuracy
- Requires server-side API access (won't work on restrictive hosting)

## WordPress Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- User capabilities: `edit_posts` for generating cards, `manage_options` for settings

## Development

This plugin follows WordPress coding standards and best practices:
- Singleton pattern for main plugin class
- Proper hook initialization
- Internationalization support
- Security through nonces and sanitization
- Custom post type and meta field registration

## Changelog

### 1.0.0
- Initial release
- Custom post type for Decision Cards
- AI-powered conversation summarization
- Admin interface for generation and settings
- Meta fields for status, owner, and due date

## Support

For issues, feature requests, or contributions, please visit the plugin repository or contact the development team.

## License

This plugin is licensed under GPL v2 or later.
# AI Decision Cards

A comprehensive WordPress plugin that converts Slack-style conversations into AI-generated Decision Cards with structured summaries, action items, and metadata using OpenAI-compatible APIs.

## Description

Transform your team's conversation transcripts into professional Decision Cards with AI-powered summarization. This plugin helps teams document important decisions made in Slack conversations by creating structured records with consistent formatting, searchable content, and embedded display options.

### Key Benefits
- **Consistent Documentation**: All Decision Cards follow a standardized 5-section format
- **AI-Powered Analysis**: Automatic extraction of key decisions, rationale, and action items
- **Flexible Display**: Multiple shortcodes for embedding cards in pages and posts
- **Public Showcasing**: Dedicated display pages for sharing decisions with stakeholders
- **Enhanced Search**: Full-text search across all Decision Card content
- **Smart Metadata**: Status tracking, ownership assignment, and due date management

## Features

### 🎯 Core Functionality
- **Custom Post Type**: `decision_card` for organizing decision records with public visibility
- **AI-Powered Summarization**: Uses OpenAI or compatible APIs to analyze conversations
- **Fixed 5-Section Structure**: Consistent output format for all Decision Cards
  - **Decision**: One-sentence summary of what was decided
  - **Summary**: Exactly 3 concise bullet points with key rationale
  - **Action Items**: Task assignments with intelligent relative date handling
  - **Sources**: 2-3 quoted lines from original conversation with timestamps
  - **Risks/Assumptions**: Identified concerns or assumptions, "None" if not applicable
- **Meta Banner**: Visual status display (Status | Owner | Target) at top of each card
- **Smart Date Processing**: Automatically handles relative dates ("next week", "month end") with follow-up tasks

### 🎨 Display & Embedding
- **Comprehensive Shortcode System**: Two powerful shortcodes for flexible content embedding
  - `[decision-cards-list]` - Display filterable grid with pagination and search
  - `[decision-card id="123"]` - Embed specific Decision Card with display options
- **Public Display Pages**: Dedicated showcase pages accessible to website visitors
- **Enhanced Full-Text Search**: Searches both titles and complete Decision Card content across all 5 sections
- **URL Parameter Integration**: Shortcode filters respond to query parameters
  - `?aidc_search=term` - Pre-populate search field
  - `?aidc_status=Approved` - Filter by status
  - `?aidc_owner=John` - Filter by owner
- **Responsive Design**: Mobile-friendly display for all components

### 🛠️ Admin Experience
- **Structured Metadata**: Track decision status (Proposed/Approved/Rejected), owner, and due dates
- **Admin Interface**: Clean form-based interface for inputting conversations
- **Draft Workflow**: Generated cards saved as drafts for review before publishing
- **Preview Functionality**: Full preview capability for generated Decision Cards
- **User Guidance**: Built-in documentation and copy-paste shortcuts in admin screens
- **API Provider Flexibility**: Supports OpenAI and OpenAI-compatible services (OpenRouter)

### 🔧 Technical Features
- **Security**: Proper nonce verification, input sanitization, and capability checks
- **Internationalization Ready**: Full translation support with `ai-decision-cards` text domain
- **Performance Optimized**: Efficient search implementation and streamlined API calls
- **HTML Output**: Direct HTML generation for perfect rendering without Markdown conversion
- **WordPress Standards**: Follows WordPress coding standards and best practices

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
2. Configure your API settings:
   - **API Type**: Choose between OpenAI or OpenAI Compatible
   - **API Key**: Enter your OpenAI API key (required)
   - **API Base URL**: Default is `https://api.openai.com/` (change for OpenRouter or other services)
   - **Model**: Default is `gpt-3.5-turbo` (can use `gpt-4` or other chat models)
3. Test your API connection using the built-in test feature

## Usage

### Generating Decision Cards
1. Go to **Decision Cards → Generate** in WordPress admin
2. Paste your Slack conversation transcript in the text area
3. Set the decision metadata:
   - **Status**: Proposed, Approved, or Rejected
   - **Owner**: Person responsible for the decision
   - **Due Date**: Deadline for action items (if applicable)
4. Click **Generate Summary & Create Draft**
5. Review the generated Decision Card in the post editor
6. Edit if needed and publish

### Displaying Decision Cards

#### Using Shortcodes
**List Display:**
```
[decision-cards-list]                    // Display all cards with filters
[decision-cards-list limit="5"]          // Show only 5 cards
[decision-cards-list status="Approved"]  // Show only approved cards
[decision-cards-list owner="John"]       // Filter by owner
[decision-cards-list show_filters="no"]  // Hide search filters
```

**Single Card Display:**
```
[decision-card id="123"]                 // Display specific card
[decision-card id="123" show_meta="no"]  // Hide status banner
[decision-card id="123" excerpt_only="yes"] // Show excerpt only
```

#### Public Display Pages
Access dedicated showcase pages at:
- `/decision-cards-display/` - Main display page with full functionality
- Includes search, filtering by status and owner, pagination
- Responsive design for all devices

### Advanced Features

#### URL Parameters
Shortcode filters automatically respond to URL parameters:
- `?aidc_search=budget` - Pre-populate search with "budget"
- `?aidc_status=Approved&aidc_owner=John` - Filter by status and owner
- Form submissions preserve existing query parameters

#### Full-Text Search
Search functionality covers:
- Decision Card titles
- All 5 content sections (Decision, Summary, Action Items, Sources, Risks/Assumptions)
- Enhanced discoverability based on specific keywords

## API Requirements

- Valid OpenAI API key with active credits/subscription
- Internet connection for API calls
- Each generation uses your OpenAI credits (typically a few cents per conversation)
- API rate limits apply based on your OpenAI plan

## Limitations

- Conversation length limited by API context window (~4000 tokens for GPT-3.5)
- Manual input only (no direct Slack integration in this version)
- AI summaries should be reviewed for accuracy
- Requires server-side API access (won't work on restrictive hosting)

## WordPress Requirements

- **WordPress**: 6.0 or higher
- **PHP**: 7.4 or higher
- **User Capabilities**: 
  - `edit_posts` for generating Decision Cards
  - `manage_options` for settings configuration

## Development

This plugin follows WordPress coding standards and best practices:
- Singleton pattern for main plugin class
- Proper hook initialization and cleanup
- Comprehensive internationalization support
- Security through nonces, sanitization, and capability checks
- Custom post type and meta field registration
- Responsive CSS design principles

## Changelog

### 1.2.0
- **Enhanced Shortcode System**: Comprehensive embedding options with `[decision-cards-list]` and `[decision-card]`
- **Full-Text Search**: Enhanced search across all Decision Card content sections
- **URL Parameter Integration**: Shortcode filters respond to query parameters
- **Owner Filtering**: Extended filtering capabilities in both shortcodes and display pages
- **Public Display Pages**: Dedicated showcase pages for website visitors
- **User Guidance Integration**: Built-in documentation and copy-paste shortcuts
- **Responsive Design**: Mobile-friendly display for all components
- **Edit Screen Enhancements**: Shortcode meta box with one-click selection

### 1.1.0
- **Fixed 5-Section Structure**: Standardized Decision Card format
- **Meta Banner**: Visual status display (Status | Owner | Target)
- **Smart Date Processing**: Automatic relative date handling with follow-up tasks
- **Preview Functionality**: Full preview capability for generated cards
- **Enhanced AI Prompts**: Improved output quality with 600 token limit
- **Direct HTML Output**: Perfect rendering without Markdown conversion
- **Public Post Type**: Enabled public visibility for Decision Cards

### 1.0.0
- Initial release
- Custom post type for Decision Cards
- AI-powered conversation summarization
- Admin interface for generation and settings
- Meta fields for status, owner, and due date
- Basic OpenAI API integration

## Support

For issues, feature requests, or contributions, please visit the [project repository](https://github.com/sunnypoon/SlackConversation2DecisionCardPlugin).

## License

This plugin is licensed under GPL v2 or later.
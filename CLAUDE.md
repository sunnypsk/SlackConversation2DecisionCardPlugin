# SlackConversation2DecisionCardPlugin

## Project Overview

This is a WordPress plugin that converts Slack-style conversations into summarized "Decision Cards" using AI. The plugin allows users to paste conversation transcripts and automatically generate structured decision records with summaries and action items.

## Key Features

- Custom post type for Decision Cards with structured 5-section output
- AI-powered conversation summarization using OpenAI-compatible APIs
- Fixed-format Decision Cards (Decision/Summary/Action Items/Sources/Risks/Assumptions)
- Meta banner displaying Status/Owner/Target at top of Decision Cards
- Intelligent relative date handling with automatic follow-up tasks
- Admin interface for inputting conversations with preview functionality
- Structured metadata (Status, Owner, Due Date)
- Error handling and logging

## Documentation

The complete Product Requirements Document (PRD) is available at `docs/prd.md`.

## Development Timeline

This is planned as a 1-week MVP build following the milestone breakdown in the PRD.

## Testing

When testing, use various conversation inputs to ensure:
- AI generates proper 5-section structure (Decision/Summary/Action Items/Sources/Risks)
- Meta banner displays correctly with Status/Owner/Target information
- Relative date handling creates appropriate follow-up tasks
- Custom fields save correctly
- Preview functionality works for generated Decision Cards
- Error handling works (invalid API keys, empty input, etc.)
- Performance is acceptable for typical API call durations

## Commands

When working on this project:
- Run linting: Use WordPress coding standards with PHPCS
- Run tests: Use WordPress-specific testing approach (PHPUnit for WordPress)
- Development: Edit PHP files directly in the `ai-decision-cards/` directory

## Development Approach

This project follows standard WordPress plugin development practices:
- Direct PHP development (no code generators)
- WordPress coding standards compliance
- Proper plugin file structure in `ai-decision-cards/` directory
- Version control tracks actual plugin source files
- Security-first development with proper sanitization and nonce verification
- Internationalization support using `ai-decision-cards` text domain

## Architecture Notes

- **Main Plugin File**: `ai-decision-cards/ai-decision-cards.php` contains the core plugin logic
- **Custom Post Type**: `decision_card` for storing generated decision records (public visibility enabled for preview)
- **Admin Pages**: Settings page for API configuration and generation interface
- **API Integration**: Supports OpenAI and OpenAI-compatible services (OpenRouter, Azure OpenAI)
- **Metadata Fields**: Status (Proposed/Approved/Rejected), Owner, Due Date
- **Content Structure**: Fixed 5-section HTML format enforced by AI prompt
- **Meta Banner**: Displays at top of Decision Cards via `the_content` filter
- **Relative Date Processing**: AI automatically detects and handles relative time phrases

## Latest Upgrades (v1.1)

### Fixed Structure Output
- **Decision**: One-sentence summary of what was decided
- **Summary**: Exactly 3 concise bullet points with key rationale
- **Action Items**: Task list with owners and due dates, automatic relative date handling
- **Sources**: 2-3 quoted lines from original conversation with timestamps
- **Risks/Assumptions**: Identified risks or assumptions, "None" if not applicable

### Meta Banner
- Displays Status | Owner | Target at top of each Decision Card
- Shows "TBD" for empty fields
- Accessible styling with proper contrast ratios
- Visible in preview mode and front-end display

### Technical Improvements
- **Direct HTML Generation**: AI prompt modified to output HTML directly instead of Markdown for perfect rendering
- Increased max_tokens to 600 for better AI responses
- Simplified content processing to trust AI HTML output
- Enhanced wp_kses whitelist for proper HTML tag support
- Public post type visibility for preview functionality

## Security Considerations

- All user inputs are properly sanitized using WordPress functions
- Nonce verification for all form submissions
- Capability checks (`edit_posts` for generation, `manage_options` for settings)
- API keys stored securely in WordPress options
- No direct database queries (uses WordPress APIs)
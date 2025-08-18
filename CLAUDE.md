# SlackConversation2DecisionCardPlugin

## Project Overview

This is a WordPress plugin that converts Slack-style conversations into summarized "Decision Cards" using AI. The plugin allows users to paste conversation transcripts and automatically generate structured decision records with summaries and action items.

## Key Features

- Custom post type for Decision Cards
- AI-powered conversation summarization using OpenAI-compatible APIs
- Admin interface for inputting conversations
- Structured metadata (Status, Owner, Due Date)
- Error handling and logging

## Documentation

The complete Product Requirements Document (PRD) is available at `docs/prd.md`.

## Development Timeline

This is planned as a 1-week MVP build following the milestone breakdown in the PRD.

## Testing

When testing, use various conversation inputs to ensure:
- AI summaries are properly formatted
- Custom fields save correctly
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
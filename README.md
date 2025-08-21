# Slack Conversation to Decision Card Plugin

A WordPress plugin that converts Slack-style conversations into AI-generated Decision Cards using OpenAI-compatible APIs.

## 🎯 Project Overview

This project implements a WordPress plugin that helps teams document important decisions by converting Slack conversation transcripts into structured Decision Cards with AI-powered summaries and action items.

## 📁 Project Structure

```
SlackConversaton2DecisionCardPlugin/
├── ai-decision-cards/          # Main WordPress plugin directory
│   ├── ai-decision-cards.php   # Primary plugin file
│   └── readme.md              # Plugin-specific documentation
├── docs/                      # Project documentation
│   └── prd.md                # Product Requirements Document
├── CLAUDE.md                 # Development guidelines and instructions
├── CLAUDE.local.md           # Local development preferences
└── docker-compose.yml        # Docker development environment
```

## 🚀 Quick Start

### For Users
1. Navigate to the `ai-decision-cards/` directory for the complete WordPress plugin
2. See `ai-decision-cards/readme.md` for installation and usage instructions

### For Developers
1. Review the complete PRD at `docs/prd.md`
2. Check development guidelines in `CLAUDE.md`
3. Follow WordPress plugin development best practices

## 🎯 Key Features

- **🤖 AI-Powered Structured Output**: Converts conversations into fixed 5-section Decision Cards
  - **Decision**: One-sentence summary of what was decided
  - **Summary**: Exactly 3 key bullet points with rationale
  - **Action Items**: Task assignments with intelligent date handling
  - **Sources**: Direct quotes from conversation with timestamps
  - **Risks/Assumptions**: Identified concerns or assumptions
- **📊 Meta Banner**: Visual status display (Status | Owner | Target) at top of each card
- **📅 Smart Date Handling**: Automatically processes relative dates ("next week") with follow-up tasks
- **🎨 Public Display Pages**: Dedicated pages for showcasing Decision Cards to website visitors
- **🔗 Shortcode Support**: Flexible embedding options for pages and posts
  - `[decision-cards-list]` - Display filterable grid with full-text search capability
  - `[decision-card id="123"]` - Embed specific Decision Card
- **📖 Comprehensive User Guidance**: Built-in documentation and copy-paste shortcodes
- **👀 Preview Functionality**: Full preview capability for generated Decision Cards
- **🎛️ Admin Interface**: Simple form-based conversation input with metadata fields
- **🔌 Multiple AI Providers**: Supports OpenAI and OpenAI-compatible APIs (OpenRouter)
- **📝 Draft Workflow**: Generated cards saved as drafts for review and editing

## 🎉 Latest Updates (v1.2)

### 🔗 Shortcode & Display Features
- **Comprehensive Shortcode System**: Full embedding support for Decision Cards
  - `[decision-cards-list]` with filtering, pagination, and enhanced full-text search
  - `[decision-card id="123"]` for single card embedding
  - Parameters: `limit`, `status`, `owner`, `search`, `show_meta`, `excerpt_only`
- **🔍 Enhanced Full-Text Search**: Smart search functionality that searches both titles and complete Decision Card content
  - Searches across Decision, Summary, Action Items, Sources, and Risks/Assumptions sections
  - Improved discoverability of decisions based on specific keywords in content
- **Public Display Pages**: Dedicated showcase pages for website visitors
- **User Guidance Integration**: Built-in documentation in Settings page
- **Edit Screen Shortcuts**: Copy-paste shortcodes directly from Decision Card editor
- **Responsive Design**: Mobile-friendly display for all shortcode outputs

### ✨ Previous Major Upgrades (v1.1)
- **Fixed 5-Section Structure**: All Decision Cards now follow consistent format
- **Visual Meta Banner**: Status/Owner/Target display at top of each card
- **Intelligent Date Processing**: Handles "next week", "week after" with automatic follow-ups
- **Preview Capability**: Full preview functionality for generated cards
- **Enhanced AI Prompts**: Improved quality with 600 token limit

### 🔧 Technical Improvements
- **Direct HTML Output**: AI now generates HTML directly instead of Markdown for perfect rendering
- **Advanced Shortcode Engine**: Flexible parameter system with error handling
- **Custom Search Implementation**: Uses WordPress `posts_where` filter for full-text search across titles and content
- **Public Display Infrastructure**: Complete page system with enhanced search and filtering
- **Enhanced User Experience**: Interactive elements and guided workflows
- Enhanced HTML tag support for better formatting
- Public post type visibility for preview functionality
- Improved accessibility with proper contrast ratios

## 📖 Documentation

- **Plugin Documentation**: See `ai-decision-cards/readme.md` for detailed plugin information
- **Product Requirements**: Complete specifications in `docs/prd.md`
- **Development Guidelines**: Project standards and practices in `CLAUDE.md`

## 🛠️ Development

This is a 1-week MVP build following WordPress plugin development standards:

- Direct PHP development (no code generators)
- WordPress coding standards compliance
- Proper error handling and security measures
- Internationalization support

## 📋 Development Commands

- **Linting**: Use WordPress coding standards with PHPCS
- **Testing**: WordPress-specific testing approach (PHPUnit for WordPress)
- **Development**: Edit PHP files directly in the `ai-decision-cards/` directory

## 🔧 Requirements

- WordPress 6.0+
- PHP 7.4+
- Valid OpenAI API key or compatible service
- Internet connection for AI API calls

## 📄 License

GPL v2 or later

---

**Note**: The main plugin files are located in the `ai-decision-cards/` directory. This README provides project-level overview, while plugin-specific documentation is available in `ai-decision-cards/readme.md`.
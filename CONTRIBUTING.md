# Contributing to AI Decision Cards

Thank you for your interest in contributing to AI Decision Cards! We welcome contributions from the community to help make this WordPress plugin better.

## 🚀 Getting Started

### Prerequisites
- WordPress 6.0+
- PHP 7.4+
- Basic knowledge of WordPress plugin development
- OpenAI API key for testing

### Development Setup
1. Clone the repository
2. Set up a local WordPress development environment
3. Copy the `ai-decision-cards/` folder to `/wp-content/plugins/`
4. Activate the plugin in WordPress admin
5. Configure your API settings for testing

## 📋 How to Contribute

### Reporting Bugs
Before creating bug reports, please check the issue list as you might find that the issue has already been reported. When creating a bug report, please include:

- **Clear description** of the issue
- **Steps to reproduce** the behavior
- **Expected vs actual behavior**
- **WordPress version** and **PHP version**
- **Plugin version**
- **Error messages** or logs if available

### Suggesting Features
Feature requests are welcome! Please:

- Use a clear and descriptive title
- Provide a detailed description of the suggested feature
- Explain why this feature would be useful
- Consider the scope and complexity of the feature

### Code Contributions

#### Before Starting
- Check existing issues and pull requests
- Discuss major changes by opening an issue first
- Follow WordPress coding standards

#### Development Process
1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Follow** coding standards (see below)
4. **Test** your changes thoroughly
5. **Commit** your changes (`git commit -m 'Add amazing feature'`)
6. **Push** to the branch (`git push origin feature/amazing-feature`)
7. **Open** a Pull Request

## 💻 Coding Standards

### PHP Standards
- Follow [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Use meaningful variable and function names
- Add proper PHPDoc comments
- Sanitize inputs and escape outputs
- Use WordPress APIs whenever possible

### Code Organization (v1.3.0 Architecture)
- **Admin classes**: `admin/class-*.php`
- **Public classes**: `public/class-*.php` 
- **Core classes**: `includes/class-*.php`
- **Views**: `admin/views/` and `public/views/`
- **Assets**: `assets/css/` and `assets/js/`

### Security Best Practices
- Always sanitize user input
- Escape output properly
- Use nonces for form submissions
- Check user capabilities
- Never hardcode API keys or secrets

### Testing Guidelines
- Test on multiple WordPress versions (6.0+)
- Test with different PHP versions (7.4+)
- Verify functionality with real OpenAI API calls
- Test error handling scenarios
- Check responsive design on mobile devices

## 📝 Commit Message Guidelines

Use clear and meaningful commit messages:

```
type(scope): brief description

Longer description if needed

- Additional details
- Breaking changes noted
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

**Examples:**
```
feat(generator): add support for custom AI prompts
fix(shortcode): resolve pagination issue in decision cards list
docs(readme): update installation instructions
```

## 🔍 Pull Request Process

### Before Submitting
- [ ] Code follows WordPress coding standards
- [ ] All new functionality is properly documented
- [ ] Changes have been tested locally
- [ ] No hardcoded secrets or API keys
- [ ] Commit messages are clear and descriptive

### Pull Request Template
- Describe what changes you've made
- Reference any related issues
- Include testing steps
- Note any breaking changes
- Add screenshots if UI changes are involved

### Review Process
1. **Automated checks** (if configured)
2. **Code review** by maintainers
3. **Testing** in local environment
4. **Approval** and merge

## 📚 Resources

### WordPress Development
- [WordPress Plugin Developer Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [WordPress API Reference](https://developer.wordpress.org/reference/)

### Project Documentation
- [Product Requirements Document](docs/prd.md)
- [Plugin README](ai-decision-cards/readme.md)
- [Development Guidelines](CLAUDE.md)

### AI Integration
- [OpenAI API Documentation](https://platform.openai.com/docs)
- [OpenRouter API](https://openrouter.ai/docs)

## 🤝 Code of Conduct

### Our Commitment
We are committed to providing a welcoming and inspiring community for all. Please be respectful in all interactions.

### Guidelines
- **Be respectful** of different viewpoints and experiences
- **Accept constructive criticism** gracefully
- **Focus on what's best** for the community and project
- **Show empathy** towards other community members

### Unacceptable Behavior
- Harassment, trolling, or discriminatory language
- Personal attacks or insults
- Public or private harassment
- Publishing others' private information without permission

## ❓ Questions?

- **General questions**: Open a [Discussion](https://github.com/sunnypsk/SlackConversation2DecisionCardPlugin/discussions)
- **Bug reports**: Open an [Issue](https://github.com/sunnypsk/SlackConversation2DecisionCardPlugin/issues)
- **Feature requests**: Open an [Issue](https://github.com/sunnypsk/SlackConversation2DecisionCardPlugin/issues)

Thank you for contributing to AI Decision Cards! 🎉

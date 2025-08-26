# Security Policy

## 🔒 Supported Versions

We actively maintain and provide security updates for the following versions:

| Version | Supported          | Status |
| ------- | ------------------ | ------ |
| 1.3.x   | ✅ Yes             | Current stable release |
| 1.2.x   | ⚠️ Limited support | Critical security fixes only |
| 1.1.x   | ❌ No              | End of life |
| < 1.1   | ❌ No              | End of life |

## 🚨 Reporting a Vulnerability

We take security seriously. If you discover a security vulnerability in AI Decision Cards, please follow these steps:

### How to Report
1. **DO NOT** create a public GitHub issue for security vulnerabilities
2. **Email us directly** at: [sunnypsk+security@gmail.com](mailto:sunnypsk+security@gmail.com)
3. **Include** as much detail as possible about the vulnerability

### What to Include in Your Report
- **Description** of the vulnerability
- **Steps to reproduce** the issue
- **Potential impact** and attack scenarios
- **Suggested fix** (if you have one)
- **WordPress version** and plugin version affected
- **Your contact information** for follow-up

### Response Timeline
- **Initial response**: Within 24-48 hours
- **Assessment**: Within 7 days
- **Fix timeline**: Depends on severity (see below)

## 🔧 Security Measures in Place

### Data Protection
- **API Keys**: Stored securely in WordPress options table
- **User Input**: All inputs are sanitized using WordPress functions
- **Output Escaping**: All outputs are properly escaped
- **Nonce Verification**: All form submissions use nonces
- **Capability Checks**: Proper user permission verification

### WordPress Security Standards
- **No Direct File Access**: All files check for `ABSPATH`
- **SQL Injection Prevention**: Uses WordPress APIs, no direct SQL
- **XSS Prevention**: Proper input/output sanitization
- **CSRF Protection**: Nonce verification on all actions
- **File Upload Security**: No file upload functionality to minimize risk

### Third-Party Integrations
- **OpenAI API**: Secure HTTPS communication only
- **API Key Handling**: Never exposed to frontend
- **Rate Limiting**: Prevents abuse of AI API calls
- **Error Handling**: Sensitive information not exposed in errors

## 🚀 Security Best Practices for Users

### Installation & Configuration
- **Keep Updated**: Always use the latest version
- **Strong API Keys**: Use secure OpenAI API keys
- **Access Control**: Limit plugin access to trusted users
- **Regular Backups**: Maintain current backups of your WordPress site

### Operational Security
- **Monitor Usage**: Keep track of API usage and costs
- **Review Generated Content**: Always review AI-generated content before publishing
- **Sensitive Data**: Avoid inputting sensitive information in conversations
- **User Permissions**: Only grant `edit_posts` capability to trusted users

### WordPress Environment
- **WordPress Updates**: Keep WordPress core updated
- **Plugin Updates**: Keep all plugins updated
- **Strong Passwords**: Use strong passwords for all accounts
- **Security Plugins**: Consider using WordPress security plugins

## 🏷️ Vulnerability Severity Levels

### Critical (Fix within 24-48 hours)
- Remote code execution
- SQL injection
- Authentication bypass
- Privilege escalation

### High (Fix within 7 days)
- Cross-site scripting (XSS)
- Cross-site request forgery (CSRF)
- Data disclosure
- Security feature bypass

### Medium (Fix within 30 days)
- Information disclosure
- Denial of service
- Configuration issues

### Low (Fix in next scheduled release)
- Minor information leaks
- Security improvements
- Documentation issues

## 🛡️ Disclosure Policy

### Responsible Disclosure
- We follow responsible disclosure practices
- Security researchers are credited (with permission)
- Patches are released before public disclosure
- Users are notified of security updates

### Public Disclosure Timeline
1. **Patch Development**: Fix is developed and tested
2. **Security Release**: Patch is released to users
3. **Public Disclosure**: Details shared after users have time to update
4. **Credit Given**: Security researchers are credited appropriately

## 🔍 Security Audit History

### Version 1.3.0 (Current)
- ✅ Complete security review during modularization
- ✅ Enhanced input sanitization
- ✅ Improved nonce verification
- ✅ Strengthened capability checks

### Version 1.2.x
- ✅ Security review for shortcode functionality
- ✅ XSS prevention in public displays
- ✅ CSRF protection for admin functions

## 📞 Contact Information

### Security Team
- **Email**: [sunnypsk+security@gmail.com](mailto:sunnypsk+security@gmail.com)
- **Response Time**: 24-48 hours
- **Languages**: English

### General Support
- **GitHub Issues**: For non-security related issues only
- **Documentation**: See [CONTRIBUTING.md](CONTRIBUTING.md)
- **Community**: GitHub Discussions

## 🏅 Hall of Fame

We acknowledge security researchers who help improve our security:

*No security researchers have been credited yet. Be the first to help us improve!*

---

**Note**: This security policy applies to the AI Decision Cards WordPress plugin. For WordPress core security issues, please refer to the [WordPress Security Team](https://wordpress.org/about/security/).

Thank you for helping keep AI Decision Cards secure! 🙏
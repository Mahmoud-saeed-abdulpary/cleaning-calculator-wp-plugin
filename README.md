# Cleaning Price Calculator - Professional WordPress Plugin

A production-ready, enterprise-level WordPress plugin for cleaning service companies to calculate prices dynamically and manage quote requests.

## Features

### Frontend Calculator
- ✅ **Accordion-based room repeater** for mobile-responsive UX
- ✅ **Real-time price calculation** with live updates
- ✅ **Multi-room support** with dynamic add/remove functionality
- ✅ **Configurable room types** with individual pricing
- ✅ **Integrated quote request system** with multiple display modes
- ✅ **Customizable colors** matching your brand
- ✅ **Multi-language support** (German, English, Arabic)
- ✅ **Mobile-first responsive design**

### Admin Dashboard
- ✅ **Statistics dashboard** with key metrics
- ✅ **Room types management** with full CRUD operations
- ✅ **Quote management system** with detailed views
- ✅ **Email configuration** with SMTP support
- ✅ **Company information settings**
- ✅ **Design customization** with color pickers
- ✅ **Multiple currency support**
- ✅ **PDF export capability** for quotes

### Technical Excellence
- ✅ **Clean architecture** with separated concerns
- ✅ **Object-oriented design** following WordPress coding standards
- ✅ **Secure AJAX** with nonce verification
- ✅ **Database optimization** with custom tables
- ✅ **Sanitized inputs** and validated data
- ✅ **i18n ready** with translation support
- ✅ **Shortcode-based** integration
- ✅ **Compatible** with Elementor, Gutenberg, Classic Editor

## Installation

1. Upload the `cleaning-price-calculator` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'Cleaning Calculator' in the admin menu
4. Configure your settings and add room types
5. Use the shortcode `[cleaning_price_calculator]` on any page

## Usage

### Shortcode

```
[cleaning_price_calculator]
```

**Optional Attributes:**
```
[cleaning_price_calculator title="Get Your Cleaning Quote"]
```

### Admin Configuration

#### 1. Room Types
- Navigate to **Cleaning Calculator → Room Types**
- Add room types with:
  - Name (e.g., "Single Room", "Kitchen")
  - Price per square meter
  - Description
  - Sort order
  - Status (active/inactive)

#### 2. Settings

**Company Information:**
- Company name
- Contact phone
- Admin email
- Currency selection

**Email Configuration:**
- SMTP settings for reliable delivery
- From name and address
- Host, port, username, password
- Encryption (TLS/SSL)

**Language:**
- Select default language (German, English, Arabic)

**Design:**
- Customize primary color
- Button color
- Accent color

**Form Display:**
- Popup modal
- Inline below totals
- Replace calculator view

### Quote Management

- View all submitted quotes in **Cleaning Calculator → Quotes**
- Click on any quote to see full details
- Export quotes as PDF
- Email notifications sent automatically to both admin and customer

## Technical Architecture

### File Structure

```
cleaning-price-calculator/
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       ├── admin.js
│       └── frontend.js
├── includes/
│   ├── class-cpc-core.php
│   ├── class-cpc-database.php
│   ├── class-cpc-activator.php
│   ├── class-cpc-deactivator.php
│   ├── class-cpc-loader.php
│   └── class-cpc-i18n.php
├── admin/
│   ├── class-cpc-admin.php
│   ├── class-cpc-admin-menu.php
│   ├── class-cpc-room-types.php
│   ├── class-cpc-quotes.php
│   ├── class-cpc-settings.php
│   └── views/
│       ├── dashboard.php
│       ├── room-types.php
│       ├── quotes-list.php
│       ├── quote-detail.php
│       └── settings.php
├── public/
│   ├── class-cpc-public.php
│   ├── class-cpc-calculator.php
│   ├── class-cpc-ajax.php
│   └── views/
│       ├── calculator.php
│       └── quote-form.php
├── languages/
└── cleaning-price-calculator.php
```

### Database Tables

**cpc_room_types:**
- Stores room type configurations
- Includes pricing and status

**cpc_quotes:**
- Stores customer quote requests
- Includes customer information and metadata

**cpc_quote_items:**
- Stores individual rooms for each quote
- Links to room types with pricing snapshots

## Security

- ✅ Nonce verification on all forms
- ✅ Capability checks for admin functions
- ✅ Input sanitization and output escaping
- ✅ Prepared SQL statements
- ✅ CSRF protection
- ✅ XSS prevention

## Performance

- ✅ Assets loaded only when shortcode is present
- ✅ Database queries optimized with indexes
- ✅ AJAX for dynamic operations
- ✅ Minimal DOM manipulation
- ✅ Efficient event delegation

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Support

For technical support, feature requests, or bug reports, please contact your development team or submit an issue.

## License

GPL-2.0+

## Version History

### 1.0.0
- Initial release
- Full calculator functionality
- Admin dashboard
- Quote management
- Multi-language support
- Email notifications
- SMTP configuration
- Design customization

## Credits

Developed following WordPress coding standards and best practices for production-ready commercial use.
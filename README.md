# 🎬 Cineworm - Video Streaming Platform

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)

> A comprehensive video streaming platform built with Laravel, offering movies, TV shows, live TV, and sports content with subscription management and multi-device support.

## 🌟 Features

### 🎥 Content Management
- **Movies**: Complete movie library with detailed information, trailers, and ratings
- **TV Shows & Series**: Multi-season TV shows with episode management
- **Live TV**: Real-time television streaming with channel categorization
- **Sports**: Sports content with category-based organization
- **Upcoming Content**: Preview upcoming movies and shows

### 👥 User Experience
- **User Registration & Authentication**: Secure user accounts with email verification
- **Social Login**: Google and Facebook authentication integration
- **Watchlist**: Personal watchlist for favorite content
- **Recently Watched**: Track viewing history across devices
- **Multi-device Support**: Device limit management per subscription
- **Responsive Design**: Optimized for desktop, tablet, and mobile

### 💳 Subscription & Payments
- **Flexible Subscription Plans**: Multiple subscription tiers
- **Multiple Payment Gateways**:
  - Stripe
  - PayPal
  - Razorpay
  - Paystack
  - Paytm
  - PayU
  - Cashfree
  - Coingate
  - Flutterwave
  - Mollie
  - Instamojo
  - MercadoPago

### 🔧 Admin Panel
- **Content Management**: Add, edit, and manage all content types
- **User Management**: Complete user administration
- **Subscription Management**: Plan creation and transaction monitoring
- **Analytics Dashboard**: Comprehensive platform statistics
- **Settings Configuration**: Platform-wide settings and customization

### 🌐 Multi-language Support
- **Internationalization**: Support for multiple languages
- **Content Localization**: Language-specific content organization
- Available languages: English, Spanish, French, Portuguese

### 📱 API Support
- **Mobile App API**: Complete REST API for mobile applications
- **Android App Integration**: Ready for Android app development
- **Real-time Data**: Live content updates and synchronization

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 10.x
- **PHP**: 8.1+
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum
- **File Management**: Laravel File Manager
- **Image Processing**: Intervention Image
- **Email**: SMTP, Mailgun support
- **Backup**: Spatie Laravel Backup

### Frontend
- **Build Tool**: Vite
- **CSS Framework**: Custom responsive design
- **JavaScript**: Vanilla JS with modern features
- **Video Player**: Custom video player implementation

### Third-party Services
- **Cloud Storage**: AWS S3 compatible
- **CDN**: Content delivery network support
- **Social Auth**: Google, Facebook OAuth
- **Analytics**: Google Analytics integration

## 📋 Requirements

- **PHP**: 8.1 or higher
- **Composer**: Latest version
- **Node.js**: 16.x or higher
- **MySQL**: 8.0 or higher
- **Apache/Nginx**: Web server
- **SSL Certificate**: For production deployment

### PHP Extensions Required
```
- BCMath PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- GD PHP Extension
- cURL PHP Extension
- Zip PHP Extension
```

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yasirraheel/cineworm-org-web.git
cd cineworm-org-web
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration
Update your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cineworm_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed
```

### 6. Storage & Permissions
```bash
# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 7. Build Assets
```bash
# Development build
npm run dev

# Production build
npm run build
```

### 8. Start Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## ⚙️ Configuration

### Admin Account
Create your first admin account:
```bash
php artisan tinker
```
```php
$user = new App\User();
$user->name = 'Admin';
$user->email = 'admin@cineworm.com';
$user->password = Hash::make('password');
$user->usertype = 'Admin';
$user->save();
```

### Payment Gateway Setup
Configure your preferred payment gateways in the `.env` file:

```env
# Stripe
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key

# PayPal
PAYPAL_MODE=sandbox # or live
PAYPAL_SANDBOX_CLIENT_ID=your_paypal_client_id
PAYPAL_SANDBOX_CLIENT_SECRET=your_paypal_client_secret

# Add other payment gateway credentials as needed
```

### Email Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

## 📁 Project Structure

```
cineworm-org-web/
├── app/
│   ├── Http/Controllers/          # Application controllers
│   ├── Models/                    # Eloquent models
│   ├── Mail/                      # Email classes
│   └── ...
├── resources/
│   ├── views/                     # Blade templates
│   ├── css/                       # Stylesheets
│   └── js/                        # JavaScript files
├── public/
│   ├── images/                    # Public images
│   ├── upload/                    # User uploads
│   └── ...
├── database/
│   ├── migrations/                # Database migrations
│   └── seeders/                   # Database seeders
├── lang/                          # Language files
└── routes/
    ├── web.php                    # Web routes
    └── api.php                    # API routes
```

## 🔒 Security Features

- **CSRF Protection**: Built-in CSRF token validation
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- **XSS Protection**: Output escaping and validation
- **Authentication**: Secure password hashing and session management
- **Device Tracking**: Multi-device login monitoring
- **SSL Support**: HTTPS enforcement for production

## 📱 Mobile API

The platform includes a comprehensive REST API for mobile applications:

### Authentication Endpoints
- `POST /api/login` - User login
- `POST /api/register` - User registration
- `POST /api/logout` - User logout

### Content Endpoints
- `GET /api/movies` - Get movies list
- `GET /api/shows` - Get TV shows
- `GET /api/sports` - Get sports content
- `GET /api/livetv` - Get live TV channels

### User Endpoints
- `GET /api/watchlist` - User watchlist
- `POST /api/add_watchlist` - Add to watchlist
- `GET /api/recently_watched` - Recently watched content

## 🎨 Customization

### Themes
The platform supports multiple themes located in `resources/views/`:
- Default responsive theme
- Custom CSS variables for easy color customization
- Mobile-first responsive design

### Language Support
Add new languages by:
1. Creating language files in `lang/` directory
2. Translating all keys in `words.php`
3. Updating language selector in admin panel

## 📊 Analytics & Monitoring

- **User Analytics**: Track user engagement and viewing patterns
- **Content Performance**: Monitor popular content and trends
- **Revenue Tracking**: Subscription and payment analytics
- **Device Analytics**: Multi-device usage statistics

## 🚦 Performance Optimization

- **Database Indexing**: Optimized database queries
- **Caching**: Redis/Memcached support
- **CDN Integration**: Content delivery optimization
- **Image Optimization**: Automatic image compression
- **Lazy Loading**: Improved page load times

## 🧪 Testing

```bash
# Run PHP tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
```

## 📦 Deployment

### Production Deployment
1. Set up production server (Apache/Nginx)
2. Configure SSL certificate
3. Set environment to production:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```
4. Optimize application:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

### Docker Deployment
Docker configuration files are available for containerized deployment.

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write descriptive commit messages
- Add tests for new features
- Update documentation as needed

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- **Email**: care@cineworm.online
- **Documentation**: Check the `docs/` directory
- **Issues**: Create an issue on GitHub

## 🔄 Changelog

### Version 1.0.0
- Initial release with core streaming functionality
- Multi-device support
- Payment gateway integration
- Admin panel
- Mobile API

### Upcoming Features
- **Live Chat**: Real-time user communication
- **Content Recommendations**: AI-powered content suggestions
- **Offline Viewing**: Download for offline playback
- **Social Features**: User reviews and ratings
- **Enhanced Analytics**: Advanced reporting dashboard

## 🙏 Acknowledgments

- Laravel Framework team
- All contributing developers
- Open source community
- Payment gateway providers

---

**Made with ❤️ by the Cineworm Team**

For more information, visit [cineworm.online](https://cineworm.online)

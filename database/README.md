# EcoConnect Database Setup

This folder contains the database configuration and setup files for the EcoConnect waste management platform.

## 📁 File Structure

```
database/
├── config.php      # Database connection configuration
├── schema.sql      # Complete SQL schema with all tables
└── setup.php       # Automated database setup script
```

## 🗄️ Tables Created

### Core Tables
- **users** - User accounts and profiles
- **waste_reports** - Waste pickup requests and reports
- **green_points_transactions** - Points earning/redemption history

### Service Tables
- **services** - Available waste management services
- **recycling_centers** - Recycling facility locations and details
- **dustbin_bookings** - Office/School dustbin requests

### Engagement Tables
- **quiz_questions** - Recycling quiz content
- **quiz_attempts** - User quiz participation
- **volunteers** - Volunteer applications
- **sustainability_events** - Clean-up drives and events
- **event_registrations** - Event signups

### Reward Tables
- **rewards** - Available rewards catalog
- **user_rewards** - Redeemed rewards tracking

### Analytics Tables
- **impact_statistics** - Platform-wide environmental impact
- **announcements** - System notifications
- **contact_messages** - User inquiries

## 🚀 Setup Instructions

### 1. Configure Database Credentials

Edit `config.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'ecoconnect');
```

### 2. Run Setup Script

Open in browser:
```
http://localhost/ecoconnect/database/setup.php
```

Or run via command line:
```bash
php database/setup.php
```

### 3. Manual Setup (Alternative)

Import `schema.sql` directly in phpMyAdmin or MySQL:

```bash
mysql -u root -p < database/schema.sql
```

## 🔌 API Integration

The `/api/` folder contains REST API endpoints for frontend integration:

| Endpoint | Description |
|----------|-------------|
| `api/user.php` | User registration, login, profile |
| `api/reports.php` | Waste report submission |
| `api/services.php` | Services and recycling centers |
| `api/quiz.php` | Quiz questions and attempts |
| `api/events.php` | Sustainability events |
| `api/dashboard.php` | Dashboard statistics |

### Example Usage

```javascript
// Register user
fetch('/ecoconnect/api/user.php?action=register', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        full_name: 'John Doe',
        email: 'john@example.com',
        password: 'password123',
        city: 'Noida'
    })
})

// Get dashboard data
fetch('/ecoconnect/api/dashboard.php?action=stats&user_id=1')
```

## 🔐 Security Notes

- All passwords are hashed using bcrypt
- SQL injection prevention via prepared statements
- Input validation on all API endpoints
- File upload restrictions for report images

## 📊 Default Data

The schema includes default data for:
- 8 services (Waste Collection, Recycling, etc.)
- 6 rewards (Gift cards, merchandise, etc.)
- 5 quiz questions
- 4 recycling centers (NCR region)
- 4 upcoming events
- 3 announcements
- 8 impact statistics

## 🔧 Troubleshooting

### Connection Failed
- Check if MySQL is running
- Verify credentials in `config.php`
- Ensure database user has proper privileges

### Table Already Exists
- Drop existing database: `DROP DATABASE ecoconnect;`
- Re-run setup script

### Permission Errors
- Ensure PHP has write permissions for uploads folder
- Set proper permissions: `chmod 755 -R uploads/`

# Pet Adoption System

A web-based pet adoption management system built with PHP, MySQL, and TailwindCSS. This system allows administrators to manage pets available for adoption and enables users to browse and submit adoption applications.

## Features

- **Public Features**

  - Browse available pets with filtering options (species, age, gender)
  - View detailed pet information
  - Submit adoption applications
  - Responsive design for all devices

- **Admin Features**
  - Secure admin login
  - Dashboard with overview of pets and applications
  - Add, edit, and delete pets
  - Manage adoption applications
  - Upload pet images

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- mod_rewrite enabled (for Apache)

## Installation

1. Clone the repository to your web server directory:

   ```bash
   git clone [repository-url] pet-adoption-system
   ```

2. Create a MySQL database:

   ```sql
   CREATE DATABASE pet_adoption;
   ```

3. Import the database schema:

   ```bash
   mysql -u your_username -p pet_adoption < config/pet_adoption.sql
   ```

4. Configure the database connection:

   - Open `config/database.php`
   - Update the database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'pet_adoption');
     ```

5. Set up the admin account:

   ```sql
   INSERT INTO users (username, password, email, is_admin)
   VALUES ('admin', '$2y$10$YOUR_HASHED_PASSWORD', 'admin@example.com', 1);
   ```

   Note: Make sure to use a properly hashed password using PHP's password_hash() function.

6. Configure your web server:

   - Set the document root to the project directory
   - Ensure the web server has write permissions for the `assets/images/pets` directory

7. Access the system:
   - Public site: `http://your-domain/`
   - Admin panel: `http://your-domain/admin/`

## Directory Structure

```
pet-adoption-system/
├── admin/                 # Admin panel files
├── assets/               # Static assets
│   ├── css/
│   ├── js/
│   └── images/
│       └── pets/        # Pet images storage
├── config/               # Configuration files
├── includes/             # Shared PHP files
└── README.md            # This file
```

## Security Considerations

- All user inputs are sanitized and validated
- Passwords are hashed using PHP's password_hash()
- Prepared statements are used for all database queries
- File uploads are validated for type and size
- Admin area is protected with session-based authentication

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

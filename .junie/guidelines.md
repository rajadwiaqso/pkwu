# Project Guidelines for MarketRaja

## Project Overview
MarketRaja is a gaming marketplace platform built with Laravel and Vue.js. It allows users to buy and sell game accounts, exclusive items, and digital currency. The platform is primarily targeted at an Indonesian audience.

### Key Features
- User authentication and authorization
- Product search and filtering
- Shopping cart functionality
- Seller dashboard for managing products
- Admin dashboard with multi-role support
- Real-time chat system
- Gaming widget for authenticated users
- Notification system
- Dark/light theme support

## Technology Stack
- **Backend**: Laravel (PHP framework)
- **Frontend**: Vue.js with Inertia.js
- **Styling**: Tailwind CSS
- **Database**: MySQL (assumed based on Laravel default)
- **Package Manager**: npm for frontend, Composer for backend

## Project Structure
- `/app` - Contains the core code of the application (Laravel backend)
- `/resources` - Contains frontend assets, Vue components, and views
- `/routes` - Contains route definitions
- `/database` - Contains database migrations and seeders
- `/config` - Contains configuration files
- `/public` - Contains publicly accessible files
- `/storage` - Contains application storage files
- `/tests` - Contains test files

## Development Guidelines

### Running the Project
1. Clone the repository
2. Install PHP dependencies: `composer install`
3. Install JavaScript dependencies: `npm install`
4. Copy `.env.example` to `.env` and configure your environment
5. Generate application key: `php artisan key:generate`
6. Run database migrations: `php artisan migrate`
7. Compile assets: `npm run dev`
8. Start the development server: `php artisan serve`

### Testing
When making changes to the codebase, Junie should run the appropriate tests to ensure that the changes don't break existing functionality. The project includes several test scripts:
- `test_chat_system.sh` - Tests the chat system functionality
- `test_seller_dashboard_complete.sh` - Tests the seller dashboard functionality
- `test_seller_dashboard_final.sh` - Tests the final implementation of the seller dashboard

To run tests, use the following command:
```bash
php artisan test
```

### Code Style
- Follow PSR-12 coding standards for PHP code
- Use camelCase for JavaScript variables and functions
- Use PascalCase for Vue components
- Use kebab-case for HTML attributes and CSS classes
- Document complex functions and methods with appropriate comments
- Keep components small and focused on a single responsibility

### Git Workflow
- Create feature branches for new features or bug fixes
- Write descriptive commit messages
- Keep commits small and focused
- Create pull requests for code review before merging to main branch

## Additional Resources
The project includes several documentation files that provide detailed information about specific features:
- `GAMING_MARKETPLACE_IMPLEMENTATION.md`
- `CART_IMPLEMENTATION_DOCS.md`
- `NOTIFICATION_SYSTEM_DOCS.md`
- `SELLER_DASHBOARD_COMPLETE_IMPLEMENTATION.md`
- And many more

Refer to these documents for detailed information about specific features.

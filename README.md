# Laravel Friend System Project

This is a Laravel-based web application that includes a user authentication system, profile management, and a full-featured friend system.

## Features

### Authentication
- User registration
- Login and logout functionality

### Profile Management
- View and update profile information (name, email, etc.)
- Upload and update profile picture (stored locally)

### Friend System
- Search users by name or email
- Send friend requests to other users
- View incoming friend requests
- Accept or reject friend requests
- View list of accepted friends
- Remove a friend from the friends list
- Send email notifications when a friend request is received (queued)

## Technologies Used
- Laravel (with Laravel Breeze for auth scaffolding)
- Tailwind CSS (via Breeze)
- Laravel Notifications
- Laravel Queues (database driver)
- Mailtrap (for email testing)

## Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-username/your-repo-name.git
   cd your-repo-name

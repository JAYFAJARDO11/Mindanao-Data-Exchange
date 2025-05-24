# User Session Tracking System

This system allows tracking of online users on the Mindanao Data Exchange platform and displays them in the admin dashboard.

## How It Works

1. The system uses the `user_sessions` table in the database to store information about active user sessions.
2. When a user logs in, their session is recorded in the database.
3. As users navigate through the site, their session's "last_activity" timestamp is updated.
4. The admin dashboard displays users with activity in the last 15 minutes as "online".
5. Administrators can force-logout users if needed.
6. Old sessions are automatically cleaned up.

## Important Note

This system is designed to track **regular users only**, not administrators. Administrator activity is tracked separately in the `administrator` table with the `last_login` field.

## Files Included

- `session_management.php` - Core functions for managing user sessions
- `session_tracker.php` - Include file that updates session activity on each page
- `includes/header.php` - Standard header that can be included on all pages

## Implementation on New Pages

### Option 1: Include the session tracker directly

At the top of your PHP file, after session_start():

```php
<?php
session_start();
include 'db_connection.php';

// Track user activity for online users list
include 'session_tracker.php';

// Rest of your code...
?>
```

### Option 2: Use the standard header (preferred)

Create your PHP file starting with:

```php
<?php
include 'includes/header.php';

// Rest of your code...
?>
```

## Database Structure

The `user_sessions` table has the following structure:

```sql
CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `last_activity` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `session_id` (`session_id`),
  KEY `last_activity` (`last_activity`),
  CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
```

Note the foreign key constraint that connects the `user_id` in this table to the `users` table. This is why administrator sessions are not stored in this table.

## Admin Dashboard Integration

The admin dashboard shows all online users with:
- User name
- Email
- Organization
- Last activity time
- Option to force-logout

## Session Cleanup

Old sessions are automatically cleaned up on a random basis (1% chance on each page load) to prevent database clutter. The default cleanup time is 30 minutes of inactivity.

---

For any issues or questions, please contact the system administrator. 
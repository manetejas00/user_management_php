# user_management_php
 
User Management System
Overview
The User Management System is a simple application built using core PHP for managing user accounts. It supports functionalities such as user registration, login, and management (add/edit/delete) of user accounts. This system implements soft deletion for user records and provides basic authentication mechanisms to ensure secure access.

Features
User Registration: Allows new users to register with their name, email, password, and role.
User Login: Authenticates users using their email and password.
User Management: Admin users can add, edit, and delete (soft delete) other user accounts.
Role-Based Access: Supports different user roles, allowing for an admin role with higher privileges.
Soft Deletion: Users can be soft deleted, allowing for data retention and recovery.
JSON API: Provides a simple API to manage users with authentication.

Create a database
-- Create database
CREATE DATABASE user_management;
-- Use the newly created database
USE user_management;
-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL DEFAULT NULL
);
-- Add any other necessary tables here (if applicable)

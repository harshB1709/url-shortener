# URL Shortener Application Technical Document

## Overview

This document outlines the technical aspects of the URL Shortener application, designed to create shorter aliases for long URLs, facilitating easier sharing and tracking.

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
  - [High-Level Architecture](#high-level-architecture)
  - [Database Schema](#database-schema)
- [Technology Stack](#technology-stack)
- [Features](#features)
- [API Endpoints](#api-endpoints)
  - [Creating a Short URL](#creating-a-short-url)
  - [Redirecting a Short URL](#redirecting-a-short-url)
  - [Analytics](#analytics)
- [Security Considerations](#security-considerations)
- [Deployment](#deployment)
- [Future Enhancements](#future-enhancements)

## Architecture

### High-Level Architecture/ Tech Stack

- **Frontend**: Blade scaffolding provided by Laravel Breeze with AlpineJS, Tailwind CSS and FontAwesome for icons.
- **Backend**: PHP 8.2.10, Laravel 11.2.0.
- **Database**: SQlite for local DB (Any DB compatible with Laravel v11 can be used).

### Database Schema

- **Users Table** (if applicable): Consists of the user details like name, email, hashed password, etc.
- **URLs Table**: Consists of user_id foreign key to determine the owner of the url record, the original url, the shortened code for the url, is_active to determine if the url is currently active, deleted_at for soft delete timestamps.

## Features

- **Short URL Generation**: Users can register/login to authenticate themselves, click on 'Generate New Url' button, enter the original URL and retrieve the new shortened URL.

## API Endpoints

### Creating a Short URL

- **Path**: `/urls/store`
- **Method**: `POST`
- **Request Parameters**: `original_url`
- **Response**: Returns the newly created Short URL

### Updating a Short URL

- **Path**: `/urls/{url_id}/update`
- **Method**: `POST`
- **Request Parameters**: `original_url`, `is_active`
- **Response**: Returns if the request successfully went through

### Deactivating a Short URL

- **Path**: `/urls/{url_id}/deactivate`
- **Method**: `POST`
- **Request Parameters**: -
- **Response**: Deactivates a given url and responds if the request went through

### Deleting a Short URL

- **Path**: `/urls/{url_id}`
- **Method**: `DELETE`
- **Request Parameters**: -
- **Response**: deletes a given url and responds if the request went through

### Redirecting a Short URL

- **Path**: `/url/{short_code}`
- **Method**: `GET`
- **Response**: Redirect to the original URL or error page.

## Security Considerations

Authentication is being handled by dlaravel's built in authentication functionality. For authorization of user manipulating the url records, the Policy feature of Laravel has been used. The original URL is being validated when entered by the user using the validation methods provided by Laravel.

## Algorithm

The app uses `Base62 Encoding` to create the short codes for the input urls. The short codes are essentially the Base62 encoding of the primary keys of a particular url record. The characters considered in the base62 encoding are `0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ` in that order. The advantages of this approach is that its easy to retrieve a row from the table since we can decode it to the original id and get the record. Also, no two url records with same original URL would have the same short_code

## Future Enhancements

- Create plans for limiting the number of urls a user can shorten

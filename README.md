# Skvosh - Sports Ranking Application

A modern sports ranking tracking application built with Laravel and Vue.js, with a primary focus on squash rankings while supporting multiple racquet sports.

## Overview

Skvosh is a comprehensive sports ranking application designed to track player rankings across various racquet sports including:

- **Squash** (primary focus)
- Tennis
- Badminton
- Table Tennis
- Pickleball
- Paddle Tennis

## Tech Stack

### Backend
- **Laravel 12** - PHP framework
- **SQLite** - Database (for development)
- **Inertia.js** - Server-side rendering adapter

### Frontend
- **Vue.js 3** - Frontend framework
- **TypeScript** - Type safety
- **Inertia.js Vue adapter** - Seamless Laravel-Vue integration

### UI & Styling
- **shadcn-vue** - Component library
- **Tailwind CSS 4** - Utility-first CSS framework
- **Inter font** - Typography

### Development Tools
- **Vite** - Build tool and dev server
- **ESLint** - Code linting
- **Pest** - PHP testing framework
- **PHPUnit** - Additional testing support

## Code Standards

- **Indentation**: 4 spaces (consistent across all files)
- **Language**: TypeScript for frontend, PHP for backend
- **Component Library**: shadcn-vue components preferred
- **Styling**: Tailwind CSS 4 utility classes

## Project Structure

```
skvosh/
├── app/                    # Laravel application code
│   ├── Http/Controllers/   # API and web controllers
│   ├── Models/            # Eloquent models
│   └── ...
├── resources/
│   ├── js/                # Vue.js frontend
│   │   ├── components/    # Reusable Vue components
│   │   ├── pages/         # Inertia.js pages
│   │   ├── layouts/       # Page layouts
│   │   └── types/         # TypeScript definitions
│   └── css/               # Stylesheets
├── routes/                # Laravel routing
├── database/              # Migrations, seeders, factories
└── tests/                 # Pest/PHPUnit tests
```

## Key Features

- Multi-sport ranking system with emphasis on squash
- Modern, responsive UI built with shadcn-vue components
- Type-safe frontend with TypeScript
- Server-side rendering with Inertia.js
- Comprehensive testing setup

## Development Notes

- Uses Laravel's Inertia.js starter kit for Vue.js integration
- Dark mode support built into the UI
- SQLite database for development environment
- Vite for fast development builds and HMR
- All components follow shadcn-vue design patterns
- Consistent 4-space indentation across the codebase

## Sports Focus

While the application supports multiple racquet sports, **squash** is the primary focus and receives priority in feature development and UI optimization. Other supported sports include tennis, badminton, table tennis, pickleball, and paddle tennis.

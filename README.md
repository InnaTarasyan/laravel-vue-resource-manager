# Laravel Vue Resource Manager

A modern resource management application built with **Laravel, Vue.js, and Tailwind CSS**.

The project demonstrates how to build a clean, responsive CRUD application using Laravel as the backend and Vue.js as the frontend. It is designed around managing resources through an intuitive interface with create, read, update, and delete functionality.

## ✨ Features

* 📋 Resource management
* ➕ Create new resources
* 👀 View resource details
* ✏️ Edit existing resources
* 🗑️ Delete resources
* 🔎 Search and filter resources
* 📄 Pagination
* ⚡ Dynamic Vue.js interface
* 🎨 Modern Tailwind CSS UI
* 📱 Responsive design
* 🔄 Laravel + Vue integration
* 🗄️ MySQL database
* 🧩 Reusable Vue components
* 🛡️ Server-side validation

## 🛠️ Tech Stack

### Backend

* **Laravel**
* **PHP**
* **MySQL**
* **Laravel Eloquent ORM**
* **Laravel Validation**
* **Laravel Routing**

### Frontend

* **Vue.js**
* **Tailwind CSS**
* **Vite**
* JavaScript

### Development Tools

* Composer
* NPM
* Git

## 🏗️ Architecture

The application follows a modern Laravel + Vue architecture:

```text
┌─────────────────────────────┐
│          Vue.js             │
│                             │
│  Components                 │
│  Forms                      │
│  Resource List              │
│  Resource Details           │
└──────────────┬──────────────┘
               │
               │ HTTP
               ▼
┌─────────────────────────────┐
│          Laravel            │
│                             │
│  Routes                     │
│  Controllers                │
│  Validation                 │
│  Models                     │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│           MySQL             │
│                             │
│       Resource Data         │
└─────────────────────────────┘
```

## 📁 Project Structure

```text
laravel-vue-resource-manager/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Requests/
│   │
│   └── Models/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── resources/
│   ├── js/
│   │   ├── components/
│   │   ├── pages/
│   │   └── ...
│   │
│   └── css/
│
├── routes/
│   ├── web.php
│   └── ...
│
├── tests/
│
├── public/
├── storage/
│
├── composer.json
├── package.json
└── vite.config.js
```

## 🔄 CRUD Workflow

The application demonstrates the complete CRUD lifecycle.

### Create

A user fills in a form and submits a new resource.

```text
Vue Form
   ↓
Laravel Route
   ↓
Controller
   ↓
Validation
   ↓
Eloquent Model
   ↓
MySQL
```

### Read

Resources are retrieved from the database and displayed in the Vue interface.

```text
MySQL
   ↓
Eloquent
   ↓
Laravel
   ↓
Vue.js
   ↓
Resource List
```

### Update

Existing resources can be edited through the Vue interface.

```text
Vue Edit Form
   ↓
Laravel
   ↓
Validation
   ↓
Eloquent
   ↓
MySQL
```

### Delete

Resources can be removed from the database.

```text
Vue
   ↓
Laravel
   ↓
Eloquent
   ↓
MySQL
```

## 🎨 User Interface

The frontend focuses on a clean and modern user experience.

The interface includes:

* Responsive resource tables/cards
* Clean forms
* Action buttons
* Validation messages
* Loading states
* Empty states
* Confirmation dialogs
* Responsive layouts
* Tailwind utility classes

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/InnaTarasyan/laravel-vue-resource-manager.git

cd laravel-vue-resource-manager
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install JavaScript dependencies

```bash
npm install
```

### 4. Create the environment file

```bash
cp .env.example .env
```

### 5. Generate the application key

```bash
php artisan key:generate
```

### 6. Configure the database

Update the database configuration in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=resource_manager
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Run migrations

```bash
php artisan migrate
```

### 8. Start Laravel

```bash
php artisan serve
```

### 9. Start Vite

In another terminal:

```bash
npm run dev
```

The application will then be available at:

```text
http://127.0.0.1:8000
```

## 🧪 Testing

Run the test suite with:

```bash
php artisan test
```

## 📌 What This Project Demonstrates

This project was created to demonstrate practical experience with:

* Laravel application architecture
* Vue.js frontend development
* Tailwind CSS
* CRUD operations
* Eloquent ORM
* Database migrations
* Form validation
* Laravel routing
* Vue components
* HTTP requests
* Vite
* Responsive web design
* Component-based frontend development
* Clean separation between backend and frontend responsibilities

## 🔮 Future Improvements

Possible improvements include:

* Authentication and user accounts
* Role-based permissions
* Advanced filtering
* Sorting
* Bulk actions
* Dashboard with statistics
* Notifications
* File/image uploads
* API integration
* Automated tests
* Docker support
* CI/CD pipeline

## 📸 Screenshots

### Resource List

*Add a screenshot of the resource list here.*

### Create Resource

*Add a screenshot of the create form here.*

### Edit Resource

*Add a screenshot of the edit interface here.*

### Resource Details

*Add a screenshot of the resource details here.*

## 📄 License

This project is open-sourced software licensed under the MIT License.

## 👩‍💻 Author

**Inna Tarasyan**

GitHub:
https://github.com/InnaTarasyan

---

⭐ If you find this project useful, feel free to star the repository.

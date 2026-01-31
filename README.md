# The Vault API 🔒

A security-first RESTful API built with Laravel 11, designed for secure document management and encrypted note storage. This project serves as the backbone for the "Vault" ecosystem, implementing high-level architectural patterns and strict Role-Based Access Control (RBAC).

## 🚀 Key Features

* **Layered Security:** Implementation of Laravel Sanctum for state-of-the-art token-based authentication.
* **Role-Based Access Control (RBAC):** Granular permissions managed via Spatie Laravel-Permission.
* **Scalable File Storage:** Decoupled storage logic using the Service Provider pattern and Interfaces, allowing seamless switching between Local and AWS S3 storage.
* **Test-Driven Development:** 100% feature coverage using Pest PHP.
* **Resource Transformation:** Standardized JSON responses using Eloquent Resources for consistent frontend consumption.

## 🛠 Architectural Highlights

* **Dependency Inversion:** Controllers depend on `FileStorageInterface`, not concrete implementations.
* **Policy-Driven Authorization:** Model-level security ensured by Laravel Policies to prevent ID enumeration and unauthorized data access.
* **FormRequest Validation:** Strict data integrity checks and sanitization before hitting the controller logic.

---

## 📖 API Documentation

### Authentication
| Method | Endpoint | Description | Payload |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/register` | User onboarding | `name, email, password, password_confirmation` |
| `POST` | `/api/v1/login` | Authenticate & get token | `email, password` |
| `POST` | `/api/v1/logout` | Revoke access token | *Bearer Token required* |

### Documents & Vaults
| Method | Endpoint | Description | Payload |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/documents` | List user's documents | *None* |
| `POST` | `/api/v1/documents` | Create Note or Upload File | `title, content, type (note/file), file (optional)` |
| `GET` | `/api/v1/documents/{id}` | View specific item | *None (Policy enforced)* |
| `PATCH` | `/api/v1/documents/{id}` | Update metadata | `title, content` |
| `DELETE` | `/api/v1/documents/{id}` | Remove from vault | *None* |

---

## 🧪 Testing
The project uses **Pest PHP** for a modern, functional testing experience.


# Run the test suite
```php artisan test```

# 🏗 Setup & Installation
1- Clone the repository and install dependencies:
```composer install```

2- Set up your environment:
```cp .env.example .env```
```php artisan key:generate```

3- Run migrations and seeders:
```php artisan migrate --seed```

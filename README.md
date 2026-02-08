# The Vault API | Keepr 🔒

A security-first RESTful API built with Laravel 11, designed for secure document management and encrypted note storage. This project serves as the backbone for the "Vault" ecosystem, implementing high-level architectural patterns and strict Role-Based Access Control (RBAC).

# Keepr API | Neural Document Engine

The **Keepr API** is the backbone of the Keepr ecosystem. Built with Laravel, it handles secure document storage, vector embedding synchronization, and AI-powered metadata extraction.

## 🧠 Intelligence Layer
* **OCR Engine:** Integrated with Gemini 1.5 Flash for high-accuracy document transcription.
* **Vector Search:** Utilizes **pgvector** to perform semantic similarity searches across the document vault.
* **Queue Management:** Background processing for AI tasks to ensure low-latency API responses.


## 🚀 Key Features

* **Layered Security:** Implementation of Laravel Sanctum for state-of-the-art token-based authentication.
* **Role-Based Access Control (RBAC):** Granular permissions managed via Spatie Laravel-Permission.
* **Scalable File Storage:** Decoupled storage logic using the Service Provider pattern and Interfaces, allowing seamless switching between Local and AWS S3 storage.
* **Test-Driven Development:** 100% feature coverage using Pest PHP.
* **Resource Transformation:** Standardized JSON responses using Eloquent Resources for consistent frontend consumption.
* **Dependency Inversion:** Controllers depend on `FileStorageInterface`, not concrete implementations.
* **Policy-Driven Authorization:** Model-level security ensured by Laravel Policies to prevent ID enumeration and unauthorized data access.
* **FormRequest Validation:** Strict data integrity checks and sanitization before hitting the controller logic.


## 🛠️ Tech Stack
* **Language:** [PHP 8.3](https://www.php.net/)
* **Framework:** [Laravel 11](https://laravel.com/)
* **Database:** [PostgreSQL](https://www.postgresql.org/) with `pgvector`
* **AI Integration:** [Google Gemini API](https://ai.google.dev/)
* **Authentication:** [Laravel Sanctum](https://laravel.com/docs/sanctum)
* **Testing:** Pest / PHPUnit


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



## 🔧 Installation & Setup
1. **Clone the repo:**
   ```bash
   git clone [https://github.com/your-username/vault-api.git](https://github.com/your-username/vault-api.git)```

2. **Install PHP dependencies:**
```composer install```

3. **Setup Environment:**

```cp .env.example .env```
```php artisan key:generate```

4. **Run Migrations:**

```php artisan migrate```

5. **Start Server:**
```php artisan serve```

🔐 Security Features
Encrypted Storage: All user documents are stored with unique, non-predictable pathing.

CORS Protection: Configured for strict origin validation.

Rate Limiting: Protects AI endpoints from exhaustion.

Developed by mbilal.ca

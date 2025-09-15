# Laravel Authentication Learning Project

Đây là một project Laravel hoàn chỉnh để học cách module login hoạt động, quản lý token và user. Project sử dụng Laravel Sanctum để quản lý API authentication.

## 🚀 Tính năng

-   **User Registration**: Đăng ký tài khoản mới với validation
-   **User Login**: Đăng nhập với email/password
-   **Token Management**: Quản lý API tokens với Laravel Sanctum
-   **User Profile**: Xem và cập nhật thông tin cá nhân
-   **Password Change**: Đổi mật khẩu
-   **Logout**: Đăng xuất (single device hoặc all devices)
-   **Token Revocation**: Thu hồi tokens cụ thể
-   **User Status**: Kiểm tra trạng thái active của user
-   **Frontend Demo**: Giao diện web để test các tính năng

## 📋 Yêu cầu hệ thống

-   PHP 8.3+
-   Composer
-   Laravel 11
-   SQLite (hoặc MySQL/PostgreSQL)

## 🛠️ Cài đặt

1. **Clone project:**

```bash
git clone <repository-url>
cd auth-learning-project
```

2. **Cài đặt dependencies:**

```bash
composer install
```

3. **Cấu hình environment:**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Chạy migrations:**

```bash
php artisan migrate
```

5. **Khởi động server:**

```bash
php artisan serve
```

6. **Truy cập demo:**

```
http://localhost:8000/demo.html
```

## 📚 Cấu trúc Project

```
auth-learning-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── AuthController.php    # Controller xử lý authentication
│   │   └── Middleware/
│   │       └── EnsureUserIsActive.php    # Middleware kiểm tra user active
│   └── Models/
│       └── User.php                      # User model với Sanctum
├── database/
│   └── migrations/
│       ├── 0001_01_01_000000_create_users_table.php
│       ├── 2025_09_13_085426_create_personal_access_tokens_table.php
│       └── 2025_09_13_085435_add_additional_fields_to_users_table.php
├── routes/
│   └── api.php                           # API routes
├── public/
│   └── demo.html                         # Frontend demo
└── config/
    └── sanctum.php                       # Sanctum configuration
```

## 🔐 API Endpoints

### Public Routes (Không cần authentication)

#### POST `/api/auth/register`

Đăng ký tài khoản mới

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "0123456789",
    "date_of_birth": "1990-01-01",
    "gender": "male",
    "address": "123 Main St"
}
```

**Response:**

```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "0123456789",
            "date_of_birth": "1990-01-01",
            "gender": "male",
            "address": "123 Main St",
            "is_active": true,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

#### POST `/api/auth/login`

Đăng nhập

**Request Body:**

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": { ... },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Protected Routes (Cần authentication)

Tất cả routes dưới đây cần header:

```
Authorization: Bearer {token}
```

#### GET `/api/auth/profile`

Lấy thông tin profile của user hiện tại

#### PUT `/api/auth/profile`

Cập nhật thông tin profile

**Request Body:**

```json
{
    "name": "John Updated",
    "phone": "0987654321",
    "address": "456 New St"
}
```

#### POST `/api/auth/change-password`

Đổi mật khẩu

**Request Body:**

```json
{
    "current_password": "oldpassword",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

#### POST `/api/auth/logout`

Đăng xuất (revoke current token)

#### POST `/api/auth/logout-all`

Đăng xuất từ tất cả devices (revoke all tokens)

#### GET `/api/auth/tokens`

Lấy danh sách tất cả tokens của user

#### DELETE `/api/auth/tokens/{tokenId}`

Thu hồi token cụ thể

#### GET `/api/user`

Test protected route

## 🏗️ Kiến trúc Authentication

### 1. Laravel Sanctum

-   **Personal Access Tokens**: Mỗi user có thể có nhiều tokens
-   **Token Abilities**: Có thể phân quyền cho từng token
-   **Token Expiration**: Tokens có thể có thời hạn
-   **Token Revocation**: Có thể thu hồi tokens

### 2. User Model

```php
class User extends Authenticatable
{
    use HasApiTokens; // Sanctum trait

    protected $fillable = [
        'name', 'email', 'password', 'phone',
        'date_of_birth', 'gender', 'address', 'avatar', 'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];
}
```

### 3. Middleware

-   **auth:sanctum**: Xác thực token
-   **user.active**: Kiểm tra user có active không

### 4. Token Flow

1. User đăng nhập → Tạo token mới
2. Client lưu token → Gửi trong header mỗi request
3. Server validate token → Cho phép truy cập
4. User đăng xuất → Revoke token

## 🔒 Bảo mật

### 1. Password Hashing

```php
'password' => Hash::make($request->password)
```

### 2. Token Security

-   Tokens được hash trong database
-   Chỉ trả về plain text token một lần (khi tạo)
-   Có thể revoke tokens bất kỳ lúc nào

### 3. User Status

-   Kiểm tra `is_active` trước khi cho phép truy cập
-   Middleware tự động block inactive users

### 4. Validation

-   Tất cả input đều được validate
-   Password confirmation required
-   Email unique constraint

## 🧪 Testing

### 1. Sử dụng Frontend Demo

Truy cập `http://localhost:8000/demo.html` để test:

-   Đăng ký tài khoản mới
-   Đăng nhập
-   Xem profile
-   Test protected routes
-   Đăng xuất

### 2. Sử dụng Postman/Insomnia

Import các endpoints và test với:

-   Headers: `Authorization: Bearer {token}`
-   Content-Type: `application/json`

### 3. Sử dụng cURL

```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# Get profile
curl -X GET http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer {token}"
```

## 📖 Học tập

### 1. Hiểu Token-based Authentication

-   Tại sao cần tokens?
-   Cách tokens hoạt động?
-   So sánh với session-based auth

### 2. Laravel Sanctum

-   Cách cài đặt và cấu hình
-   Personal Access Tokens
-   API Authentication
-   SPA Authentication

### 3. Security Best Practices

-   Password hashing
-   Token management
-   Input validation
-   User status checking

### 4. API Design

-   RESTful endpoints
-   Response format
-   Error handling
-   Status codes

## 🚀 Mở rộng

### 1. Thêm tính năng

-   Email verification
-   Password reset
-   Two-factor authentication
-   Role-based permissions
-   API rate limiting

### 2. Cải thiện UI

-   React/Vue.js frontend
-   Mobile app
-   Admin dashboard

### 3. Database

-   Thêm user roles
-   User preferences
-   Login history
-   Audit logs

## 📝 Ghi chú

-   Project này chỉ dành cho mục đích học tập
-   Trong production, cần thêm nhiều security measures
-   Luôn validate và sanitize input
-   Sử dụng HTTPS trong production
-   Implement proper logging và monitoring

## 🤝 Đóng góp

Nếu bạn muốn đóng góp hoặc có câu hỏi, hãy tạo issue hoặc pull request.

## 📄 License

MIT License
# vue-ecommce

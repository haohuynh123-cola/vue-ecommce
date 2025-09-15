# 🚀 Quick Start Guide

## Bắt đầu nhanh với Laravel Authentication Project

### 1. Khởi động project

```bash
# Di chuyển vào thư mục project
cd auth-learning-project

# Khởi động Laravel server
php artisan serve
```

### 2. Truy cập demo

Mở trình duyệt và truy cập: **http://localhost:8000/demo.html**

### 3. Test các tính năng

#### Đăng ký tài khoản mới

1. Click tab "Register"
2. Điền thông tin:
    - Name: Tên của bạn
    - Email: email@example.com
    - Password: password123
    - Confirm Password: password123
    - Phone: 0123456789 (optional)
3. Click "Register"

#### Đăng nhập

1. Click tab "Login"
2. Điền email và password vừa tạo
3. Click "Login"

#### Test protected routes

1. Sau khi đăng nhập thành công
2. Click "Test Protected Route"
3. Xem kết quả

#### Đăng xuất

1. Click "Logout" để đăng xuất

### 4. API Testing với cURL

#### Đăng ký

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### Đăng nhập

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

#### Lấy profile (thay {token} bằng token từ response login)

```bash
curl -X GET http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### 5. Các file quan trọng cần đọc

1. **`README.md`** - Tổng quan project
2. **`AUTHENTICATION_GUIDE.md`** - Hướng dẫn chi tiết về authentication
3. **`app/Http/Controllers/Api/AuthController.php`** - Logic xử lý authentication
4. **`routes/api.php`** - API endpoints
5. **`public/demo.html`** - Frontend demo

### 6. Cấu trúc database

#### Bảng `users`

-   `id` - Primary key
-   `name` - Tên user
-   `email` - Email (unique)
-   `password` - Mật khẩu (hashed)
-   `phone` - Số điện thoại
-   `date_of_birth` - Ngày sinh
-   `gender` - Giới tính (male/female/other)
-   `address` - Địa chỉ
-   `avatar` - Ảnh đại diện
-   `is_active` - Trạng thái active
-   `last_login_at` - Lần đăng nhập cuối
-   `created_at`, `updated_at` - Timestamps

#### Bảng `personal_access_tokens` (Sanctum)

-   `id` - Primary key
-   `tokenable_type` - Model type (User)
-   `tokenable_id` - User ID
-   `name` - Tên token
-   `token` - Token hash
-   `abilities` - Quyền của token
-   `last_used_at` - Lần sử dụng cuối
-   `expires_at` - Thời hạn token
-   `created_at`, `updated_at` - Timestamps

### 7. Middleware

-   **`auth:sanctum`** - Xác thực token
-   **`user.active`** - Kiểm tra user active

### 8. Environment Variables

Đảm bảo file `.env` có:

```env
APP_NAME="Laravel Auth Learning"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1
```

### 9. Troubleshooting

#### Lỗi 419 CSRF Token Mismatch

-   Đảm bảo đang test API routes (`/api/*`) chứ không phải web routes
-   Sử dụng `Accept: application/json` header

#### Lỗi 401 Unauthorized

-   Kiểm tra token có đúng không
-   Kiểm tra header `Authorization: Bearer {token}`
-   Kiểm tra token chưa expire

#### Lỗi 403 Forbidden

-   User có thể bị deactivate (`is_active = false`)
-   Kiểm tra middleware `user.active`

#### Database errors

-   Chạy `php artisan migrate` để tạo tables
-   Kiểm tra file `database/database.sqlite` tồn tại

### 10. Next Steps

Sau khi hiểu cơ bản, bạn có thể:

1. **Thêm tính năng:**

    - Email verification
    - Password reset
    - Two-factor authentication
    - Role-based permissions

2. **Cải thiện security:**

    - Rate limiting
    - Input sanitization
    - Audit logging
    - Token expiration

3. **Frontend integration:**

    - React/Vue.js app
    - Mobile app
    - Admin dashboard

4. **Testing:**
    - Unit tests
    - Feature tests
    - API tests

### 11. Resources

-   [Laravel Documentation](https://laravel.com/docs)
-   [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
-   [Laravel Authentication](https://laravel.com/docs/authentication)
-   [PHP CS Fixer (Pint)](https://laravel.com/docs/pint)

---

**Happy Learning! 🎉**

# Router Structure

Cấu trúc router được tổ chức theo từng nhóm chức năng để dễ quản lý và bảo trì.

## 📁 Cấu trúc thư mục

```
resources/js/router/
├── index.js              # Router chính với guards và middleware
├── routes/
│   ├── index.js          # Tổng hợp tất cả routes
│   ├── auth.js           # Routes đăng nhập/đăng ký
│   ├── customer.js       # Routes cho khách hàng
│   └── admin.js          # Routes cho quản trị
└── README.md             # Tài liệu này
```

## 🛣️ Các nhóm routes

### 1. **Auth Routes** (`auth.js`)

-   `/login` - Đăng nhập
-   `/register` - Đăng ký
-   **Meta**: `requiresGuest: true` - Chỉ cho phép người dùng chưa đăng nhập

### 2. **Customer Routes** (`customer.js`)

-   `/` - Trang chủ
-   `/products` - Danh sách sản phẩm
-   `/products/:id` - Chi tiết sản phẩm
-   `/categories` - Danh mục sản phẩm
-   `/orders` - Đơn hàng của khách hàng

### 3. **Admin Routes** (`admin.js`)

-   `/admin` - Dashboard quản trị
-   `/admin/products` - Quản lý sản phẩm
-   `/admin/categories` - Quản lý danh mục
-   `/admin/orders` - Quản lý đơn hàng
-   `/admin/users` - Quản lý người dùng
-   `/admin/settings` - Cài đặt hệ thống
-   **Meta**: `requiresAuth: true, requiresAdmin: true` - Yêu cầu đăng nhập và quyền admin

## 🔒 Navigation Guards

### `beforeEach` Guard

-   **Authentication Check**: Kiểm tra người dùng đã đăng nhập chưa
-   **Authorization Check**: Kiểm tra quyền admin cho các route admin
-   **Guest Check**: Chuyển hướng người dùng đã đăng nhập khỏi trang login/register

### Meta Fields

-   `requiresAuth`: Yêu cầu đăng nhập
-   `requiresAdmin`: Yêu cầu quyền admin/manager
-   `requiresGuest`: Chỉ cho phép người dùng chưa đăng nhập

## 🚀 Cách sử dụng

### Thêm route mới

1. Mở file route tương ứng (auth.js, customer.js, hoặc admin.js)
2. Thêm route mới vào mảng routes
3. Đặt meta fields phù hợp

### Ví dụ thêm route mới:

```javascript
{
    path: "/admin/reports",
    component: () => import("../../components/admin/AdminReports.vue"),
    name: "admin-reports",
    meta: {
        requiresAuth: true,
        requiresAdmin: true,
    },
}
```

### Thêm nhóm route mới

1. Tạo file mới trong thư mục `routes/`
2. Export mảng routes từ file đó
3. Import và thêm vào `routes/index.js`

## 🔧 Tùy chỉnh Guards

Để thêm logic guard mới, chỉnh sửa file `router/index.js`:

```javascript
router.beforeEach(async (to, from, next) => {
    // Logic hiện tại...

    // Thêm logic mới ở đây
    if (to.meta.requiresSpecialPermission) {
        // Kiểm tra quyền đặc biệt
    }

    next();
});
```

## 📝 Lưu ý

-   Tất cả routes admin đều có prefix `/admin`
-   Sử dụng lazy loading (`() => import()`) cho performance tốt hơn
-   Meta fields giúp kiểm soát quyền truy cập dễ dàng
-   Router guards đảm bảo bảo mật và UX tốt

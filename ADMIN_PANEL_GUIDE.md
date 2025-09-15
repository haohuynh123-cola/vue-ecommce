# Admin Panel Guide

## 🎯 Tổng quan

Dự án này đã được cấu hình để chỉ chạy Admin Panel, không có phần client/customer. Tất cả các component và route liên quan đến customer đã được xóa. Tên các file và route đã được đơn giản hóa, bỏ prefix "admin" vì project này chỉ dành cho admin. Cấu trúc components cũng đã được đơn giản hóa bằng cách di chuyển tất cả components từ folder `admin/` lên root của `components/`.

## 🏗️ Cấu trúc Admin Panel

### **Layout Structure**

```
resources/js/
├── layouts/
│   └── MainLayout.vue           # Layout chính
├── components/
│   ├── layout/
│   │   ├── Sidebar.vue          # Sidebar navigation
│   │   └── Navbar.vue           # Top navigation bar
│   ├── ui/
│   │   └── StatsCard.vue        # Stats cards component
│   ├── Login.vue                # Login page
│   ├── Dashboard.vue            # Dashboard component
│   ├── Products.vue             # Products management
│   ├── Categories.vue           # Categories management
│   ├── Orders.vue               # Orders management
│   ├── Users.vue                # Users management
│   ├── Settings.vue             # Settings
│   └── shared/                  # Reusable components
│       ├── ui/
│       ├── forms/
│       └── layout/
├── router/
│   └── index.js                 # All routes
└── stores/
    └── auth.js                  # Authentication store
```

### **Routes**

-   `/login` → Admin login page
-   `/` → Dashboard
-   `/products` → Products management
-   `/categories` → Categories management
-   `/orders` → Orders management
-   `/users` → Users management
-   `/settings` → Settings

## 🎨 Design Features

### **Admin Layout**

-   **Sidebar**: Dark theme với navigation menu
-   **Header**: Clean header với search, notifications, user menu
-   **Responsive**: Mobile-friendly với collapsible sidebar
-   **Modern UI**: Sử dụng Tailwind CSS với gradient accents

### **Components**

-   **StatsCard**: Hiển thị metrics với icons và change indicators
-   **Sidebar**: Navigation sidebar với sections (Main, Management, System)
-   **Navbar**: Top navigation với search và user menu

## 🔐 Authentication

### **Login Credentials**

-   **Email**: `admin@example.com`
-   **Password**: `password`

### **Login Flow**

1. User truy cập `/` → Nếu chưa login → Redirect to `/login`
2. Nhập email và password
3. Sau khi login thành công → Redirect to `/` (dashboard)

### **Authorization**

-   Tất cả admin routes yêu cầu authentication
-   Kiểm tra role admin/manager
-   Guest routes chỉ cho phép khi chưa login

## 🚀 Cách sử dụng

### **1. Khởi động project**

```bash
# Backend
php artisan serve

# Frontend
npm run dev
```

### **2. Truy cập admin panel**

-   URL: `http://localhost:8000`
-   Tự động redirect to `/admin`
-   Nếu chưa login → redirect to `/login`

### **3. Login**

-   Email: admin@example.com (hoặc user có role admin/manager)
-   Password: password

### **4. Navigation**

-   **Dashboard**: Tổng quan metrics và stats
-   **Products**: Quản lý sản phẩm
-   **Categories**: Quản lý danh mục
-   **Orders**: Quản lý đơn hàng
-   **Users**: Quản lý người dùng
-   **Settings**: Cài đặt hệ thống

## 📱 Responsive Design

### **Desktop (lg+)**

-   Sidebar luôn hiển thị
-   Full navigation menu
-   Hover effects

### **Mobile/Tablet**

-   Collapsible sidebar
-   Hamburger menu
-   Touch-friendly interface

## 🎯 Features

### **Dashboard**

-   Stats cards với metrics
-   Recent activity
-   Quick actions

### **Products Management**

-   Product listing với search/filter
-   CRUD operations
-   Image management
-   Category assignment

### **Orders Management**

-   Order listing
-   Status management
-   Customer information

### **Users Management**

-   User listing
-   Role assignment
-   Permission management

## 🔧 Customization

### **Colors**

-   Primary: Blue gradient
-   Secondary: Purple accent
-   Success: Green
-   Warning: Yellow
-   Error: Red

### **Icons**

-   Heroicons (SVG)
-   Consistent icon set
-   Scalable vector graphics

### **Typography**

-   Inter font family
-   Consistent sizing
-   Proper hierarchy

## 📝 Notes

-   Tất cả customer/client code đã được xóa
-   Chỉ còn admin functionality
-   Clean và focused codebase
-   Ready for production deployment

## 🚀 Next Steps

1. **Add more admin features**:

    - Reports & Analytics
    - System logs
    - Backup management
    - Email templates

2. **Enhance UI**:

    - Dark mode toggle
    - Theme customization
    - Advanced charts
    - Data visualization

3. **Performance**:
    - Lazy loading
    - Code splitting
    - Caching strategies
    - API optimization

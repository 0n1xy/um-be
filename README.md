# Laravel API Documentation

Đây là một ứng dụng API được xây dựng bằng Laravel, chuyên quản lý người dùng và xác thực. Dưới đây là danh sách các endpoint có sẵn cùng hướng dẫn cài đặt và sử dụng.

## Table of Contents

-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Running the Application](#running-the-application)
-   [API Endpoints](#api-endpoints)
-   [Demo & Deployment](#demo--deployment)
-   [Additional Notes](#additional-notes)
-   [License](#license)

## Installation

1. **Clone repository:**
    ```bash
    git clone <repository-url>
    cd <repository-folder>
    ```
2. **Cài đặt dependencies bằng Composer:**
    ```bash
    composer install
    ```
3. **Copy file môi trường:**
    ```bash
    cp .env.example .env
    ```
4. **Cấu hình file `.env`:**  
   Cập nhật các thông số như database, mail, … theo môi trường của bạn.
5. **Generate key cho ứng dụng:**
    ```bash
    php artisan key:generate
    ```
6. **Chạy migration để tạo bảng:**
    ```bash
    php artisan migrate
    ```

## Configuration

-   **Database:** Đảm bảo bạn đã cấu hình đúng các thông số kết nối trong file `.env`.
-   **Environment Variables:** Kiểm tra các biến môi trường khác (ví dụ: APP_DEBUG, MAIL_DRIVER, …) theo yêu cầu ứng dụng.

## Running the Application

Chạy ứng dụng bằng lệnh:

```bash
php artisan serve
```

Ứng dụng sẽ chạy tại địa chỉ mặc định: [http://localhost:8000](http://localhost:8000).

## API Endpoints

Bạn có thể xem toàn bộ danh sách endpoint bằng lệnh:

```bash
php artisan route:list
```

### User Management (Admin)

-   **GET /api/admin/users**  
    Lấy danh sách tất cả người dùng.  
    _Controller_: `UserController@getAllUserData`

-   **GET /api/admin/users/{id}**  
    Lấy thông tin chi tiết của 1 người dùng theo ID.  
    _Controller_: `UserController@getUserById`

-   **PUT /api/admin/users/{id}**  
    Cập nhật thông tin người dùng.  
    _Controller_: `UserController@update`

-   **DELETE /api/admin/users/{id}**  
    Xóa người dùng theo ID.  
    _Controller_: `UserController@delete`

### Authentication

-   **POST /api/auth/login**  
    Xác thực và đăng nhập người dùng.  
    _Controller_: `AuthController@login`

-   **POST /api/auth/logout**  
    Đăng xuất người dùng đã xác thực.  
    _Controller_: `UserController@logout`

-   **GET /api/auth/me**  
    Lấy thông tin người dùng hiện đang đăng nhập.  
    _Controller_: `AuthController@me`

-   **POST /api/auth/register**  
    Đăng ký người dùng mới.  
    _Controller_: `AuthController@register`

## Demo & Deployment

Ứng dụng đã được triển khai tại:  
🔗 [Dashboard URL](https://um-fe.vercel.app/dashboard)

### Test Accounts:

-   **Admin Account**

    -   Email: `admin@example.com`
    -   Password: `123456789`

-   **User Account**
    -   Email: `user5@example.com`
    -   Password: `userpassword5`

## Additional Notes

-   **Authorization:**  
    Hầu hết các endpoint (ngoại trừ login và register) yêu cầu người dùng phải gửi kèm token xác thực (ví dụ: JWT) qua header:

    ```
    Authorization: Bearer <token>
    ```

-   **Validation & Error Handling:**  
    Các yêu cầu đến API đều được kiểm tra và nếu có lỗi sẽ trả về thông báo lỗi tương ứng. Hãy kiểm tra log và response để xử lý các trường hợp lỗi.

-   **Missing Endpoints:**  
    Nếu bạn thấy còn thiếu endpoint nào hoặc có yêu cầu chức năng bổ sung, vui lòng cập nhật README và code tương ứng.

## License

Ứng dụng này được cấp phép theo [MIT License](LICENSE).

# **Desktop app Hệ thống quản lý bán vé tàu (Java Swing)**
- [Giới thiệu](#giới-thiệu)
- [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
- [Hướng dẫn cài đặt](#hướng-dẫn-cài-đặt)

## **Giới thiệu**

  **HỌC PHẦN LẬP TRÌNH WEB**
  
  **Nhóm 3 - Lớp 74DCTT28 - Trường Đại học Công nghệ Giao thông Vận tải**
  ```
  Hoàng Quốc Phương
  Nguyễn Văn Tuấn
  Nguyễn Phúc Thanh
  Hoàng Trọng Nguyên
  Phạm Quang Minh
  ```

## **Yêu cầu hệ thống**
- [XAMPP 8.2.12](https://www.apachefriends.org/download.html)

## **Hướng dẫn cài đặt**
Cài đặt IDE: Bạn có thể sử dụng các IDE hoặc các text editor thông thường để phát triển code cho dự án này (nếu muốn). Có rất nhiều IDE/Text Editor hỗ trợ code tốt, điển hình như:
- [VSCode](https://code.visualstudio.com/)
- [Sublime Text](https://www.sublimetext.com/)

Ngoài ra còn rất nhiều các công cụ khác, bạn có thể tìm kiếm trên mạng.

Hướng dẫn chi tiết:
- Clone project này về máy tính cá nhân.

- Khởi chạy Apache và MySQL trong XAMPP, truy cập `phpmyadmin` bằng trình duyệt bất kỳ, sau đó import file [`quan_ly_ban_ve_tau.sql`](./assets/sql/quan_ly_ban_ve_tau.sql) để nhập cơ sở dữ liệu `quan_ly_ban_ve_tau`. Bạn có thể đổi tên database sau khi import thành công (Lưu ý: Bạn có thể tắt kiểm tra khoá ngoại khi import để không bị báo lỗi xung đột khoá ngoại - do sai thứ tự tạo bảng vì file sql này xuất ra từ MySQL, thứ tự tạo bảng sắp xếp theo alphabet).

- Trong thư mục [`config/`](./config/):
  + Tạo một bản sao của file [config.php.example](./config/config.php.example), sau đó đổi tên thành `config.php`. Đây là file cấu hình hệ thống, bạn có thể thay đổi thông tin kết nối DB tại file này.

- Khởi chạy:
   + Truy cập web bằng đường link mặc định trong file `config.php` hoặc đường link mới của bạn.
   + Đăng nhập bằng mã nhân viên bất kỳ nằm trong bảng `nhan_vien` với mật khẩu mặc định `123456`.
   + Tài khoản quản trị viên sẽ được sử dụng toàn bộ chức năng, nhân viên chỉ được dùng một số chức năng quản lý, tra cứu nhất định.

Chú ý: Không thêm thủ công plain text vào cột `mat_khau` trong bảng (vì check mật khẩu sẽ luôn sai). Nếu muốn cấp tài khoản mới/cập nhật mật khẩu, sử dụng chức năng thêm nhân viên/đổi mật khẩu hoặc truy cập
[BCrypt Generator](https://bcrypt-generator.com/) để tạo mã băm (12 rounds) và thêm thủ công vào cột `mat_khau`.

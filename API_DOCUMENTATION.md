# Tài liệu API - Social Media App

## 📋 Mục lục
1. [Giới thiệu](#giới-thiệu)
2. [Cấu hình cơ bản](#cấu-hình-cơ-bản)
3. [Xác thực (Authentication)](#xác-thực-authentication)
4. [API Endpoints](#api-endpoints)
   - [Authentication](#authentication-endpoints)
   - [Posts (Bài viết)](#posts-bài-viết)
   - [Comments (Bình luận)](#comments-bình-luận)
   - [Users & Profiles](#users--profiles)
   - [Categories (Chuyên mục)](#categories-chuyên-mục)
   - [Votes (Bình chọn)](#votes-bình-chọn)
   - [Follow (Theo dõi)](#follow-theo-dõi)
   - [Reports (Báo cáo)](#reports-báo-cáo)
   - [Notifications (Thông báo)](#notifications-thông-báo)
   - [Chat & Messages](#chat--messages)
   - [Realtime](#realtime)
   - [Admin APIs](#admin-apis)
   - [Moderator APIs](#moderator-apis)

---

## Giới thiệu

Đây là tài liệu API cho ứng dụng Social Media. API sử dụng **RESTful** và trả về dữ liệu dưới dạng **JSON**.

**Base URL:** `http://127.0.0.1:8000/api` (hoặc domain của bạn)

**Lưu ý:** Tất cả các request phải có header:
```
Content-Type: application/json
Accept: application/json
```

---

## Cấu hình cơ bản

### CORS (Cross-Origin Resource Sharing)

Backend đã được cấu hình để cho phép requests từ các domain sau:
- `http://localhost:5173`
- `http://127.0.0.1:5173`
- `http://localhost:5174`
- `http://127.0.0.1:5174`

Nếu frontend chạy ở domain khác, cần cấu hình thêm trong file `config/cors.php` của backend.

### Format Response

Tất cả response đều trả về JSON với cấu trúc:

**Thành công:**
```json
{
  "data": { ... },
  "message": "Success message" // (tùy chọn)
}
```

**Lỗi:**
```json
{
  "message": "Error message",
  "errors": {
    "field_name": ["Error detail 1", "Error detail 2"]
  }
}
```

### Mã trạng thái HTTP

- `200` - Thành công
- `201` - Đã tạo thành công
- `400` - Bad Request (Dữ liệu không hợp lệ)
- `401` - Unauthorized (Chưa đăng nhập hoặc token hết hạn)
- `403` - Forbidden (Không có quyền)
- `404` - Not Found (Không tìm thấy)
- `422` - Validation Error (Dữ liệu không đúng format)
- `500` - Server Error

---

## Xác thực (Authentication)

API sử dụng **Token-based Authentication** (Bearer Token).

### Cách hoạt động:

1. **Đăng nhập** → Nhận token từ server
2. **Lưu token** vào localStorage/cookie
3. **Gửi token** trong header mỗi request:
   ```
   Authorization: Bearer {token}
   ```

### Ví dụ với JavaScript (Axios):

```javascript
// Cấu hình Axios
axios.defaults.baseURL = 'http://127.0.0.1:8000/api';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Sau khi đăng nhập, lưu token
const token = response.data.token;
localStorage.setItem('token', token);

// Thêm token vào mọi request
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

// Hoặc cho từng request
axios.get('/posts', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
```

### Token hết hạn

- Token hiện tại **KHÔNG hết hạn** (vĩnh viễn)
- Nếu nhận lỗi `401`, có nghĩa là:
  - Token không hợp lệ
  - User bị ban
  - Token đã bị xóa ở server

---

## API Endpoints

### Authentication Endpoints

#### 1. Đăng ký tài khoản

**POST** `/api/register`

**Không cần đăng nhập**

**Request Body:**
```json
{
  "name": "Nguyễn Văn A",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "user": {
    "id": 1,
    "name": "Nguyễn Văn A",
    "email": "user@example.com",
    "created_at": "2024-01-01T00:00:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
}
```

**Lỗi (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password confirmation does not match."]
  }
}
```

---

#### 2. Đăng nhập

**POST** `/api/login`

**Không cần đăng nhập**

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "user": {
    "id": 1,
    "name": "Nguyễn Văn A",
    "email": "user@example.com",
    "avatar": "https://res.cloudinary.com/.../avatar.jpg",
    "cover_photo_url": null,
    "created_at": "2024-01-01T00:00:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
}
```

**Lỗi (422):**
```json
{
  "message": "These credentials do not match our records."
}
```

**Lỗi khi user bị ban:**
```json
{
  "message": "Tài khoản của bạn đã bị khóa đến 01-01-2025 12:00:00",
  "errors": {
    "email": ["Tài khoản của bạn đã bị khóa đến 01-01-2025 12:00:00"]
  }
}
```

---

#### 3. Đăng xuất

**POST** `/api/logout`

**Cần đăng nhập** (gửi token trong header)

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

---

#### 4. Lấy thông tin user hiện tại

**GET** `/api/user`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "name": "Nguyễn Văn A",
  "email": "user@example.com",
  "avatar": "https://res.cloudinary.com/.../avatar.jpg",
  "cover_photo_url": null,
  "created_at": "2024-01-01T00:00:00.000000Z"
}
```

---

#### 5. Đăng nhập Admin (dành cho trang admin)

**POST** `/api/admin/login`

**Không cần đăng nhập**

**Request Body:**
```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "avatar": "...",
    "role": "admin"  // ← Thêm field role
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
}
```

**Lỗi (422):**
```json
{
  "message": "Tài khoản này không có quyền truy cập quản trị.",
  "errors": {
    "email": ["Tài khoản này không có quyền truy cập quản trị."]
  }
}
```

---

### Posts (Bài viết)

#### 1. Lấy danh sách bài viết

**GET** `/api/posts`

**Không cần đăng nhập** (nhưng nếu có token sẽ hiển thị thêm thông tin vote của user)

**Query Parameters:**
- `sort` (optional): `newest` (mặc định) hoặc `hot` (bài viết hot)
- `limit` (optional): Số bài viết mỗi trang (mặc định: 10, tối đa: 50)
- `category` (optional): ID chuyên mục để lọc
- `q` (optional): Từ khóa tìm kiếm
- `user_id` (optional): ID user để lọc bài viết của user đó
- `page` (optional): Số trang (mặc định: 1)

**Ví dụ:**
```
GET /api/posts?sort=hot&limit=20&category=1&page=2
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Tiêu đề bài viết",
      "thumbnail_url": "https://res.cloudinary.com/.../image.jpg",
      "category": {
        "id": 1,
        "name": "Công nghệ",
        "slug": "cong-nghe"
      },
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z",
      "author": {
        "id": 1,
        "name": "Nguyễn Văn A",
        "avatar": "https://res.cloudinary.com/.../avatar.jpg",
        "created_at": "2024-01-01T00:00:00.000000Z"
      },
      "comments_count": 5,
      "vote_score": 10,
      "user_vote": 1,  // 1 = upvote, -1 = downvote, 0 = chưa vote
      "is_following_author": false
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/posts?page=1",
    "last": "http://127.0.0.1:8000/api/posts?page=10",
    "prev": null,
    "next": "http://127.0.0.1:8000/api/posts?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "path": "http://127.0.0.1:8000/api/posts",
    "per_page": 10,
    "to": 10,
    "total": 100
  }
}
```

---

#### 2. Lấy chi tiết một bài viết

**GET** `/api/posts/{post_id}`

**Không cần đăng nhập** (nhưng nếu có token sẽ hiển thị thêm thông tin)

**Ví dụ:**
```
GET /api/posts/1
```

**Response (200):**
```json
{
  "id": 1,
  "title": "Tiêu đề bài viết",
  "thumbnail_url": "https://res.cloudinary.com/.../image.jpg",
  "content_html": "<p>Nội dung bài viết...</p>",  // ← Chỉ có khi xem chi tiết
  "category": {
    "id": 1,
    "name": "Công nghệ",
    "slug": "cong-nghe"
  },
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z",
  "author": {
    "id": 1,
    "name": "Nguyễn Văn A",
    "avatar": "https://res.cloudinary.com/.../avatar.jpg",
    "created_at": "2024-01-01T00:00:00.000000Z"
  },
  "comments_count": 5,
  "vote_score": 10,
  "user_vote": 1,
  "is_following_author": false
}
```

**Lỗi (404):**
```json
{
  "message": "Bài viết không tồn tại."
}
```

---

#### 3. Tạo bài viết mới

**POST** `/api/posts`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "title": "Tiêu đề bài viết",
  "content_html": "<p>Nội dung bài viết với HTML...</p>",
  "category_id": 1,
  "thumbnail_url": "https://res.cloudinary.com/.../image.jpg"  // optional
}
```

**Response (201):**
```json
{
  "id": 1,
  "title": "Tiêu đề bài viết",
  "thumbnail_url": "https://res.cloudinary.com/.../image.jpg",
  "category": {
    "id": 1,
    "name": "Công nghệ",
    "slug": "cong-nghe"
  },
  "created_at": "2024-01-01T00:00:00.000000Z",
  "author": {
    "id": 1,
    "name": "Nguyễn Văn A",
    "avatar": "..."
  },
  "comments_count": 0,
  "vote_score": 0,
  "user_vote": 0,
  "is_following_author": false
}
```

**Lỗi (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "category_id": ["The selected category id is invalid."]
  }
}
```

---

#### 4. Cập nhật bài viết

**PUT** `/api/posts/{post_id}`

**Cần đăng nhập** + **Chỉ tác giả mới được sửa**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "title": "Tiêu đề đã sửa",
  "content_html": "<p>Nội dung đã sửa...</p>",
  "category_id": 2,
  "thumbnail_url": "https://res.cloudinary.com/.../new-image.jpg"
}
```

**Response (200):** (giống như GET chi tiết)

**Lỗi (403):**
```json
{
  "message": "This action is unauthorized."
}
```

---

#### 5. Xóa bài viết

**DELETE** `/api/posts/{post_id}`

**Cần đăng nhập** + **Chỉ tác giả hoặc Moderator mới được xóa**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Bài viết đã được xóa."
}
```

**Lỗi (403):**
```json
{
  "message": "This action is unauthorized."
}
```

---

### Comments (Bình luận)

#### 1. Lấy bình luận của một bài viết

**GET** `/api/posts/{post_id}/comments`

**Không cần đăng nhập**

**Query Parameters:**
- `limit` (optional): Số bình luận mỗi trang (mặc định: 10, tối đa: 50)
- `page` (optional): Số trang

**Ví dụ:**
```
GET /api/posts/1/comments?limit=20&page=1
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "content": "Bình luận hay quá!",
      "post_id": 1,
      "parent_id": null,  // null = bình luận gốc
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z",
      "user": {
        "id": 2,
        "name": "Người dùng B",
        "avatar": "..."
      },
      "replies_count": 3  // Số phản hồi
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

#### 2. Lấy phản hồi của một bình luận

**GET** `/api/comments/{comment_id}/replies`

**Không cần đăng nhập**

**Response (200):**
```json
{
  "data": [
    {
      "id": 2,
      "content": "Đồng ý với bạn!",
      "post_id": 1,
      "parent_id": 1,  // ID của bình luận cha
      "created_at": "2024-01-01T00:00:00.000000Z",
      "user": {
        "id": 3,
        "name": "Người dùng C",
        "avatar": "..."
      },
      "replies_count": 0
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

#### 3. Tạo bình luận mới

**POST** `/api/comments`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "content": "Bình luận của tôi",
  "post_id": 1,
  "parent_id": null  // null = bình luận gốc, hoặc ID của bình luận cha nếu là phản hồi
}
```

**Response (201):**
```json
{
  "id": 1,
  "content": "Bình luận của tôi",
  "post_id": 1,
  "parent_id": null,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "user": {
    "id": 1,
    "name": "Nguyễn Văn A",
    "avatar": "..."
  },
  "replies_count": 0
}
```

---

#### 4. Cập nhật bình luận

**PATCH** `/api/comments/{comment_id}`

**Cần đăng nhập** + **Chỉ tác giả mới được sửa**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "content": "Nội dung đã sửa"
}
```

**Response (200):** (giống như GET)

---

#### 5. Xóa bình luận

**DELETE** `/api/comments/{comment_id}`

**Cần đăng nhập** + **Chỉ tác giả mới được xóa**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Bình luận đã được xóa."
}
```

---

### Users & Profiles

#### 1. Xem profile công khai

**GET** `/api/profiles/{user_id}`

**Không cần đăng nhập**

**Ví dụ:**
```
GET /api/profiles/1
```

**Response (200):**
```json
{
  "id": 1,
  "name": "Nguyễn Văn A",
  "avatar": "https://res.cloudinary.com/.../avatar.jpg",
  "cover_photo_url": "https://res.cloudinary.com/.../cover.jpg",
  "created_at": "2024-01-01T00:00:00.000000Z",
  "posts_count": 10,
  "followers_count": 50,
  "following_count": 20,
  "is_following": false  // true nếu user hiện tại đang follow user này
}
```

---

#### 2. Lấy danh sách người theo dõi (Followers)

**GET** `/api/profiles/{user_id}/followers`

**Không cần đăng nhập**

**Query Parameters:**
- `limit` (optional): Số lượng mỗi trang
- `page` (optional): Số trang

**Response (200):**
```json
{
  "data": [
    {
      "id": 2,
      "name": "Người dùng B",
      "avatar": "...",
      "created_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

#### 3. Lấy danh sách đang theo dõi (Following)

**GET** `/api/profiles/{user_id}/following`

**Không cần đăng nhập**

**Response (200):** (giống như followers)

---

#### 4. Tìm kiếm user

**GET** `/api/users/search`

**Không cần đăng nhập**

**Query Parameters:**
- `q` (required): Từ khóa tìm kiếm

**Ví dụ:**
```
GET /api/users/search?q=nguyen
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Nguyễn Văn A",
      "avatar": "..."
    }
  ]
}
```

---

#### 5. Cập nhật thông tin profile

**PATCH** `/api/profile/details`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Tên mới"
}
```

**Response (200):**
```json
{
  "message": "Profile updated successfully"
}
```

---

#### 6. Đổi mật khẩu

**PATCH** `/api/profile/password`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "current_password": "password123",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Response (200):**
```json
{
  "message": "Password updated successfully"
}
```

---

#### 7. Cập nhật avatar

**POST** `/api/user/avatar`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
avatar: [file]
```

**Response (200):**
```json
{
  "message": "Avatar updated successfully",
  "avatar": "https://res.cloudinary.com/.../new-avatar.jpg"
}
```

---

#### 8. Cập nhật cover photo

**POST** `/api/user/cover-photo`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
cover_photo: [file]
```

**Response (200):**
```json
{
  "message": "Cover photo updated successfully",
  "cover_photo_url": "https://res.cloudinary.com/.../new-cover.jpg"
}
```

---

### Categories (Chuyên mục)

#### 1. Lấy danh sách chuyên mục

**GET** `/api/categories`

**Không cần đăng nhập**

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Công nghệ",
      "slug": "cong-nghe",
      "description": "Các bài viết về công nghệ"
    }
  ]
}
```

---

#### 2. Lấy chi tiết một chuyên mục

**GET** `/api/categories/{category_id}`

**Không cần đăng nhập**

**Response (200):**
```json
{
  "id": 1,
  "name": "Công nghệ",
  "slug": "cong-nghe",
  "description": "Các bài viết về công nghệ"
}
```

---

### Votes (Bình chọn)

#### 1. Upvote (Bình chọn tích cực)

**POST** `/api/posts/{post_id}/upvote`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Upvoted successfully",
  "vote_score": 11,
  "user_vote": 1
}
```

**Lưu ý:** Nếu đã upvote rồi, gọi lại sẽ bỏ upvote (toggle).

---

#### 2. Downvote (Bình chọn tiêu cực)

**POST** `/api/posts/{post_id}/downvote`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Downvoted successfully",
  "vote_score": 9,
  "user_vote": -1
}
```

**Lưu ý:** Nếu đã downvote rồi, gọi lại sẽ bỏ downvote (toggle).

---

### Follow (Theo dõi)

#### 1. Theo dõi/Bỏ theo dõi user

**POST** `/api/users/{user_id}/follow`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Followed successfully",  // hoặc "Unfollowed successfully"
  "is_following": true  // hoặc false
}
```

**Lưu ý:** Đây là toggle - nếu đang follow thì sẽ unfollow, và ngược lại.

---

### Reports (Báo cáo)

#### 1. Báo cáo bài viết

**POST** `/api/posts/{post_id}/report`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "reason": "Nội dung không phù hợp"
}
```

**Response (200):**
```json
{
  "message": "Report submitted successfully"
}
```

---

#### 2. Báo cáo bình luận

**POST** `/api/comments/{comment_id}/report`

**Cần đăng nhập**

**Request Body:**
```json
{
  "reason": "Bình luận spam"
}
```

**Response (200):**
```json
{
  "message": "Report submitted successfully"
}
```

---

#### 3. Báo cáo user

**POST** `/api/users/{user_id}/report`

**Cần đăng nhập**

**Request Body:**
```json
{
  "reason": "Hành vi không phù hợp"
}
```

**Response (200):**
```json
{
  "message": "Report submitted successfully"
}
```

---

### Notifications (Thông báo)

#### 1. Lấy danh sách thông báo

**GET** `/api/realtime/notifications`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Số trang

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "type": "comment",  // comment, vote, follow, reply_comment
      "read_at": null,  // null = chưa đọc
      "created_at": "2024-01-01T00:00:00.000000Z",
      "sender": {
        "id": 2,
        "name": "Người dùng B",
        "avatar": "..."
      },
      "post": {
        "id": 1,
        "title": "Tiêu đề bài viết"
      },
      "comment": {
        "id": 1,
        "content": "Bình luận..."
      }
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

#### 2. Đánh dấu tất cả thông báo đã đọc

**POST** `/api/realtime/notifications/mark-all-read`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "All notifications marked as read"
}
```

---

### Chat & Messages

#### 1. Lấy danh sách cuộc trò chuyện

**GET** `/api/realtime/conversations`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "participant": {
        "id": 2,
        "name": "Người dùng B",
        "avatar": "..."
      },
      "last_message": {
        "id": 10,
        "content": "Tin nhắn cuối cùng",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "sender_id": 2
      },
      "unread_count": 3
    }
  ]
}
```

---

#### 2. Lấy tin nhắn với một user

**GET** `/api/realtime/messages/{receiver_id}`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Số trang

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "content": "Xin chào!",
      "sender_id": 1,
      "receiver_id": 2,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "read_at": null
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

#### 3. Gửi tin nhắn

**POST** `/api/realtime/sendmessage`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "receiver_id": 2,
  "content": "Nội dung tin nhắn"
}
```

**Response (200):**
```json
{
  "id": 1,
  "content": "Nội dung tin nhắn",
  "sender_id": 1,
  "receiver_id": 2,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "read_at": null
}
```

---

### Realtime

API hỗ trợ realtime thông qua **Pusher** hoặc **Laravel Echo**.

**Cấu hình cần thiết:**
- Cài đặt Laravel Echo và Pusher JS trong frontend
- Kết nối với Pusher server

**Các channel cần subscribe:**

1. **Notifications:** `private-notifications.{user_id}`
   - Event: `NotificationSent`

2. **Messages:** `private-messages.{user_id}`
   - Event: `MessageSent`

3. **Conversations:** `private-conversations.{user_id}`
   - Event: `ConversationChange`

**Ví dụ với Laravel Echo:**

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
  broadcaster: 'pusher',
  key: 'your-pusher-key',
  cluster: 'your-cluster',
  encrypted: true,
  authEndpoint: 'http://127.0.0.1:8000/broadcasting/auth',
  auth: {
    headers: {
      Authorization: `Bearer ${token}`
    }
  }
});

// Lắng nghe thông báo
echo.private(`notifications.${userId}`)
  .listen('NotificationSent', (e) => {
    console.log('New notification:', e);
  });

// Lắng nghe tin nhắn
echo.private(`messages.${userId}`)
  .listen('MessageSent', (e) => {
    console.log('New message:', e);
  });
```

---

### Image Upload

#### Upload ảnh (dùng cho trình soạn thảo)

**POST** `/api/image-upload`

**Cần đăng nhập**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
image: [file]
```

**Response (200):**
```json
{
  "url": "https://res.cloudinary.com/.../uploaded-image.jpg"
}
```

**Lưu ý:** URL trả về là từ Cloudinary, có thể dùng trực tiếp trong HTML.

---

### Advertisements (Quảng cáo)

#### Lấy danh sách quảng cáo (Public)

**GET** `/api/advertisements`

**Không cần đăng nhập**

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Quảng cáo",
      "link_url": "https://example.com",
      "image_url": "https://res.cloudinary.com/.../ad.jpg",
      "position": "sidebar_top",
      "status": "active",
      "display_order": 1
    }
  ]
}
```

---

### Settings

#### Lấy logo

**GET** `/api/settings/logo`

**Không cần đăng nhập**

**Response (200):**
```json
{
  "logo_url": "https://res.cloudinary.com/.../logo.png"
}
```

---

## Admin APIs

**Lưu ý:** Tất cả Admin APIs đều cần:
- Đăng nhập (token)
- Role = `admin` hoặc `superadmin`

### Categories Management

#### 1. Lấy danh sách chuyên mục (Admin)

**GET** `/api/admin/categories`

**Cần đăng nhập + Admin role**

**Query Parameters:**
- `page` (optional): Số trang

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Công nghệ",
      "slug": "cong-nghe",
      "description": "...",
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

#### 2. Tạo chuyên mục mới

**POST** `/api/admin/categories`

**Cần đăng nhập + Admin role**

**Request Body:**
```json
{
  "name": "Tên chuyên mục",
  "slug": "ten-chuyen-muc",  // optional, tự động tạo nếu không có
  "description": "Mô tả"  // optional
}
```

**Response (201):**
```json
{
  "id": 1,
  "name": "Tên chuyên mục",
  "slug": "ten-chuyen-muc",
  "description": "Mô tả",
  "created_at": "...",
  "updated_at": "..."
}
```

---

#### 3. Cập nhật chuyên mục

**PUT** `/api/admin/categories/{category_id}`

**Cần đăng nhập + Admin role**

**Request Body:**
```json
{
  "name": "Tên mới",
  "slug": "ten-moi",
  "description": "Mô tả mới"
}
```

**Response (200):** (giống như GET)

---

#### 4. Xóa chuyên mục

**DELETE** `/api/admin/categories/{category_id}`

**Cần đăng nhập + Admin role**

**Response (200):**
```json
{
  "message": "Đã xóa chuyên mục thành công."
}
```

---

### User Management

#### 1. Lấy danh sách user bị ban

**GET** `/api/admin/users/banned`

**Cần đăng nhập + Admin role**

**Query Parameters:**
- `limit` (optional): Số lượng mỗi trang
- `page` (optional): Số trang

**Response (200):**
```json
{
  "data": [
    {
      "id": 5,
      "name": "User bị ban",
      "email": "banned@example.com",
      "banned_until": "2025-01-01T00:00:00.000000Z"
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

#### 2. Ban user

**POST** `/api/admin/users/{user_id}/ban`

**Cần đăng nhập + Admin role**

**Request Body:**
```json
{
  "duration_days": 7  // optional, để trống = ban vĩnh viễn
}
```

**Response (200):**
```json
{
  "message": "Người dùng đã bị ban.",
  "banned_until": "2025-01-08T00:00:00.000000Z"
}
```

---

#### 3. Gỡ ban user

**POST** `/api/admin/users/{user_id}/unban`

**Cần đăng nhập + Admin role**

**Response (200):**
```json
{
  "message": "Người dùng đã được gỡ ban."
}
```

---

#### 4. Xem lịch sử kiểm duyệt của user

**GET** `/api/admin/users/{user_id}/moderation-history`

**Cần đăng nhập + Admin role**

**Response (200):**
```json
{
  "user_info": {
    "id": 5,
    "name": "User",
    "email": "user@example.com",
    "role": "user",
    "banned_until": null
  },
  "violations": {
    "removed_posts": [
      {
        "id": 10,
        "title": "Bài viết bị gỡ",
        "content_html": "...",
        "status": "removed_by_mod",
        "updated_at": "..."
      }
    ],
    "removed_comments": [
      {
        "id": 20,
        "content": "Bình luận bị gỡ",
        "status": "removed_by_mod",
        "updated_at": "..."
      }
    ],
    "active_user_reports": [
      {
        "id": 1,
        "reason": "Lý do báo cáo",
        "reporter": {
          "id": 2,
          "name": "Người báo cáo"
        },
        "created_at": "..."
      }
    ]
  }
}
```

---

### Advertisement Management

#### 1. Lấy danh sách quảng cáo (Admin)

**GET** `/api/admin/advertisements`

**Cần đăng nhập + Admin role**

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Quảng cáo",
      "link_url": "https://example.com",
      "image_url": "https://res.cloudinary.com/.../ad.jpg",
      "position": "sidebar_top",
      "status": "active",
      "display_order": 1,
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```

---

#### 2. Tạo quảng cáo mới

**POST** `/api/admin/advertisements`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
title: Tên quảng cáo
link_url: https://example.com
position: sidebar_top  // sidebar_top, sidebar_bottom, header, footer
status: active  // active hoặc inactive
display_order: 1
image_file: [file]  // required
```

**Response (201):**
```json
{
  "id": 1,
  "title": "Quảng cáo",
  "link_url": "https://example.com",
  "image_url": "https://res.cloudinary.com/.../ad.jpg",
  "position": "sidebar_top",
  "status": "active",
  "display_order": 1
}
```

---

#### 3. Cập nhật quảng cáo

**POST** `/api/admin/advertisements/{advertisement_id}`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
title: Tên mới
link_url: https://new-example.com
position: sidebar_bottom
status: inactive
display_order: 2
image_file: [file]  // optional, chỉ gửi nếu muốn đổi ảnh
```

**Response (200):** (giống như GET)

---

#### 4. Xóa quảng cáo

**DELETE** `/api/admin/advertisements/{advertisement_id}`

**Cần đăng nhập + Admin role**

**Response (204):** (No Content)

---

### Settings Management

#### Cập nhật logo

**POST** `/api/admin/settings/logo`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
logo: [file]
```

**Response (200):**
```json
{
  "message": "Logo updated successfully",
  "logo_url": "https://res.cloudinary.com/.../logo.png"
}
```

---

## Moderator APIs

**Lưu ý:** Tất cả Moderator APIs đều cần:
- Đăng nhập (token)
- Role = `moderator`, `admin`, hoặc `superadmin`

### Reports Management

#### 1. Lấy danh sách báo cáo bài viết

**GET** `/api/moderator/reports/posts`

**Cần đăng nhập + Moderator role**

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "reason": "Nội dung không phù hợp",
      "reporter": {
        "id": 2,
        "name": "Người báo cáo"
      },
      "post": {
        "id": 10,
        "title": "Bài viết bị báo cáo"
      },
      "created_at": "..."
    }
  ]
}
```

---

#### 2. Lấy danh sách báo cáo bình luận

**GET** `/api/moderator/reports/comments`

**Cần đăng nhập + Moderator role**

**Response (200):** (tương tự như posts)

---

#### 3. Lấy danh sách báo cáo user

**GET** `/api/moderator/reports/users`

**Cần đăng nhập + Moderator role**

**Response (200):** (tương tự như posts)

---

#### 4. Xử lý/Xóa báo cáo bài viết

**DELETE** `/api/moderator/reports/posts/{report_id}`

**Cần đăng nhập + Moderator role**

**Response (200):**
```json
{
  "message": "Report resolved"
}
```

---

#### 5. Xử lý/Xóa báo cáo bình luận

**DELETE** `/api/moderator/reports/comments/{report_id}`

**Cần đăng nhập + Moderator role**

**Response (200):** (tương tự)

---

#### 6. Xử lý/Xóa báo cáo user

**DELETE** `/api/moderator/reports/users/{report_id}`

**Cần đăng nhập + Moderator role**

**Response (200):** (tương tự)

---

### Content Management

#### 1. Lấy danh sách bài viết bị gỡ

**GET** `/api/moderator/content/removed-posts`

**Cần đăng nhập + Moderator role**

**Response (200):**
```json
{
  "data": [
    {
      "id": 10,
      "title": "Bài viết bị gỡ",
      "content_html": "...",
      "status": "removed_by_mod",
      "user": {
        "id": 5,
        "name": "Tác giả"
      },
      "updated_at": "..."
    }
  ]
}
```

---

#### 2. Lấy danh sách bình luận bị gỡ

**GET** `/api/moderator/content/removed-comments`

**Cần đăng nhập + Moderator role**

**Response (200):** (tương tự)

---

#### 3. Khôi phục bài viết

**POST** `/api/moderator/posts/{post_id}/restore`

**Cần đăng nhập + Moderator role**

**Response (200):**
```json
{
  "message": "Post restored successfully"
}
```

---

#### 4. Khôi phục bình luận

**POST** `/api/moderator/comments/{comment_id}/restore`

**Cần đăng nhập + Moderator role**

**Response (200):**
```json
{
  "message": "Comment restored successfully"
}
```

---

## Superadmin APIs

**Lưu ý:** Chỉ user có role = `superadmin` mới có quyền.

### User Role Management

#### Cập nhật role của user

**PATCH** `/api/superadmin/users/{user_id}/role`

**Cần đăng nhập + Superadmin role**

**Request Body:**
```json
{
  "role": "moderator"  // user, moderator, admin, superadmin
}
```

**Response (200):**
```json
{
  "message": "Role updated successfully",
  "user": {
    "id": 5,
    "name": "User",
    "role": "moderator"
  }
}
```

---

## Lưu ý quan trọng

### 1. Phân trang

Hầu hết các API trả về danh sách đều có phân trang. Cấu trúc response:

```json
{
  "data": [ ... ],
  "links": {
    "first": "http://...?page=1",
    "last": "http://...?page=10",
    "prev": null,
    "next": "http://...?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 10,
    "to": 10,
    "total": 100
  }
}
```

### 2. Xử lý lỗi

Luôn kiểm tra status code và message trong response:

```javascript
try {
  const response = await axios.get('/api/posts');
  // Xử lý dữ liệu
} catch (error) {
  if (error.response) {
    // Server trả về lỗi
    console.log(error.response.status); // 400, 401, 404, 422, 500
    console.log(error.response.data.message); // Thông báo lỗi
    console.log(error.response.data.errors); // Chi tiết lỗi validation
  } else if (error.request) {
    // Request đã gửi nhưng không nhận được response
    console.log('Network error');
  }
}
```

### 3. Upload file

Khi upload file, phải dùng `multipart/form-data`:

```javascript
const formData = new FormData();
formData.append('avatar', fileInput.files[0]);

await axios.post('/api/user/avatar', formData, {
  headers: {
    'Content-Type': 'multipart/form-data',
    'Authorization': `Bearer ${token}`
  }
});
```

### 4. Date Format

Tất cả các date/time đều trả về dưới dạng ISO 8601:
```
2024-01-01T00:00:00.000000Z
```

Có thể parse bằng:
```javascript
const date = new Date(response.data.created_at);
```

### 5. Image URLs

Tất cả image URLs đều từ Cloudinary và đã được tối ưu tự động. Có thể dùng trực tiếp trong `<img>` tag.

---

## Ví dụ code hoàn chỉnh

### Setup Axios

```javascript
import axios from 'axios';

// Tạo instance axios
const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Thêm token vào mọi request
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Xử lý lỗi 401 (token hết hạn)
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Xóa token và redirect về trang login
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default api;
```

### Đăng nhập

```javascript
import api from './api';

async function login(email, password) {
  try {
    const response = await api.post('/login', {
      email,
      password
    });
    
    // Lưu token
    localStorage.setItem('token', response.data.token);
    
    // Lưu thông tin user
    localStorage.setItem('user', JSON.stringify(response.data.user));
    
    return response.data;
  } catch (error) {
    if (error.response?.status === 422) {
      throw new Error(error.response.data.message);
    }
    throw error;
  }
}
```

### Lấy danh sách bài viết

```javascript
import api from './api';

async function getPosts(page = 1, sort = 'newest', categoryId = null) {
  try {
    const params = {
      page,
      sort,
      limit: 10
    };
    
    if (categoryId) {
      params.category = categoryId;
    }
    
    const response = await api.get('/posts', { params });
    return response.data;
  } catch (error) {
    console.error('Error fetching posts:', error);
    throw error;
  }
}
```

### Tạo bài viết

```javascript
import api from './api';

async function createPost(title, contentHtml, categoryId, thumbnailUrl = null) {
  try {
    const response = await api.post('/posts', {
      title,
      content_html: contentHtml,
      category_id: categoryId,
      thumbnail_url: thumbnailUrl
    });
    
    return response.data;
  } catch (error) {
    if (error.response?.status === 422) {
      // Hiển thị lỗi validation
      const errors = error.response.data.errors;
      console.log('Validation errors:', errors);
    }
    throw error;
  }
}
```

---

## Hỗ trợ

Nếu có thắc mắc hoặc gặp vấn đề, vui lòng liên hệ với team backend.

**Chúc bạn code vui vẻ! 🚀**



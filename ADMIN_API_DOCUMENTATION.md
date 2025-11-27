# Tài liệu API Quản trị - Moderator, Admin & Superadmin

## 📋 Mục lục
1. [Giới thiệu](#giới-thiệu)
2. [Phân quyền và Vai trò](#phân-quyền-và-vai-trò)
3. [Xác thực cho Admin Panel](#xác-thực-cho-admin-panel)
4. [Moderator APIs](#moderator-apis)
5. [Admin APIs](#admin-apis)
6. [Superadmin APIs](#superadmin-apis)
7. [Ví dụ code hoàn chỉnh](#ví-dụ-code-hoàn-chỉnh)

---

## Giới thiệu

Tài liệu này dành riêng cho các API quản trị của hệ thống Social Media, bao gồm:
- **Moderator**: Quản lý báo cáo, kiểm duyệt nội dung
- **Admin**: Quản lý chuyên mục, quảng cáo, người dùng, cài đặt
- **Superadmin**: Quản lý vai trò người dùng

**Base URL:** `http://127.0.0.1:8000/api`

**Lưu ý:** Tất cả các API trong tài liệu này đều yêu cầu:
- Đăng nhập (có token hợp lệ)
- Vai trò phù hợp (moderator/admin/superadmin)

---

## Phân quyền và Vai trò

### Cấp độ quyền

Hệ thống có 4 cấp độ quyền (từ thấp đến cao):

1. **user** - Người dùng thông thường
2. **moderator** - Kiểm duyệt viên
3. **admin** - Quản trị viên
4. **superadmin** - Siêu quản trị viên

### Quy tắc kế thừa quyền

- **moderator** có thể làm mọi thứ của **user**
- **admin** có thể làm mọi thứ của **moderator** + **user**
- **superadmin** có thể làm mọi thứ của **admin** + **moderator** + **user**

### Bảng quyền truy cập API

| API Group | User | Moderator | Admin | Superadmin |
|-----------|------|-----------|-------|------------|
| Moderator APIs | ❌ | ✅ | ✅ | ✅ |
| Admin APIs | ❌ | ❌ | ✅ | ✅ |
| Superadmin APIs | ❌ | ❌ | ❌ | ✅ |

---

## Xác thực cho Admin Panel

### Đăng nhập Admin/Moderator

**POST** `/api/admin/login`

**Không cần đăng nhập** (đây là endpoint đăng nhập)

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
    "avatar": "https://res.cloudinary.com/.../avatar.jpg",
    "cover_photo_url": null,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "role": "admin"  // ← Quan trọng: field này chỉ có khi đăng nhập qua /api/admin/login
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
}
```

**Lỗi (422) - Không có quyền:**
```json
{
  "message": "Tài khoản này không có quyền truy cập quản trị.",
  "errors": {
    "email": ["Tài khoản này không có quyền truy cập quản trị."]
  }
}
```

**Lỗi (422) - Thông tin đăng nhập sai:**
```json
{
  "message": "These credentials do not match our records.",
  "errors": {
    "email": ["These credentials do not match our records."]
  }
}
```

**Lưu ý quan trọng:**
- Endpoint này chỉ chấp nhận user có role: `moderator`, `admin`, hoặc `superadmin`
- User thông thường (role = `user`) sẽ bị từ chối
- Token nhận được có thể dùng cho tất cả các API quản trị

---

## Moderator APIs

**Yêu cầu:** Role = `moderator`, `admin`, hoặc `superadmin`

### 1. Quản lý Báo cáo (Reports)

#### 1.1. Lấy danh sách báo cáo bài viết

**GET** `/api/moderator/reports/posts`

**Cần đăng nhập + Moderator role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Số trang (mặc định: 1)

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "reason": "Nội dung không phù hợp",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "reporter": {
        "id": 5,
        "name": "Người báo cáo",
        "avatar": "https://res.cloudinary.com/.../avatar.jpg"
      },
      "post": {
        "id": 10,
        "title": "Bài viết bị báo cáo",
        "content_html": "<p>Nội dung bài viết...</p>",
        "status": "published",
        "author": {
          "id": 3,
          "name": "Tác giả bài viết",
          "avatar": "..."
        },
        "created_at": "2024-01-01T00:00:00.000000Z"
      }
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/moderator/reports/posts?page=1",
    "last": "http://127.0.0.1:8000/api/moderator/reports/posts?page=5",
    "prev": null,
    "next": "http://127.0.0.1:8000/api/moderator/reports/posts?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 20,
    "to": 20,
    "total": 100
  }
}
```

**Lỗi (401):**
```json
{
  "message": "Unauthenticated."
}
```

**Lỗi (403):**
```json
{
  "message": "Bạn không có quyền thực hiện hành động này."
}
```

---

#### 1.2. Lấy danh sách báo cáo bình luận

**GET** `/api/moderator/reports/comments`

**Cần đăng nhập + Moderator role**

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
      "id": 2,
      "reason": "Bình luận spam",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "reporter": {
        "id": 6,
        "name": "Người báo cáo",
        "avatar": "..."
      },
      "comment": {
        "id": 15,
        "content": "Bình luận bị báo cáo",
        "post_id": 10,
        "parent_id": null,
        "status": "published",
        "user": {
          "id": 4,
          "name": "Tác giả bình luận",
          "avatar": "..."
        },
        "created_at": "2024-01-01T00:00:00.000000Z"
      }
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

#### 1.3. Lấy danh sách báo cáo người dùng

**GET** `/api/moderator/reports/users`

**Cần đăng nhập + Moderator role**

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
      "id": 3,
      "reason": "Hành vi không phù hợp",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "reporter": {
        "id": 7,
        "name": "Người báo cáo",
        "avatar": "..."
      },
      "reported_user": {
        "id": 8,
        "name": "Người bị báo cáo",
        "avatar": "...",
        "role": "user",
        "banned_until": null
      }
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

#### 1.4. Xử lý/Xóa báo cáo bài viết

**DELETE** `/api/moderator/reports/posts/{report_id}`

**Cần đăng nhập + Moderator role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Giải thích:**
- Sau khi moderator đã xem và xử lý báo cáo (ví dụ: gỡ bài viết vi phạm), họ sẽ xóa báo cáo này
- Xóa báo cáo không có nghĩa là khôi phục bài viết, chỉ là đánh dấu báo cáo đã được xử lý

**Response (200):**
```json
{
  "message": "Báo cáo đã được giải quyết."
}
```

**Lỗi (404):**
```json
{
  "message": "No query results for model [App\\Models\\Report_post] {report_id}"
}
```

---

#### 1.5. Xử lý/Xóa báo cáo bình luận

**DELETE** `/api/moderator/reports/comments/{report_id}`

**Cần đăng nhập + Moderator role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Báo cáo đã được giải quyết."
}
```

---

#### 1.6. Xử lý/Xóa báo cáo người dùng

**DELETE** `/api/moderator/reports/users/{report_id}`

**Cần đăng nhập + Moderator role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Báo cáo đã được giải quyết."
}
```

---

### 2. Quản lý Nội dung đã bị gỡ

#### 2.1. Lấy danh sách bài viết đã bị gỡ

**GET** `/api/moderator/content/removed-posts`

**Cần đăng nhập + Moderator role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Số trang (mặc định: 1)

**Response (200):**
```json
{
  "data": [
    {
      "id": 20,
      "title": "Bài viết đã bị gỡ",
      "thumbnail_url": "https://res.cloudinary.com/.../image.jpg",
      "content_html": "<p>Nội dung bài viết...</p>",
      "status": "removed_by_mod",  // ← Trạng thái đặc biệt
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z",  // ← Thời điểm bị gỡ
      "category": {
        "id": 1,
        "name": "Công nghệ",
        "slug": "cong-nghe"
      },
      "author": {
        "id": 5,
        "name": "Tác giả",
        "avatar": "..."
      },
      "comments_count": 10,
      "vote_score": -5
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

**Lưu ý:**
- Chỉ hiển thị các bài viết có `status = "removed_by_mod"`
- Sắp xếp theo `updated_at` giảm dần (bài viết mới bị gỡ nhất lên đầu)
- Mỗi trang có 29 bài viết

---

#### 2.2. Lấy danh sách bình luận đã bị gỡ

**GET** `/api/moderator/content/removed-comments`

**Cần đăng nhập + Moderator role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Số trang (mặc định: 1)

**Response (200):**
```json
{
  "data": [
    {
      "id": 30,
      "content": "Bình luận đã bị gỡ",
      "post_id": 10,
      "parent_id": null,
      "status": "removed_by_mod",  // ← Trạng thái đặc biệt
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-15T11:00:00.000000Z",  // ← Thời điểm bị gỡ
      "user": {
        "id": 6,
        "name": "Tác giả bình luận",
        "avatar": "..."
      },
      "post": {
        "id": 10,
        "title": "Bài viết liên quan"
      },
      "replies_count": 0
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

**Lưu ý:**
- Chỉ hiển thị các bình luận có `status = "removed_by_mod"`
- Sắp xếp theo `updated_at` giảm dần
- Mỗi trang có 20 bình luận

---

#### 2.3. Khôi phục bài viết

**POST** `/api/moderator/posts/{post_id}/restore`

**Cần đăng nhập + Moderator role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Giải thích:**
- Khôi phục bài viết đã bị gỡ về trạng thái `published`
- Chỉ có thể khôi phục bài viết có `status = "removed_by_mod"`

**Response (200):**
```json
{
  "message": "Bài viết này đã được khôi phục",
  "post": {
    "id": 20,
    "title": "Bài viết đã được khôi phục",
    "status": "published",  // ← Đã đổi về published
    "content_html": "...",
    "author": { ... },
    "category": { ... },
    "comments_count": 10,
    "vote_score": -5
  }
}
```

**Lỗi (422) - Bài viết đang hiển thị bình thường:**
```json
{
  "messange": "Bài viết này đang hiển thị bình thường."
}
```

**Lưu ý:** Có typo trong message: "messange" thay vì "message" (đây là lỗi từ backend, frontend cần xử lý)

---

#### 2.4. Khôi phục bình luận

**POST** `/api/moderator/comments/{comment_id}/restore`

**Cần đăng nhập + Moderator role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Giải thích:**
- Khôi phục bình luận đã bị gỡ về trạng thái `published`
- Chỉ có thể khôi phục bình luận có `status = "removed_by_mod"`

**Response (200):**
```json
{
  "message": "Bình luận đã được khôi phục thành công",
  "comment": {
    "id": 30,
    "content": "Bình luận đã được khôi phục",
    "status": "published",  // ← Đã đổi về published
    "user": { ... },
    "post": { ... },
    "replies_count": 0
  }
}
```

**Lỗi (422) - Bình luận đang hiển thị bình thường:**
```json
{
  "message": "Bình luận này đang được hiển thị bình thường"
}
```

---

## Admin APIs

**Yêu cầu:** Role = `admin` hoặc `superadmin`

### 1. Quản lý Chuyên mục (Categories)

#### 1.1. Lấy danh sách chuyên mục (Admin)

**GET** `/api/admin/categories`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Số trang (mặc định: 1)

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Công nghệ",
      "slug": "cong-nghe",
      "description": "Các bài viết về công nghệ thông tin",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Giáo dục",
      "slug": "giao-duc",
      "description": "Các bài viết về giáo dục",
      "created_at": "2024-01-02T00:00:00.000000Z",
      "updated_at": "2024-01-02T00:00:00.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/admin/categories?page=1",
    "last": "http://127.0.0.1:8000/api/admin/categories?page=3",
    "prev": null,
    "next": "http://127.0.0.1:8000/api/admin/categories?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "per_page": 20,
    "to": 20,
    "total": 50
  }
}
```

**Lưu ý:**
- Sắp xếp theo `created_at` giảm dần (mới nhất lên đầu)
- Mỗi trang có 20 chuyên mục

---

#### 1.2. Lấy chi tiết một chuyên mục

**GET** `/api/admin/categories/{category_id}`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "name": "Công nghệ",
  "slug": "cong-nghe",
  "description": "Các bài viết về công nghệ thông tin",
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

**Lỗi (404):**
```json
{
  "message": "No query results for model [App\\Models\\Category] {category_id}"
}
```

---

#### 1.3. Tạo chuyên mục mới

**POST** `/api/admin/categories`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Thể thao",
  "slug": "the-thao",  // optional - nếu không có sẽ tự động tạo từ name
  "description": "Các bài viết về thể thao"  // optional
}
```

**Response (201):**
```json
{
  "id": 3,
  "name": "Thể thao",
  "slug": "the-thao",
  "description": "Các bài viết về thể thao",
  "created_at": "2024-01-15T00:00:00.000000Z",
  "updated_at": "2024-01-15T00:00:00.000000Z"
}
```

**Lỗi (422) - Tên đã tồn tại:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name has already been taken."]
  }
}
```

**Lỗi (422) - Slug đã tồn tại:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "slug": ["The slug has already been taken."]
  }
}
```

**Lưu ý:**
- `name` là bắt buộc, tối đa 100 ký tự
- `slug` là tùy chọn, tối đa 150 ký tự. Nếu không gửi, hệ thống sẽ tự động tạo từ `name` (chuyển thành chữ thường, thay khoảng trắng bằng dấu gạch ngang)
- `description` là tùy chọn, tối đa 255 ký tự

---

#### 1.4. Cập nhật chuyên mục

**PUT** `/api/admin/categories/{category_id}`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Công nghệ thông tin",  // required
  "slug": "cong-nghe-thong-tin",  // optional
  "description": "Mô tả mới"  // optional
}
```

**Response (200):**
```json
{
  "id": 1,
  "name": "Công nghệ thông tin",
  "slug": "cong-nghe-thong-tin",
  "description": "Mô tả mới",
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-15T10:00:00.000000Z"
}
```

**Lưu ý:**
- Khi update, `name` và `slug` phải unique (không trùng với chuyên mục khác)
- Nếu gửi `slug` rỗng và `name` thay đổi, hệ thống sẽ tự động tạo slug mới từ `name`
- Nếu `name` không đổi và `slug` rỗng, slug sẽ giữ nguyên

---

#### 1.5. Xóa chuyên mục

**DELETE** `/api/admin/categories/{category_id}`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Đã xóa chuyên mục thành công."
}
```

**Lưu ý quan trọng:**
- Khi xóa chuyên mục, tất cả bài viết thuộc chuyên mục đó sẽ có `category_id = null`
- Bài viết không bị xóa, chỉ mất liên kết với chuyên mục

---

### 2. Quản lý Người dùng (User Management)

#### 2.1. Lấy danh sách người dùng bị ban

**GET** `/api/admin/users/banned`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `limit` (optional): Số lượng mỗi trang (mặc định: 20)
- `page` (optional): Số trang (mặc định: 1)

**Response (200):**
```json
{
  "data": [
    {
      "id": 10,
      "name": "User bị ban",
      "email": "banned@example.com",
      "avatar": "https://res.cloudinary.com/.../avatar.jpg",
      "cover_photo_url": null,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "banned_until": "2025-01-15T00:00:00.000000Z"  // ← Thời điểm hết ban
    },
    {
      "id": 11,
      "name": "User bị ban vĩnh viễn",
      "email": "banned2@example.com",
      "avatar": "...",
      "banned_until": "2100-01-01T00:00:00.000000Z"  // ← Ban vĩnh viễn
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

**Lưu ý:**
- Chỉ hiển thị các user có `banned_until` không null và trong tương lai
- Sắp xếp theo `banned_until` tăng dần (người sắp hết ban lên đầu)
- User đã hết ban (banned_until < hiện tại) sẽ không hiển thị

---

#### 2.2. Ban người dùng

**POST** `/api/admin/users/{user_id}/ban`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "duration_days": 7  // optional - số ngày ban (1-36500). Để trống = ban vĩnh viễn
}
```

**Response (200):**
```json
{
  "message": "Người dùng đã bị ban.",
  "banned_until": "2025-01-22T00:00:00.000000Z"  // ← Thời điểm hết ban
}
```

**Lỗi (403) - Không thể ban Superadmin:**
```json
{
  "message": "Không thể ban Super Admin."
}
```

**Lưu ý quan trọng:**
- Khi ban user, tất cả token của user đó sẽ bị xóa ngay lập tức (user bị đăng xuất)
- `duration_days` tối thiểu: 1, tối đa: 36500 (khoảng 100 năm)
- Nếu không gửi `duration_days` hoặc gửi `null`, user sẽ bị ban vĩnh viễn (36500 ngày)
- Không thể ban user có role = `superadmin`

---

#### 2.3. Gỡ ban người dùng

**POST** `/api/admin/users/{user_id}/unban`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Người dùng đã được gỡ ban."
}
```

**Lưu ý:**
- Gỡ ban sẽ set `banned_until = null`
- User có thể đăng nhập lại ngay sau khi được gỡ ban

---

#### 2.4. Xem lịch sử kiểm duyệt của người dùng

**GET** `/api/admin/users/{user_id}/moderation-history`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "user_info": {
    "id": 10,
    "name": "User có lịch sử vi phạm",
    "email": "user@example.com",
    "role": "user",
    "banned_until": "2025-01-15T00:00:00.000000Z"
  },
  "violations": {
    "removed_posts": [
      {
        "id": 20,
        "title": "Bài viết đã bị gỡ",
        "thumbnail_url": "...",
        "content_html": "<p>Nội dung vi phạm...</p>",
        "status": "removed_by_mod",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-10T10:00:00.000000Z",  // ← Thời điểm bị gỡ
        "author": { ... },
        "category": { ... },
        "comments_count": 5,
        "vote_score": -10
      }
    ],
    "removed_comments": [
      {
        "id": 30,
        "content": "Bình luận đã bị gỡ",
        "post_id": 20,
        "parent_id": null,
        "status": "removed_by_mod",
        "created_at": "2024-01-05T00:00:00.000000Z",
        "updated_at": "2024-01-12T14:00:00.000000Z",  // ← Thời điểm bị gỡ
        "user": { ... },
        "replies_count": 0
      }
    ],
    "active_user_reports": [
      {
        "id": 5,
        "reason": "Hành vi không phù hợp",
        "created_at": "2024-01-15T00:00:00.000000Z",
        "reporter": {
          "id": 8,
          "name": "Người báo cáo"
        }
      }
    ]
  }
}
```

**Giải thích:**
- `removed_posts`: Danh sách bài viết của user đã bị moderator gỡ
- `removed_comments`: Danh sách bình luận của user đã bị moderator gỡ
- `active_user_reports`: Danh sách báo cáo đang hoạt động nhắm vào user này (chưa được xử lý)

**Lưu ý:**
- Đây là công cụ để admin đánh giá hành vi của user trước khi quyết định ban
- Sắp xếp theo `updated_at` giảm dần (vi phạm mới nhất lên đầu)

---

### 3. Quản lý Quảng cáo (Advertisements)

#### 3.1. Lấy danh sách quảng cáo (Admin)

**GET** `/api/admin/advertisements`

**Cần đăng nhập + Admin role**

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
      "title": "Quảng cáo sản phẩm A",
      "link_url": "https://example.com/product-a",
      "image_url": "https://res.cloudinary.com/.../advertisement.jpg",
      "position": "sidebar_top",
      "status": "active",
      "display_order": 1,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    {
      "id": 2,
      "title": "Quảng cáo sản phẩm B",
      "link_url": "https://example.com/product-b",
      "image_url": "https://res.cloudinary.com/.../advertisement2.jpg",
      "position": "sidebar_bottom",
      "status": "inactive",
      "display_order": 2,
      "created_at": "2024-01-02T00:00:00.000000Z",
      "updated_at": "2024-01-02T00:00:00.000000Z"
    }
  ]
}
```

**Lưu ý:**
- Admin có thể xem tất cả quảng cáo (kể cả `inactive`)
- Sắp xếp theo `position` trước, sau đó theo `display_order`
- Không có phân trang (trả về tất cả)

---

#### 3.2. Tạo quảng cáo mới

**POST** `/api/admin/advertisements`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
title: Tên quảng cáo (required)
link_url: https://example.com (required, phải là URL hợp lệ, max 500 ký tự)
position: sidebar_top (required, các giá trị: sidebar_top, sidebar_bottom, header, footer)
status: active (required, các giá trị: active, inactive)
display_order: 1 (optional, số nguyên)
image_file: [file] (required, file ảnh, tối đa 2MB)
```

**Response (201):**
```json
{
  "id": 3,
  "title": "Tên quảng cáo",
  "link_url": "https://example.com",
  "image_url": "https://res.cloudinary.com/.../advertisement3.jpg",
  "position": "sidebar_top",
  "status": "active",
  "display_order": 1,
  "created_at": "2024-01-15T00:00:00.000000Z",
  "updated_at": "2024-01-15T00:00:00.000000Z"
}
```

**Lỗi (422) - Validation:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "link_url": ["The link url must be a valid URL."],
    "position": ["The selected position is invalid."],
    "status": ["The selected status must be one of: active, inactive."],
    "image_file": ["The image file must be an image.", "The image file must not be greater than 2048 kilobytes."]
  }
}
```

**Lỗi (500) - Upload thất bại:**
```json
{
  "message": "Upload thất bại: [chi tiết lỗi]"
}
```

**Lưu ý:**
- Ảnh sẽ được upload lên Cloudinary và tự động tối ưu
- `display_order` mặc định là 0 nếu không gửi
- Các giá trị `position` hợp lệ: `sidebar_top`, `sidebar_bottom`, `header`, `footer`

---

#### 3.3. Cập nhật quảng cáo

**POST** `/api/admin/advertisements/{advertisement_id}`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
title: Tên mới (optional - chỉ gửi nếu muốn đổi)
link_url: https://new-example.com (optional)
position: sidebar_bottom (optional)
status: inactive (optional)
display_order: 2 (optional)
image_file: [file] (optional - chỉ gửi nếu muốn đổi ảnh)
```

**Response (200):**
```json
{
  "id": 1,
  "title": "Tên mới",
  "link_url": "https://new-example.com",
  "image_url": "https://res.cloudinary.com/.../new-advertisement.jpg",
  "position": "sidebar_bottom",
  "status": "inactive",
  "display_order": 2,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-15T10:00:00.000000Z"
}
```

**Lưu ý:**
- Tất cả các field đều optional (chỉ gửi field muốn cập nhật)
- Nếu không gửi `image_file`, ảnh cũ sẽ giữ nguyên
- Nếu gửi `image_file`, ảnh mới sẽ thay thế ảnh cũ

---

#### 3.4. Xóa quảng cáo

**DELETE** `/api/admin/advertisements/{advertisement_id}`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
```

**Response (204):**
```
(No Content - không có body)
```

**Lưu ý:**
- Xóa vĩnh viễn (hard delete) - không thể khôi phục
- Ảnh trên Cloudinary vẫn còn (không tự động xóa)

---

### 4. Quản lý Cài đặt (Settings)

#### 4.1. Cập nhật logo website

**POST** `/api/admin/settings/logo`

**Cần đăng nhập + Admin role**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
logo: [file] (required, file ảnh: jpeg, png, jpg, gif, svg, tối đa 2MB)
```

**Response (200):**
```json
{
  "message": "Logo đã được cập nhật thành công.",
  "logo_url": "https://res.cloudinary.com/.../site_assets/logo.png"
}
```

**Lỗi (422) - Validation:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "logo": [
      "The logo field is required.",
      "The logo must be an image.",
      "The logo must not be greater than 2048 kilobytes."
    ]
  }
}
```

**Lỗi (500) - Upload thất bại:**
```json
{
  "message": "Upload thất bại: [chi tiết lỗi]"
}
```

**Lưu ý:**
- Logo được lưu trong thư mục `site_assets` trên Cloudinary
- Chất lượng ảnh được giữ nguyên (không tối ưu như quảng cáo)
- Nếu đã có logo, sẽ được cập nhật (không tạo mới)

---

## Superadmin APIs

**Yêu cầu:** Role = `superadmin` (chỉ có superadmin)

### 1. Quản lý Vai trò Người dùng

#### 1.1. Cập nhật vai trò của người dùng

**PATCH** `/api/superadmin/users/{user_id}/role`

**Cần đăng nhập + Superadmin role**

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "role": "moderator"  // required: user, moderator, admin, superadmin
}
```

**Response (200):**
```json
{
  "message": "Cập nhật vai trò thành công.",
  "user": {
    "id": 5,
    "name": "User Name",
    "email": "user@example.com",
    "role": "moderator",  // ← Đã được cập nhật
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-15T10:00:00.000000Z"
  }
}
```

**Lỗi (422) - Role không hợp lệ:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "role": ["The selected role is invalid."]
  }
}
```

**Lỗi (403) - Không thể thay đổi role của superadmin khác:**
```json
{
  "message": "Không thể thay đổi vai trò của Superadmin khác."
}
```

**Lưu ý quan trọng:**
- Chỉ superadmin mới có quyền thay đổi role
- Không thể thay đổi role của superadmin khác (chỉ có thể thay đổi role của chính mình)
- Các giá trị role hợp lệ: `user`, `moderator`, `admin`, `superadmin`
- Khi thay đổi role, user sẽ giữ nguyên tất cả dữ liệu (bài viết, bình luận, v.v.)

---

## Ví dụ code hoàn chỉnh

### Setup Axios cho Admin Panel

```javascript
import axios from 'axios';

// Tạo instance axios cho admin
const adminApi = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Thêm token vào mọi request
adminApi.interceptors.request.use((config) => {
  const token = localStorage.getItem('admin_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Xử lý lỗi 401 (token hết hạn hoặc không có quyền)
adminApi.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('admin_token');
      window.location.href = '/admin/login';
    } else if (error.response?.status === 403) {
      alert('Bạn không có quyền thực hiện hành động này');
    }
    return Promise.reject(error);
  }
);

export default adminApi;
```

---

### Đăng nhập Admin

```javascript
import adminApi from './adminApi';

async function adminLogin(email, password) {
  try {
    const response = await adminApi.post('/admin/login', {
      email,
      password
    });
    
    // Lưu token
    localStorage.setItem('admin_token', response.data.token);
    
    // Lưu thông tin user (bao gồm role)
    localStorage.setItem('admin_user', JSON.stringify(response.data.user));
    
    return response.data;
  } catch (error) {
    if (error.response?.status === 422) {
      throw new Error(error.response.data.message || 'Đăng nhập thất bại');
    }
    throw error;
  }
}
```

---

### Moderator: Lấy danh sách báo cáo

```javascript
import adminApi from './adminApi';

// Lấy báo cáo bài viết
async function getPostReports(page = 1) {
  try {
    const response = await adminApi.get('/moderator/reports/posts', {
      params: { page }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching post reports:', error);
    throw error;
  }
}

// Lấy báo cáo bình luận
async function getCommentReports(page = 1) {
  try {
    const response = await adminApi.get('/moderator/reports/comments', {
      params: { page }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching comment reports:', error);
    throw error;
  }
}

// Lấy báo cáo user
async function getUserReports(page = 1) {
  try {
    const response = await adminApi.get('/moderator/reports/users', {
      params: { page }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching user reports:', error);
    throw error;
  }
}
```

---

### Moderator: Xử lý báo cáo

```javascript
import adminApi from './adminApi';

// Xử lý báo cáo bài viết
async function resolvePostReport(reportId) {
  try {
    const response = await adminApi.delete(`/moderator/reports/posts/${reportId}`);
    return response.data;
  } catch (error) {
    console.error('Error resolving post report:', error);
    throw error;
  }
}

// Xử lý báo cáo bình luận
async function resolveCommentReport(reportId) {
  try {
    const response = await adminApi.delete(`/moderator/reports/comments/${reportId}`);
    return response.data;
  } catch (error) {
    console.error('Error resolving comment report:', error);
    throw error;
  }
}

// Xử lý báo cáo user
async function resolveUserReport(reportId) {
  try {
    const response = await adminApi.delete(`/moderator/reports/users/${reportId}`);
    return response.data;
  } catch (error) {
    console.error('Error resolving user report:', error);
    throw error;
  }
}
```

---

### Moderator: Khôi phục nội dung

```javascript
import adminApi from './adminApi';

// Khôi phục bài viết
async function restorePost(postId) {
  try {
    const response = await adminApi.post(`/moderator/posts/${postId}/restore`);
    return response.data;
  } catch (error) {
    if (error.response?.status === 422) {
      // Xử lý typo trong message: "messange" thay vì "message"
      const message = error.response.data.messange || error.response.data.message;
      throw new Error(message);
    }
    throw error;
  }
}

// Khôi phục bình luận
async function restoreComment(commentId) {
  try {
    const response = await adminApi.post(`/moderator/comments/${commentId}/restore`);
    return response.data;
  } catch (error) {
    if (error.response?.status === 422) {
      throw new Error(error.response.data.message);
    }
    throw error;
  }
}
```

---

### Admin: Quản lý chuyên mục

```javascript
import adminApi from './adminApi';

// Lấy danh sách chuyên mục
async function getCategories(page = 1) {
  try {
    const response = await adminApi.get('/admin/categories', {
      params: { page }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
    throw error;
  }
}

// Tạo chuyên mục mới
async function createCategory(name, slug = null, description = null) {
  try {
    const data = { name };
    if (slug) data.slug = slug;
    if (description) data.description = description;
    
    const response = await adminApi.post('/admin/categories', data);
    return response.data;
  } catch (error) {
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      console.error('Validation errors:', errors);
    }
    throw error;
  }
}

// Cập nhật chuyên mục
async function updateCategory(categoryId, name, slug = null, description = null) {
  try {
    const data = { name };
    if (slug) data.slug = slug;
    if (description !== null) data.description = description;
    
    const response = await adminApi.put(`/admin/categories/${categoryId}`, data);
    return response.data;
  } catch (error) {
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      console.error('Validation errors:', errors);
    }
    throw error;
  }
}

// Xóa chuyên mục
async function deleteCategory(categoryId) {
  try {
    const response = await adminApi.delete(`/admin/categories/${categoryId}`);
    return response.data;
  } catch (error) {
    console.error('Error deleting category:', error);
    throw error;
  }
}
```

---

### Admin: Quản lý người dùng

```javascript
import adminApi from './adminApi';

// Lấy danh sách user bị ban
async function getBannedUsers(page = 1, limit = 20) {
  try {
    const response = await adminApi.get('/admin/users/banned', {
      params: { page, limit }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching banned users:', error);
    throw error;
  }
}

// Ban user
async function banUser(userId, durationDays = null) {
  try {
    const data = {};
    if (durationDays) {
      data.duration_days = durationDays;
    }
    
    const response = await adminApi.post(`/admin/users/${userId}/ban`, data);
    return response.data;
  } catch (error) {
    if (error.response?.status === 403) {
      throw new Error('Không thể ban Super Admin');
    }
    throw error;
  }
}

// Gỡ ban user
async function unbanUser(userId) {
  try {
    const response = await adminApi.post(`/admin/users/${userId}/unban`);
    return response.data;
  } catch (error) {
    console.error('Error unbanning user:', error);
    throw error;
  }
}

// Xem lịch sử kiểm duyệt
async function getModerationHistory(userId) {
  try {
    const response = await adminApi.get(`/admin/users/${userId}/moderation-history`);
    return response.data;
  } catch (error) {
    console.error('Error fetching moderation history:', error);
    throw error;
  }
}
```

---

### Admin: Quản lý quảng cáo

```javascript
import adminApi from './adminApi';

// Lấy danh sách quảng cáo
async function getAdvertisements() {
  try {
    const response = await adminApi.get('/admin/advertisements');
    return response.data;
  } catch (error) {
    console.error('Error fetching advertisements:', error);
    throw error;
  }
}

// Tạo quảng cáo mới
async function createAdvertisement(formData) {
  try {
    const response = await adminApi.post('/admin/advertisements', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    return response.data;
  } catch (error) {
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      console.error('Validation errors:', errors);
    } else if (error.response?.status === 500) {
      throw new Error(error.response.data.message);
    }
    throw error;
  }
}

// Ví dụ sử dụng createAdvertisement
function handleCreateAd() {
  const formData = new FormData();
  formData.append('title', 'Quảng cáo mới');
  formData.append('link_url', 'https://example.com');
  formData.append('position', 'sidebar_top');
  formData.append('status', 'active');
  formData.append('display_order', '1');
  formData.append('image_file', fileInput.files[0]); // file từ input
  
  createAdvertisement(formData)
    .then(data => {
      console.log('Advertisement created:', data);
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

// Cập nhật quảng cáo
async function updateAdvertisement(adId, updates, newImage = null) {
  try {
    const formData = new FormData();
    
    if (updates.title) formData.append('title', updates.title);
    if (updates.link_url) formData.append('link_url', updates.link_url);
    if (updates.position) formData.append('position', updates.position);
    if (updates.status) formData.append('status', updates.status);
    if (updates.display_order !== undefined) {
      formData.append('display_order', updates.display_order);
    }
    if (newImage) formData.append('image_file', newImage);
    
    const response = await adminApi.post(`/admin/advertisements/${adId}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    return response.data;
  } catch (error) {
    if (error.response?.status === 500) {
      throw new Error(error.response.data.message);
    }
    throw error;
  }
}

// Xóa quảng cáo
async function deleteAdvertisement(adId) {
  try {
    const response = await adminApi.delete(`/admin/advertisements/${adId}`);
    return response; // 204 No Content
  } catch (error) {
    console.error('Error deleting advertisement:', error);
    throw error;
  }
}
```

---

### Admin: Cập nhật logo

```javascript
import adminApi from './adminApi';

async function updateLogo(logoFile) {
  try {
    const formData = new FormData();
    formData.append('logo', logoFile);
    
    const response = await adminApi.post('/admin/settings/logo', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    return response.data;
  } catch (error) {
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      console.error('Validation errors:', errors);
    } else if (error.response?.status === 500) {
      throw new Error(error.response.data.message);
    }
    throw error;
  }
}
```

---

### Superadmin: Quản lý vai trò

```javascript
import adminApi from './adminApi';

async function updateUserRole(userId, newRole) {
  try {
    const response = await adminApi.patch(`/superadmin/users/${userId}/role`, {
      role: newRole
    });
    return response.data;
  } catch (error) {
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      console.error('Validation errors:', errors);
    } else if (error.response?.status === 403) {
      throw new Error('Không thể thay đổi vai trò của Superadmin khác');
    }
    throw error;
  }
}

// Ví dụ sử dụng
updateUserRole(5, 'moderator')
  .then(data => {
    console.log('Role updated:', data);
  })
  .catch(error => {
    console.error('Error:', error.message);
  });
```

---

## Lưu ý quan trọng

### 1. Xử lý lỗi phân quyền

Luôn kiểm tra status code:
- `401`: Chưa đăng nhập hoặc token hết hạn → Redirect về trang login
- `403`: Không có quyền → Hiển thị thông báo phù hợp
- `404`: Không tìm thấy resource
- `422`: Validation error → Hiển thị lỗi cụ thể
- `500`: Server error → Log và thông báo cho user

### 2. Upload file

Khi upload file (quảng cáo, logo), phải dùng `multipart/form-data`:

```javascript
const formData = new FormData();
formData.append('field_name', file);

await adminApi.post('/endpoint', formData, {
  headers: {
    'Content-Type': 'multipart/form-data'
  }
});
```

### 3. Phân trang

Hầu hết API trả về danh sách đều có phân trang. Luôn kiểm tra:
- `meta.current_page`: Trang hiện tại
- `meta.last_page`: Trang cuối cùng
- `links.next`: URL trang tiếp theo (null nếu không có)
- `links.prev`: URL trang trước (null nếu không có)

### 4. Typo trong response

Có một typo trong API khôi phục bài viết:
- Response lỗi dùng `messange` thay vì `message`
- Frontend cần xử lý cả hai trường hợp:

```javascript
const message = error.response.data.messange || error.response.data.message;
```

### 5. Kiểm tra role trước khi gọi API

Nên kiểm tra role của user trước khi hiển thị các chức năng:

```javascript
const user = JSON.parse(localStorage.getItem('admin_user'));
const role = user?.role;

if (role === 'superadmin') {
  // Hiển thị chức năng quản lý role
} else if (role === 'admin') {
  // Hiển thị chức năng admin
} else if (role === 'moderator') {
  // Chỉ hiển thị chức năng moderator
}
```

---

## Hỗ trợ

Nếu có thắc mắc hoặc gặp vấn đề, vui lòng liên hệ với team backend.

**Chúc bạn code vui vẻ! 🚀**



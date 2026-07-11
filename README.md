<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/Razorpay-Payment_Gateway-0066FF?style=for-the-badge&logo=razorpay&logoColor=white" alt="Razorpay">
  <img src="https://img.shields.io/badge/Gemini_AI-Integrated-4285F4?style=for-the-badge&logo=google&logoColor=white" alt="Gemini AI">
</p>

# 🎓 College ERP System

A **comprehensive, full-featured College Enterprise Resource Planning (ERP)** web application built with **Laravel 10**. It provides role-based dashboards for **Admins**, **Faculty**, and **Students** to manage every aspect of academic and campus life — from attendance and exams to fee payments with **Razorpay** and AI-powered timetable generation with **Google Gemini**.

---

## 📋 Table of Contents

- [Features Overview](#-features-overview)
- [Tech Stack](#-tech-stack)
- [System Architecture](#-system-architecture)
- [Role-Based Access](#-role-based-access)
- [Admin Panel Features](#-admin-panel-features)
- [Faculty Panel Features](#-faculty-panel-features)
- [Student Portal Features](#-student-portal-features)
- [Payment Gateway (Razorpay)](#-payment-gateway-razorpay)
- [AI-Powered Features (Gemini)](#-ai-powered-features-gemini)
- [Installation & Setup](#-installation--setup)
- [Environment Variables](#-environment-variables)
- [Default Login Credentials](#-default-login-credentials)
- [Project Structure](#-project-structure)
- [Screenshots](#-screenshots)
- [License](#-license)

---

## ✨ Features Overview

| Category | Features |
|----------|----------|
| **Authentication** | Login, Logout, Change Password, Forgot/Reset Password with verification |
| **Admin Management** | Student CRUD, Faculty CRUD, Department CRUD, Course CRUD, Subject CRUD |
| **Academic** | Timetable management, Attendance (faculty & student), Marks/Results, Assignments |
| **Examinations** | Online MCQ exams, AI question generation, Auto-grading, Result analytics |
| **Finance** | Fee management, **Razorpay payment gateway** (test mode), Payment receipts |
| **Campus Services** | Hostel management, Transport management, Library management |
| **Communication** | Notices, Discussion forums, Real-time messaging/chat |
| **HR/Admin** | Faculty payroll, Leave management, Grievance/complaint system |
| **AI Features** | AI timetable generation, AI question parsing from PDF/images (Google Gemini) |
| **Notifications** | In-app notification system with real-time bell icon |
| **Reports** | Reports & Analytics dashboard |

---

## 🛠 Tech Stack

| Technology | Purpose |
|------------|---------|
| **Laravel 10** | Backend PHP framework |
| **PHP 8.1+** | Server-side language |
| **MySQL** | Relational database |
| **Bootstrap 5.3** | Frontend CSS framework |
| **jQuery 3.6** | JavaScript library |
| **DataTables** | Advanced table rendering |
| **Chart.js** | Charts & analytics |
| **Font Awesome 6** | Icons |
| **Google Inter Font** | Typography |
| **Razorpay SDK** | Payment gateway integration |
| **Google Gemini API** | AI-powered features |
| **XAMPP** | Local development server |

---

## 🏗 System Architecture

```
┌──────────────────────────────────────────────────────────┐
│                    College ERP System                      │
├──────────────┬──────────────┬──────────────────────────────┤
│  Admin Panel │ Faculty Panel │     Student Portal           │
├──────────────┴──────────────┴──────────────────────────────┤
│                  Laravel 10 (MVC)                          │
│  Controllers: AuthController, AdminController,             │
│  FacultyController, StudentController                      │
├───────────────────────────────────────────────────────────┤
│  Services: GeminiAIService                                 │
├───────────────────────────────────────────────────────────┤
│  Models: 40+ Eloquent Models                               │
├───────────────────────────────────────────────────────────┤
│  MySQL Database (college_erp)                              │
├───────────────────────────────────────────────────────────┤
│  External APIs: Razorpay | Google Gemini AI                │
└───────────────────────────────────────────────────────────┘
```

---

## 👥 Role-Based Access

The system has **3 user roles**, each with a dedicated dashboard and sidebar navigation:

| Role | Access Level | Dashboard Route |
|------|-------------|-----------------|
| **Admin** | Full system control — manage students, faculty, courses, fees, hostels, etc. | `/admin/dashboard` |
| **Faculty** | Manage attendance, marks, assignments, exams, study materials, research | `/faculty/dashboard` |
| **Student** | View attendance, results, take exams, pay fees, apply leave, access materials | `/student/dashboard` |

---

## 🔑 Admin Panel Features

### Academic Management
| Feature | Route | Description |
|---------|-------|-------------|
| **Dashboard** | `/admin/dashboard` | Overview stats — total students, faculty, courses, departments |
| **Students CRUD** | `/admin/students` | Add, edit, delete students with enrollment number & course assignment |
| **Faculty CRUD** | `/admin/faculty` | Add, edit, delete faculty with department assignment |
| **Courses** | `/admin/courses` | Create/delete courses linked to departments |
| **Departments** | `/admin/departments` | Create/delete departments with unique codes |
| **Subjects** | `/admin/subjects` | Create subjects with course, semester, and faculty mapping |

### Scheduling & Attendance
| Feature | Route | Description |
|---------|-------|-------------|
| **Timetable** | `/admin/timetable` | Manual timetable creation with conflict detection (faculty & student overlaps) |
| **AI Timetable** | `/admin/ai-timetable` | Google Gemini AI-powered automatic timetable generation |
| **Faculty Attendance** | `/admin/faculty-attendance` | Mark & track daily faculty attendance (present/absent/late) |

### Examinations
| Feature | Route | Description |
|---------|-------|-------------|
| **Manage Exams** | `/admin/exams` | Create exams, add/edit/delete MCQ questions |
| **AI Question Import** | `/admin/exams/{id}/parse-questions` | Upload PDF/image → Gemini AI extracts MCQs automatically |
| **AI Question Generation** | `/admin/exams/{id}/generate-questions` | AI generates questions for a given subject |
| **Exam Results** | `/admin/exams/results` | View all student exam attempts, scores, and review answers |

### Finance & HR
| Feature | Route | Description |
|---------|-------|-------------|
| **Fee Management** | `/admin/fees` | Generate fee dues for students (individual or bulk) |
| **Payroll** | `/admin/payroll` | Create salary records for faculty — base salary, bonuses, deductions |
| **Leave Requests** | `/admin/leaves` | View and approve/reject leave applications from faculty & students |
| **Grievances** | `/admin/complaints` | View and resolve student/faculty complaints |

### Campus Services
| Feature | Route | Description |
|---------|-------|-------------|
| **Hostel Manager** | `/admin/hostels` | Create hostels (boys/girls), allot rooms to students |
| **Transport Manager** | `/admin/transports` | Manage bus routes, allot transport to students |
| **Library Manager** | `/admin/library` | Add books, issue/return books, track availability |

### Other
| Feature | Route | Description |
|---------|-------|-------------|
| **Notices** | `/admin/notices` | Publish notices with optional attachments, target by role |
| **Dept Directory** | `/admin/department-wise` | View faculty & students grouped by department |
| **Reports** | `/admin/reports` | Reports & analytics dashboard |

---

## 👨‍🏫 Faculty Panel Features

| Feature | Route | Description |
|---------|-------|-------------|
| **Dashboard** | `/faculty/dashboard` | Stats overview — assigned subjects, assignments, exams, today's classes |
| **My Attendance** | `/faculty/my-attendance` | View own attendance history |
| **My Timetable** | `/faculty/timetable` | View assigned class schedule with conflict detection |
| **Student Attendance** | `/faculty/attendance` | Mark student attendance by subject & date |
| **Marks Entry** | `/faculty/marks` | Enter internal/external/online exam marks |
| **Assignments** | `/faculty/assignments` | Create assignments with deadlines & file uploads, view/grade submissions |
| **Exams** | `/faculty/exams` | Create MCQ exams, add questions (manual, AI-parse, AI-generate), view attempts |
| **Notices** | `/faculty/notices` | View published notices |
| **Study Materials** | `/faculty/materials` | Upload study materials (PDFs, docs) for students |
| **Answer Sheets** | `/faculty/answer-sheets` | Upload scanned answer sheets |
| **Apply Leave** | `/faculty/leave` | Submit leave applications with document uploads |
| **Mentoring** | `/faculty/mentoring` | Record student mentoring sessions |
| **Research** | `/faculty/research` | Track research publications & papers |
| **Discussion Forum** | `/faculty/forum` | Create topics & reply in discussion threads |
| **Message Students** | `/faculty/chat` | 1-on-1 messaging with department students |

---

## 🎓 Student Portal Features

| Feature | Route | Description |
|---------|-------|-------------|
| **Dashboard** | `/student/dashboard` | Stats — subjects, assignments, exams count, latest notices |
| **Attendance** | `/student/attendance` | View attendance records with present/absent/late stats |
| **Results** | `/student/results` | View marks/grades across subjects |
| **Assignments** | `/student/assignments` | View assignments & submit solutions (file upload) |
| **Exams** | `/student/exams` | Take online MCQ exams with live timer, auto-submit on timeout |
| **Exam Results** | `/student/exams/{id}/result` | View detailed exam results with correct answers |
| **Timetable** | `/student/timetable` | View class schedule with conflict alerts |
| **Study Materials** | `/student/materials` | Access uploaded study materials |
| **Apply Leave** | `/student/leave` | Submit leave applications with supporting documents |
| **Complaints** | `/student/complaints` | File grievances/complaints |
| **Fees Portal** | `/student/fees` | View fee dues, **pay via Razorpay**, download receipts |
| **Campus Services** | `/student/services` | View hostel allotment, transport, library book issues |
| **Discussion Forum** | `/student/forum` | Participate in discussion forums |
| **Message Faculty** | `/student/chat` | 1-on-1 messaging with faculty members |

---

## 💳 Payment Gateway (Razorpay)

The system integrates **Razorpay** for online fee payments with a complete checkout flow:

### How It Works
1. Student clicks **"Pay with Razorpay"** on an unpaid fee
2. Backend creates a Razorpay Order via API
3. Razorpay checkout popup opens with pre-filled student details
4. Student enters card details (test mode — no real charge)
5. On success, backend verifies the payment signature
6. Fee is marked as **Paid** with Razorpay Payment ID stored
7. Student can download/print the payment **receipt**

### Test Mode
The system uses **Razorpay Test Keys** — no real money is deducted.

**Test Card for Payments:**
| Field | Value |
|-------|-------|
| Card Number | `4111 1111 1111 1111` |
| Expiry | Any future date |
| CVV | Any 3 digits |
| OTP | Any value |

### Configuration
Set your Razorpay credentials in `.env`:
```env
RAZORPAY_KEY_ID=rzp_test_xxxxxxxxxxxxx
RAZORPAY_KEY_SECRET=your_test_key_secret
```

---

## 🤖 AI-Powered Features (Gemini)

### AI Timetable Generation
- Admin navigates to **AI Timetable** → selects department, course, semester
- Google Gemini AI generates an optimized, conflict-free timetable
- Results displayed in a visual weekly grid

### AI Question Paper Parsing
- Faculty uploads a **PDF or image** of a question paper
- Gemini AI extracts MCQs with options and correct answers
- Parsed questions can be imported directly into an exam

### AI Question Generation
- Faculty selects a subject and requests AI-generated questions
- Gemini creates relevant MCQs based on the subject topic
- Faculty can review and import questions

### Configuration
Set your Gemini API key in `.env`:
```env
GEMINI_API_KEY=your_gemini_api_key_here
```

---

## 🚀 Installation & Setup

### Prerequisites
- **PHP 8.1+**
- **Composer**
- **MySQL** (via XAMPP or standalone)
- **XAMPP** (recommended for local development)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/madhusudan1das/college-erp-system.git
   cd college-erp-system
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Configure the database**
   
   Create a MySQL database named `college_erp` and update `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=college_erp
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Configure Razorpay (for fee payments)**
   ```env
   RAZORPAY_KEY_ID=rzp_test_xxxxxxxxxxxxx
   RAZORPAY_KEY_SECRET=your_test_key_secret
   ```

8. **Configure Gemini AI (optional, for AI features)**
   ```env
   GEMINI_API_KEY=your_gemini_api_key
   ```

9. **Start the application**
   
   **Using XAMPP:** Place the project in `htdocs` and access via browser:
   ```
   http://localhost/college-erp-system/public
   ```
   
   **Using Artisan:**
   ```bash
   php artisan serve
   ```
   Then visit `http://localhost:8000`

---

## 🔧 Environment Variables

| Variable | Description | Required |
|----------|-------------|----------|
| `APP_NAME` | Application name | Yes |
| `APP_URL` | Application URL | Yes |
| `DB_DATABASE` | MySQL database name | Yes |
| `DB_USERNAME` | MySQL username | Yes |
| `DB_PASSWORD` | MySQL password | Yes |
| `RAZORPAY_KEY_ID` | Razorpay API Key ID (test or live) | For payments |
| `RAZORPAY_KEY_SECRET` | Razorpay API Key Secret | For payments |
| `GEMINI_API_KEY` | Google Gemini API Key | For AI features |

---

## 🔐 Default Login Credentials

> ⚠️ These are sample credentials. Actual credentials depend on your database seed/setup.

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `password` |
| Faculty | (created by admin) | (set during creation) |
| Student | (created by admin) | (set during creation) |

---

## 📁 Project Structure

```
college-erp-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php        # Login, Logout, Password Reset
│   │   │   ├── AdminController.php       # All admin operations (1800+ lines)
│   │   │   ├── FacultyController.php     # All faculty operations (1500+ lines)
│   │   │   └── StudentController.php     # All student operations (550+ lines)
│   │   └── Middleware/
│   ├── Models/                           # 40+ Eloquent models
│   │   ├── User.php, Student.php, Faculty.php
│   │   ├── Course.php, Department.php, Subject.php
│   │   ├── Exam.php, ExamQuestion.php, ExamAttempt.php
│   │   ├── Fee.php, Salary.php, Hostel.php
│   │   └── ... (and many more)
│   └── Services/
│       └── GeminiAIService.php           # Google Gemini AI integration
├── config/
│   └── razorpay.php                      # Razorpay configuration
├── database/
│   └── migrations/                       # Database schema migrations
├── public/
│   ├── css/premium.css                   # Custom premium stylesheet
│   └── uploads/                          # File uploads (assignments, timetables, etc.)
├── resources/views/
│   ├── admin/                            # Admin panel views
│   │   ├── dashboard.blade.php
│   │   ├── students/, faculty/           # CRUD views
│   │   ├── timetable.blade.php
│   │   ├── ai_timetable.blade.php
│   │   └── exams/                        # Exam management views
│   ├── faculty/                          # Faculty panel views
│   │   ├── dashboard.blade.php
│   │   ├── assignments/, exams/
│   │   └── timetable.blade.php
│   ├── student/                          # Student portal views
│   │   ├── dashboard.blade.php
│   │   ├── fees.blade.php                # Razorpay payment UI
│   │   ├── receipt.blade.php             # Payment receipt
│   │   └── take_exam.blade.php           # Online exam interface
│   ├── auth/                             # Auth views (login, password reset)
│   └── layouts/
│       └── app.blade.php                 # Main layout with sidebar & nav
├── routes/
│   └── web.php                           # All application routes (270 lines)
├── .env                                  # Environment configuration
├── composer.json                         # PHP dependencies
└── README.md                             # This file
```

---

## 📸 Screenshots

> Screenshots can be added here to showcase the UI.

| Page | Description |
|------|-------------|
| Login Page | Modern glassmorphism login with role selection |
| Admin Dashboard | Stats cards, quick navigation |
| Student Fees | Razorpay checkout popup, fee summary cards |
| Online Exam | Timed MCQ exam with auto-submit |
| Timetable | Weekly grid with conflict highlighting |
| Receipt | Professional payment receipt with Razorpay ID |

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👨‍💻 Author

**Madhusudan Das**  
GitHub: [@madhusudan1das](https://github.com/madhusudan1das)

---

<p align="center">
  <b>⭐ If you found this project useful, please give it a star on GitHub! ⭐</b>
</p>

# Entity Relationship Diagram — EduVora

62 tabel di PostgreSQL, diorganisir per domain.

---

## 1. Core — Users, Roles, Schools

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        string profile_picture
        string phone_number
        enum status "active|inactive"
        timestamps
    }

    roles {
        bigint role_id PK
        string role_name UK
        string role_description
        enum status "active|inactive"
        timestamps
    }

    user_has_roles {
        bigint user_has_role_id PK
        bigint user_id FK
        bigint role_id FK
        timestamps
    }

    school_profiles {
        bigint school_id PK
        string school_name
        string npsn UK
        string nss UK
        enum accreditation "A|B|C|D|E"
        enum school_type "Elementary|Junior High|Senior High"
        string contact_email
        string contact_phone
        string website
        string address
        string province
        string city
        string district
        string postal_code
        string logo
        string headmaster_name
        string headmaster_nip
        tinyint kkm_default
        enum status "active|inactive"
        timestamps
    }

    user_has_schools {
        bigint user_has_school_id PK
        bigint user_id FK
        bigint school_id FK
        timestamps
    }

    users ||--o{ user_has_roles : ""
    roles ||--o{ user_has_roles : ""
    users ||--o{ user_has_schools : ""
    school_profiles ||--o{ user_has_schools : ""
```

---

## 2. Academic — Tahun Ajaran, Semester, Mata Pelajaran, Kelas, Ruangan, Jadwal

```mermaid
erDiagram
    academic_years {
        bigint academic_year_id PK
        bigint school_id FK
        string academic_year_name
        date start_date
        date end_date
        enum status "active|inactive"
        timestamp deleted_at
        timestamps
    }

    semesters {
        bigint semester_id PK
        string semester_name
        bigint academic_year_id FK
        date start_date
        date end_date
        date midterm_start_date
        date midterm_end_date
        date final_start_date
        date final_end_date
        enum status "active|inactive"
        timestamps
    }

    subjects {
        bigint id PK
        bigint school_id FK
        string subject_name
        string subject_code UK
        string category
        tinyint credits
        tinyint hours_per_week
        text description
        enum status "active|inactive"
        timestamps
    }

    rooms {
        bigint room_id PK
        bigint school_id FK
        string room_name
        string code
        enum type "classroom|lab|library|office|sport"
        tinyint floor
        string building
        tinyint capacity
        string facility
        enum status "available|maintenance|inactive"
        timestamp deleted_at
        timestamps
    }

    grades {
        bigint grade_id PK
        bigint school_id FK
        bigint academic_year_id FK
        bigint room_id FK
        bigint homeroom_teacher_id FK
        string grade_name
        tinyint level
        enum status "active|inactive|graduated|archived"
        timestamp deleted_at
        timestamps
    }

    grade_subjects {
        bigint id PK
        bigint grade_id FK
        bigint subject_id FK
        bigint teacher_id FK
        tinyint kkm
        tinyint weight_harian "default 30"
        tinyint weight_uts "default 30"
        tinyint weight_uas "default 40"
        enum status "active|inactive"
        timestamps
    }

    schedules {
        bigint schedule_id PK
        bigint school_id FK
        bigint grade_subject_id FK
        bigint room_id FK
        bigint semester_id FK
        tinyint day_of_week "1=Mon..7=Sun"
        time start_time
        time end_time
        enum session_type "regular|lab|exam|extracurricular|remedial"
        enum status "active|inactive"
        timestamps
    }

    school_profiles ||--o{ academic_years : "has"
    academic_years ||--o{ semesters : "has"
    school_profiles ||--o{ subjects : "has"
    school_profiles ||--o{ rooms : "has"
    school_profiles ||--o{ grades : "has"
    academic_years ||--o{ grades : "has"
    rooms ||--o{ grades : "ruang kelas"
    grades ||--o{ grade_subjects : "mata pelajaran"
    subjects ||--o{ grade_subjects : ""
    grade_subjects ||--o{ schedules : ""
    rooms ||--o{ schedules : "ruang jadwal"
    semesters ||--o{ schedules : ""
    school_profiles ||--o{ schedules : ""
```

---

## 3. People — Guru, Siswa, Orang Tua

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email UK
        timestamps
    }

    teachers {
        bigint teacher_id PK
        bigint user_id FK UK
        string nip UK
        string nik UK
        string full_name
        string birth_place
        date birth_date
        enum gender "male|female"
        string religion
        string address
        string phone UK
        string email UK
        enum employment_status "permanent|contract"
        string position
        string grade_level
        enum education_level "bachelor|master|doctorate"
        string major
        string certification
        string npwp
        date join_date
        enum status "active|inactive|retired|suspended"
        timestamps
    }

    students {
        bigint id PK
        bigint user_id FK UK
        string nis UK
        string full_name
        string nick_name
        string email UK
        date birth_date
        enum gender "male|female"
        string phone_number
        text address
        string city
        string province
        string postal_code
        string profile_photo
        bigint grade_id FK
        string class_group
        enum status "active|inactive|graduated|dropped"
        date enrollment_date
        date graduation_date
        timestamps
    }

    student_parents {
        bigint id PK
        bigint student_id FK
        string parent_name
        enum relationship "father|mother|guardian|other"
        string email
        string phone_number
        string occupation
        text address
        enum status "active|inactive"
        timestamps
    }

    student_grade_histories {
        bigint id PK
        bigint student_id FK
        bigint grade_id FK
        bigint academic_year_id FK
        enum status "promoted|repeat|dropout|completed"
        text remarks
        timestamps
    }

    users ||--o| teachers : ""
    users ||--o| students : ""
    students ||--o{ student_parents : "orang tua"
    students ||--o{ student_grade_histories : "riwayat kelas"
```

---

## 4. Absensi — Session & Detail

```mermaid
erDiagram
    attendance_sessions {
        bigint attendance_session_id PK
        bigint school_id FK
        bigint schedule_id FK
        bigint teacher_id FK
        bigint subject_id FK
        bigint grade_id FK
        bigint semester_id FK
        date attendance_date
        tinyint meeting_number
        enum status "draft|submitted|approved"
        boolean is_locked
        text notes
        bigint recorded_by FK
        timestamps
    }

    attendance_details {
        bigint attendance_detail_id PK
        bigint attendance_session_id FK
        bigint student_id FK
        enum status "H|I|S|A|L"
        text note
        string attachment
        timestamp notified_at
        timestamps
    }

    attendance_sessions ||--o{ attendance_details : ""
```

---

## 5. Penilaian — Sesi Nilai, Detail, Rapor

```mermaid
erDiagram
    score_sessions {
        bigint score_session_id PK
        bigint grade_subject_id FK
        bigint semester_id FK
        enum score_type "daily|assignment|mid_exam|final_exam"
        string title
        text description
        date score_date
        boolean is_published
        timestamps
    }

    score_details {
        bigint score_detail_id PK
        bigint score_session_id FK
        bigint student_id FK
        decimal score
        decimal max_score "default 100"
        string notes
        timestamps
    }

    report_cards {
        bigint id PK
        bigint student_id FK
        bigint grade_id FK
        bigint semester_id FK
        decimal gpa
        decimal final_score
        enum status "draft|published|archived"
        text comments
        timestamps
    }

    report_card_details {
        bigint id PK
        bigint report_card_id FK
        bigint subject_id FK
        decimal score_harian
        decimal score_uts
        decimal score_uas
        decimal final_score
        string grade_letter
        text remarks
        timestamps
    }

    score_sessions ||--o{ score_details : ""
    report_cards ||--o{ report_card_details : ""
```

---

## 6. Ujian & Soal

```mermaid
erDiagram
    question_banks {
        bigint id PK
        bigint subject_id FK
        bigint teacher_id FK
        enum question_type "multiple_choice|essay|true_false|short_answer"
        text content
        text options
        text answer_key
        string difficulty
        timestamps
    }

    exams {
        bigint id PK
        bigint subject_id FK
        bigint grade_id FK
        bigint semester_id FK
        string exam_name
        enum exam_type "uts|uas|quiz|remedial|placement|other"
        datetime start_date
        datetime end_date
        int duration_minutes
        decimal total_score
        text instructions
        enum status "draft|published|closed"
        timestamps
    }

    exam_questions {
        bigint id PK
        bigint exam_id FK
        bigint question_bank_id FK
        decimal point_value "default 1"
        timestamps
    }

    exam_sessions {
        bigint id PK
        bigint exam_id FK
        bigint student_id FK
        datetime started_at
        datetime finished_at
        decimal score
        enum status "scheduled|in_progress|completed|cancelled"
        timestamps
    }

    exam_answers {
        bigint id PK
        bigint exam_session_id FK
        bigint exam_question_id FK
        text answer_text
        decimal score
        boolean is_correct
        timestamps
    }

    exams ||--o{ exam_questions : ""
    question_banks ||--o{ exam_questions : ""
    exams ||--o{ exam_sessions : ""
    exam_sessions ||--o{ exam_answers : ""
    exam_questions ||--o{ exam_answers : ""
```

---

## 7. Tugas

```mermaid
erDiagram
    assigments {
        bigint id PK
        bigint subject_id FK
        bigint grade_id FK
        bigint teacher_id FK
        string title
        text description
        date assigned_date
        date due_date
        string attachment
        enum status "draft|published|closed"
        timestamps
    }

    assigment_submissions {
        bigint id PK
        bigint assigment_id FK
        bigint student_id FK
        datetime submitted_at
        string file_path
        enum status "pending|submitted|graded"
        decimal score
        text feedback
        timestamps
    }

    assigments ||--o{ assigment_submissions : ""
```

---

## 8. Keuangan

```mermaid
erDiagram
    fee_types {
        bigint id PK
        string name
        text description
        enum category "tuition|registration|book|activity|other"
        enum status "active|inactive"
        timestamps
    }

    fee_settings {
        bigint id PK
        bigint fee_type_id FK
        bigint grade_id FK
        bigint academic_year_id FK
        decimal amount
        enum period "monthly|semester|yearly|one_time"
        enum status "active|inactive"
        timestamps
    }

    student_invoices {
        bigint id PK
        bigint student_id FK
        bigint fee_setting_id FK
        string invoice_number UK
        decimal amount
        date due_date
        enum status "unpaid|partial|paid|overdue"
        text notes
        timestamps
    }

    payments {
        bigint id PK
        bigint student_invoice_id FK
        decimal amount
        date payment_date
        string payment_method
        string reference_number
        text notes
        timestamps
    }

    payrolls {
        bigint id PK
        bigint teacher_id FK
        string month
        int year
        decimal base_salary
        decimal allowances
        decimal deductions
        decimal net_salary
        enum status "draft|pending|paid"
        date payment_date
        timestamps
    }

    scholarships {
        bigint id PK
        string name
        text description
        decimal amount
        enum type "full|partial|tuition_waiver"
        date start_date
        date end_date
        enum status "active|inactive"
        timestamps
    }

    scholarship_recipients {
        bigint id PK
        bigint scholarship_id FK
        bigint student_id FK
        date start_date
        date end_date
        enum status "active|suspended|completed"
        text notes
        timestamps
    }

    finance_transactions {
        bigint id PK
        string transaction_number UK
        enum type "income|expense"
        string category
        decimal amount
        date transaction_date
        text description
        bigint recorded_by FK
        timestamps
    }

    fee_types ||--o{ fee_settings : ""
    fee_settings ||--o{ student_invoices : ""
    student_invoices ||--o{ payments : ""
    scholarships ||--o{ scholarship_recipients : ""
```

---

## 9. Perpustakaan

```mermaid
erDiagram
    books {
        bigint id PK
        string title
        string isbn UK
        string author
        string publisher
        year publish_year
        string category
        text description
        string cover_image
        enum status "active|inactive"
        timestamps
    }

    book_copies {
        bigint id PK
        bigint book_id FK
        string copy_number UK
        enum condition "good|damaged|lost"
        enum status "available|borrowed|reserved"
        timestamps
    }

    book_loans {
        bigint id PK
        bigint student_id FK
        bigint book_copy_id FK
        date loan_date
        date due_date
        date return_date
        enum status "borrowed|returned|overdue"
        text notes
        timestamps
    }

    books ||--o{ book_copies : ""
    book_copies ||--o{ book_loans : ""
```

---

## 10. Aset

```mermaid
erDiagram
    assets {
        bigint id PK
        string name
        string code UK
        string category
        text description
        int quantity
        string condition
        date purchase_date
        decimal value
        enum status "available|in_use|maintenance"
        timestamps
    }

    asset_loans {
        bigint id PK
        bigint asset_id FK
        bigint teacher_id FK
        date loan_date
        date return_date
        int quantity
        text purpose
        enum status "borrowed|returned"
        timestamps
    }

    assets ||--o{ asset_loans : ""
```

---

## 11. Komunikasi

```mermaid
erDiagram
    announcements {
        bigint id PK
        bigint posted_by FK
        string title
        text content
        enum target "all|students|teachers|parents"
        date publish_date
        date expired_date
        enum status "draft|published|archived"
        timestamps
    }

    messages {
        bigint id PK
        bigint sender_id FK
        bigint receiver_id FK
        string subject
        text content
        enum type "inbox|sent"
        boolean is_read
        timestamps
    }

    notifications {
        bigint id PK
        bigint user_id FK
        string title
        text message
        string type
        boolean is_read
        timestamps
    }
```

---

## 12. Aktivitas Tambahan

```mermaid
erDiagram
    extracurriculars {
        bigint id PK
        string name
        string code UK
        text description
        bigint teacher_id FK
        time meeting_time
        string meeting_day
        string location
        enum status "active|inactive"
        timestamps
    }

    extracurriculars_members {
        bigint id PK
        bigint extracurricular_id FK
        bigint student_id FK
        date join_date
        enum status "active|inactive"
        timestamps
    }

    achievements {
        bigint id PK
        bigint student_id FK
        bigint extracurricular_id FK
        string title
        text description
        string level
        date achievement_date
        string certificate
        timestamps
    }

    violation_types {
        bigint id PK
        string name
        text description
        int point
        enum status "active|inactive"
        timestamps
    }

    violations {
        bigint id PK
        bigint student_id FK
        bigint violation_type_id FK
        bigint reported_by FK
        text description
        date violation_date
        enum status "pending|investigated|resolved"
        text resolution
        timestamps
    }

    counselling_sessions {
        bigint id PK
        bigint student_id FK
        bigint counselor_id FK
        date session_date
        text topic
        text notes
        text follow_up
        enum status "scheduled|completed|cancelled"
        timestamps
    }

    leave_requests {
        bigint id PK
        bigint teacher_id FK
        date start_date
        date end_date
        text reason
        enum status "pending|approved|rejected"
        bigint approved_by FK
        timestamps
    }

    registrations {
        bigint id PK
        bigint student_id FK
        date registration_date
        enum registration_type "new|transfer|re_registration"
        bigint grade_id FK
        enum status "pending|approved|rejected"
        text notes
        timestamps
    }

    teaching_journals {
        bigint id PK
        bigint teacher_id FK
        bigint grade_subject_id FK
        date lesson_date
        string topic
        text material_covered
        text activities
        text reflection
        enum status "draft|submitted"
        timestamps
    }

    extracurriculars ||--o{ extracurriculars_members : ""
    extracurriculars ||--o{ achievements : ""
    violation_types ||--o{ violations : ""
```

---

## 13. System

```mermaid
erDiagram
    audit_logs {
        bigint id PK
        bigint user_id FK
        string action
        string table_name
        bigint record_id
        text old_values
        text new_values
        string ip_address
        timestamps
    }
```

---

## Ringkasan Relasi Antar Domain

```
users ──┬── user_has_roles ──── roles
        ├── user_has_schools ── school_profiles ──┬── academic_years ── semesters
        │                                         ├── subjects
        │                                         ├── rooms
        │                                         ├── grades ──┬── grade_subjects ──┬── schedules
        │                                         │            │                    ├── score_sessions ── score_details
        │                                         │            │                    └── attendance_sessions ── attendance_details
        │                                         │            └── students ──┬── student_parents
        │                                         │                           ├── student_grade_histories
        │                                         │                           └── score_details
        ├── teachers ──┬── grade_subjects
        │              └── schedules
        └── students ──┬── score_details
                       ├── attendance_details
                       ├── exam_sessions ── exam_answers
                       ├── assigment_submissions
                       ├── book_loans
                       ├── student_invoices ── payments
                       ├── report_cards ── report_card_details
                       └── violations
```

---

## Legend

| Simbol | Arti |
|--------|------|
| `PK` | Primary Key |
| `FK` | Foreign Key |
| `UK` | Unique Key |
| `enum` | Tipe data enum (PostgreSQL) |
| `nullable` | Boleh NULL |
| `CASCADE` | Hapus record terkait jika parent dihapus |
| `SET NULL` | Set ke NULL jika parent dihapus |
| `timestamps` | Kolom `created_at` & `updated_at` |

## Total: 62 Tabel

| Domain | Tabel | Jumlah |
|--------|-------|--------|
| Core | users, roles, user_has_roles, user_has_schools, school_profiles, password_reset_tokens, sessions, cache, cache_locks, jobs, job_batches, failed_jobs | 12 |
| Academic | academic_years, semesters, subjects, rooms, grades, grade_subjects, schedules | 7 |
| People | teachers, students, student_parents, student_grade_histories | 4 |
| Activity | attendance_sessions, attendance_details, extracurriculars, extracurriculars_members, achievements, violation_types, violations, counselling_sessions, leave_requests, registrations, teaching_journals | 11 |
| Exam | score_sessions, score_details, report_cards, report_card_details, question_banks, exams, exam_questions, exam_sessions, exam_answers, assigments, assigment_submissions | 11 |
| Finance | fee_types, fee_settings, student_invoices, payments, payrolls, scholarships, scholarship_recipients, finance_transactions | 8 |
| Library | books, book_copies, book_loans | 3 |
| Asset | assets, asset_loans | 2 |
| Communication | announcements, messages, notifications | 3 |
| System | audit_logs | 1 |
| **Total** | | **62** |

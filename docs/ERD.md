# Entity Relationship Diagram — EduVora

62 tabel di PostgreSQL, diorganisir per domain. Diagram menggunakan [Mermaid](https://mermaid.js.org/) — render di GitHub/GitLab/VS Code.

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
        string status
    }

    roles {
        bigint role_id PK
        string role_name UK
        string role_description
        string status
    }

    user_has_roles {
        bigint user_has_role_id PK
        bigint user_id FK
        bigint role_id FK
    }

    school_profiles {
        bigint school_id PK
        string school_name
        string npsn UK
        string nss UK
        string accreditation
        string school_type
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
        int kkm_default
        string status
    }

    user_has_schools {
        bigint user_has_school_id PK
        bigint user_id FK
        bigint school_id FK
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
        string status
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
        string status
    }

    subjects {
        bigint id PK
        bigint school_id FK
        string subject_name
        string subject_code UK
        string category
        int credits
        int hours_per_week
        text description
        string status
    }

    rooms {
        bigint room_id PK
        bigint school_id FK
        string room_name
        string code
        string type
        int floor
        string building
        int capacity
        string facility
        string status
    }

    grades {
        bigint grade_id PK
        bigint school_id FK
        bigint academic_year_id FK
        bigint room_id FK
        bigint homeroom_teacher_id FK
        string grade_name
        int level
        string status
    }

    grade_subjects {
        bigint id PK
        bigint grade_id FK
        bigint subject_id FK
        bigint teacher_id FK
        int kkm
        int weight_harian
        int weight_uts
        int weight_uas
        string status
    }

    schedules {
        bigint schedule_id PK
        bigint school_id FK
        bigint grade_subject_id FK
        bigint room_id FK
        bigint semester_id FK
        int day_of_week
        time start_time
        time end_time
        string session_type
        string status
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
    }

    teachers {
        bigint teacher_id PK
        bigint user_id FK
        string nip UK
        string nik UK
        string full_name
        string birth_place
        date birth_date
        string gender
        string religion
        string address
        string phone UK
        string email UK
        string employment_status
        string position
        string grade_level
        string education_level
        string major
        string certification
        string npwp
        date join_date
        string status
    }

    students {
        bigint id PK
        bigint user_id FK
        string nis UK
        string full_name
        string nick_name
        string email UK
        date birth_date
        string gender
        string phone_number
        text address
        string city
        string province
        string postal_code
        string profile_photo
        bigint grade_id FK
        string class_group
        string status
        date enrollment_date
        date graduation_date
    }

    student_parents {
        bigint id PK
        bigint student_id FK
        string parent_name
        string relationship
        string email
        string phone_number
        string occupation
        text address
        string status
    }

    student_grade_histories {
        bigint id PK
        bigint student_id FK
        bigint grade_id FK
        bigint academic_year_id FK
        string status
        text remarks
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
        int meeting_number
        string status
        bool is_locked
        text notes
        bigint recorded_by FK
    }

    attendance_details {
        bigint attendance_detail_id PK
        bigint attendance_session_id FK
        bigint student_id FK
        string status
        text note
        string attachment
        timestamp notified_at
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
        string score_type
        string title
        text description
        date score_date
        bool is_published
    }

    score_details {
        bigint score_detail_id PK
        bigint score_session_id FK
        bigint student_id FK
        decimal score
        decimal max_score
        string notes
    }

    report_cards {
        bigint id PK
        bigint student_id FK
        bigint grade_id FK
        bigint semester_id FK
        decimal gpa
        decimal final_score
        string status
        text comments
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
        string question_type
        text content
        text options
        text answer_key
        string difficulty
    }

    exams {
        bigint id PK
        bigint subject_id FK
        bigint grade_id FK
        bigint semester_id FK
        string exam_name
        string exam_type
        datetime start_date
        datetime end_date
        int duration_minutes
        decimal total_score
        text instructions
        string status
    }

    exam_questions {
        bigint id PK
        bigint exam_id FK
        bigint question_bank_id FK
        decimal point_value
    }

    exam_sessions {
        bigint id PK
        bigint exam_id FK
        bigint student_id FK
        datetime started_at
        datetime finished_at
        decimal score
        string status
    }

    exam_answers {
        bigint id PK
        bigint exam_session_id FK
        bigint exam_question_id FK
        text answer_text
        decimal score
        bool is_correct
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
        string status
    }

    assigment_submissions {
        bigint id PK
        bigint assigment_id FK
        bigint student_id FK
        datetime submitted_at
        string file_path
        string status
        decimal score
        text feedback
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
        string category
        string status
    }

    fee_settings {
        bigint id PK
        bigint fee_type_id FK
        bigint grade_id FK
        bigint academic_year_id FK
        decimal amount
        string period
        string status
    }

    student_invoices {
        bigint id PK
        bigint student_id FK
        bigint fee_setting_id FK
        string invoice_number UK
        decimal amount
        date due_date
        string status
        text notes
    }

    payments {
        bigint id PK
        bigint student_invoice_id FK
        decimal amount
        date payment_date
        string payment_method
        string reference_number
        text notes
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
        string status
        date payment_date
    }

    scholarships {
        bigint id PK
        string name
        text description
        decimal amount
        string type
        date start_date
        date end_date
        string status
    }

    scholarship_recipients {
        bigint id PK
        bigint scholarship_id FK
        bigint student_id FK
        date start_date
        date end_date
        string status
        text notes
    }

    finance_transactions {
        bigint id PK
        string transaction_number UK
        string type
        string category
        decimal amount
        date transaction_date
        text description
        bigint recorded_by FK
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
        int publish_year
        string category
        text description
        string cover_image
        string status
    }

    book_copies {
        bigint id PK
        bigint book_id FK
        string copy_number UK
        string condition
        string status
    }

    book_loans {
        bigint id PK
        bigint student_id FK
        bigint book_copy_id FK
        date loan_date
        date due_date
        date return_date
        string status
        text notes
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
        string status
    }

    asset_loans {
        bigint id PK
        bigint asset_id FK
        bigint teacher_id FK
        date loan_date
        date return_date
        int quantity
        text purpose
        string status
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
        string target
        date publish_date
        date expired_date
        string status
    }

    messages {
        bigint id PK
        bigint sender_id FK
        bigint receiver_id FK
        string subject
        text content
        string type
        bool is_read
    }

    notifications {
        bigint id PK
        bigint user_id FK
        string title
        text message
        string type
        bool is_read
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
        string status
    }

    extracurriculars_members {
        bigint id PK
        bigint extracurricular_id FK
        bigint student_id FK
        date join_date
        string status
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
    }

    violation_types {
        bigint id PK
        string name
        text description
        int point
        string status
    }

    violations {
        bigint id PK
        bigint student_id FK
        bigint violation_type_id FK
        bigint reported_by FK
        text description
        date violation_date
        string status
        text resolution
    }

    counselling_sessions {
        bigint id PK
        bigint student_id FK
        bigint counselor_id FK
        date session_date
        text topic
        text notes
        text follow_up
        string status
    }

    leave_requests {
        bigint id PK
        bigint teacher_id FK
        date start_date
        date end_date
        text reason
        string status
        bigint approved_by FK
    }

    registrations {
        bigint id PK
        bigint student_id FK
        date registration_date
        string registration_type
        bigint grade_id FK
        string status
        text notes
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
        string status
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
| `nullable` | Boleh NULL |
| `CASCADE` | Hapus record terkait jika parent dihapus |
| `SET NULL` | Set ke NULL jika parent dihapus |

---

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

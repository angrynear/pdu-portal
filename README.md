# 📘 PDU Portal  
### Project, Task, and Personnel Management System  
*(MBHTE / BARMM Branded)*

The **PDU Portal** is a **Laravel-based web application** designed to manage **projects, tasks, personnel, and activity logs** with strict **role-based access**, **audit trails**, and **government-style reporting**.

This system follows **MBHTE / BARMM branding and operational standards**, emphasizing clarity, accountability, and data integrity.

---

## 🏛️ Purpose

The system is built to:
- Monitor project implementation and progress
- Track task execution with full audit history
- Manage personnel assignments and accountability
- Preserve historical records through archiving (no hard deletes)
- Support compliance, reporting, and transparency

---

## 🚀 Tech Stack

- **Backend:** Laravel (latest)
- **Frontend:** Blade + Bootstrap (latest)
- **Database:** MySQL
- **Local Environment:** XAMPP
- **IDE:** VS Code
- **Architecture:** MVC with Eloquent ORM
- **Authentication:** Admin-created accounts only

---

## 🎨 UI & BRANDING (MBHTE / BARMM)

The UI follows **MBHTE / BARMM visual identity guidelines**.

### Branding Principles
- Clean, professional, government-style layout
- Minimal animations
- High readability and accessibility
- Consistent color usage

### Layout Standards
- **Top Navbar**
  - System name / logo (PDU Portal – MBHTE)
  - User account menu
- **Left Sidebar**
  - Role-based navigation
  - Icon + label menu items
- **Main Content Area**
  - Card-based layout
  - Data tables
  - Progress bars and status badges

---

## 🌐 Public Pages (No Login Required)

### Homepage
- Top navigation bar
- Hero section:
  - System title
  - Short description
  - Login call-to-action
- Slideshow of architectural/project images
  - Managed by Admin via Slideshow Manager

### Login Page
- Secure authentication
- No public registration
- Clean, official government-style design

---

## 🧱 Authenticated Dashboard Layout

After login, users access:
- Top Navbar
- Left Sidebar (role-based)
- Main content area

---

## 📌 Sidebar Navigation Structure

### MAIN
- **Dashboard** (Admin & User)

### PROJECT
- **Manage Projects**
  - Admin:
    - View all projects
    - Create, edit, archive, restore projects
    - Access Project Overview (tasks are created here)
  - User:
    - View assigned projects only (read-only)

### TASK
- **Manage Tasks**
  - Admin:
    - View all tasks (project tasks + personal tasks)
  - User:
    - View assigned tasks
    - View own personal tasks

### PERSONNEL *(Admin Only)*
- **Manage Personnel**
  - Create users
  - View active and deactivated users
  - Deactivate / reactivate accounts

### CONTENT *(Admin Only)*
- **Slideshow Manager**
  - Manage homepage slideshow images
  - Image with description
  - Uses archiving instead of deletion

### ARCHIVES *(Admin Only)*
- Archived Projects
- Archived Tasks
- Deactivated Personnel

### LOGS
- Project Activity Logs
- Task Activity Logs
  - Admin: all logs
  - User: own-related logs only

### ACCOUNT
- My Profile
- Logout

---

## 👤 User / Personnel Management

### User Creation (Admin Only – Minimal Input)
- Full name
- Email
- Role (Admin / User)
- Account status (Active / Deactivated)

### User Profile (Editable Later)
- Profile photo
- Profession
- Designation
- Employment status (Permanent, Contractual, Job Order, etc.)
- Employment start date

---

## 🚫 User Deactivation Rules

- Deactivated users:
  - Cannot log in
  - Cannot interact with the system
- Assigned tasks:
  - Remain in the system
  - Progress and history preserved
  - Assigned user becomes `NULL`
  - Displayed as `-` in UI
- Tasks may be reassigned later by Admin

---

## 🗂️ Task Classification

### 1️⃣ Project Tasks (Admin-Created)
- Must always belong to a project
- Task type (acts as task name):
  - Perspective
  - Architectural
  - Structural
  - Mechanical
  - Electrical
  - Plumbing
  - Custom (admin-defined)
- Can be assigned to users
- Affects project progress and status

### 2️⃣ Personal Tasks (User-Created)
- Not attached to any project
- Task name/type entered freely by user
- Visible to:
  - Task owner
  - Admin
- Does **not** affect any project

---

## 🏗️ Project Features

Each project includes:
- Project name
- Location
- Sub-sector:
  - Basic Education
  - Higher Education
  - Madaris Education
  - Technical Education
  - Others
- Source of fund
- Funding year
- Amount
- Start date
- Due date
- Archived flag

### Auto-Computed
- **Project Status**
  - Ongoing → at least one task progress < 100
  - Completed → all tasks progress = 100
- **Project Progress**
  - Average of all project task progress

---

## ✅ Task Fields

- Task type / name
- Start date
- Due date
- Progress (0–100)
- Status (auto-derived from progress)
- Assigned user (nullable)
- Project ID (required for project tasks, null for personal)
- Created by (user)
- Archived at

---

## 🔒 Task Progress Rules

- Task status is not manually editable
- Status is derived strictly from progress
- Users:
  - Can only **increase** progress
  - Cannot decrease or reset progress
- Admins:
  - Can increase or decrease progress
- When progress reaches 100:
  - Progress update disabled for users
  - Progress update remains enabled for admins

---

## 💬 Task Remarks / Comments

- Admins and assigned users can add remarks
- Each remark records:
  - Comment text
  - Author
  - Timestamp
- Remarks are immutable

---

## 📎 Task File Uploads

- Multiple files per task
- Supported:
  - PDF, DOCX, XLSX
  - Images
- Files are linked to tasks and logged

---

## 🕒 Task History & Audit Trail

Each task maintains a **complete, immutable history**, including:
- Assignment changes
- Progress updates and overrides
- Completion events
- File uploads/removals
- Remarks added
- Archiving/restoring
- Actor and timestamp

---

## 🗄️ Archiving Policy

- **No hard deletes**
- Archiving is used for:
  - Projects
  - Tasks
  - Slideshow content
- Archived items can be restored by Admin

---

## 📜 Activity Logs

- Automatically recorded
- Tracks:
  - User lifecycle events
  - Task creation, assignment, progress changes
  - Remarks and file actions
  - Project/task archive & restore
- Admin: full access
- User: own-related logs only

---

## 🗺️ Development Roadmap

Development follows a **strict, phase-based roadmap**:
1. Phase 0 – Preparation
2. Phase 1 – Database Schema & Migrations
3. Phase 2 – Authentication & Roles
4. Phase 3 – UI Layout (MBHTE Branded)
5. Phase 4 – Personnel Module
6. Phase 5 – Project Module
7. Phase 6 – Task Module
8. Phase 7 – Archives
9. Phase 8 – Activity Logs
10. Phase 9 – Slideshow Manager
11. Phase 10 – Dashboard & Polish
12. Phase 11 – Testing & Hardening

---

## 🏁 Final Note

This README serves as the **authoritative system specification** for the PDU Portal.

All rules, roles, UI structure, and behaviors described here must be enforced exactly during development.

---

> **“Build it once. Build it right. Build it compliant.”**  
> *MBHTE / BARMM*

---
name: security-audit
description: "Comprehensive security audit skill for Laravel applications. Covers OWASP Top 10, authentication and RBAC authorization verification, IDOR prevention, input sanitization and SQL injection guards, CSRF/XSS mitigations, sensitive data leaks, session hardening, and automated vulnerability scanning."
---

# Security Audit Skill for Laravel Applications

This skill provides comprehensive instructions, methodologies, and audit verification procedures for assessing and hardening Laravel full-stack web applications.

---

## 1. Scope & Core Tenets

When conducting a security audit:
1. **Never Compromise Real Data**:
   - In production or shared database environments, never execute destructive migrations or flush real citizen records.
   - Use clean, transactional assertions in test suites with automated cleanup in `tearDown()`.
2. **Defense in Depth**:
   - Combine middleware boundaries, policy/gate checks, database constraints, and safe output encoding.
3. **Principle of Least Privilege**:
   - Ensure non-privileged users cannot access elevated administrative or superadmin endpoints.

---

## 2. Security Audit Checklist

### A. Authentication & Access Control (RBAC)
- [ ] **Role & Permission Isolation**:
  - Superadmin-only routes (such as user management, role assignments) must be guarded with explicit middleware (e.g. `peran:superadmin`).
  - Regular admin routes must verify assigned module access (`menu.akses`).
  - Unauthenticated guests must be redirected to login when attempting to access `/admin/*`.
- [ ] **Brute Force & Rate Limiting**:
  - Authentication routes (login, forgot password) must be throttled using Laravel's rate limiters to prevent credential stuffing.
- [ ] **Password Security**:
  - Password hashing must use bcrypt/argon2 with sensible complexity defaults.
  - Reset password tokens must be one-time use, securely random, and time-expiring.

### B. Insecure Direct Object References (IDOR) & Authorization
- [ ] **Scoped Queries & Route Model Binding**:
  - Destruction, update, or cancellation actions on models (e.g. `tagihanListrik`, `keuangan`, `user`) must verify user permission.
  - Superadmin accounts cannot be deleted or demoted by subordinate users.
  - Public lookup features (such as bill checking or receipt tracking) must not leak entire database listings and must enforce specific search criteria.

### C. Injection & Data Integrity
- [ ] **SQL Injection Prevention**:
  - Never concatenate raw unescaped input into SQL queries.
  - Always utilize Eloquent ORM or parameterized query bindings (`?` or named placeholders).
- [ ] **Mass Assignment Protection**:
  - Models must define `$fillable` or `#[Fillable]` explicitly.
  - Sensitive columns (such as `peran`, `status`, `is_superadmin`) must not be mass-assignable from public forms.

### D. Cross-Site Scripting (XSS) & Content Security
- [ ] **Blade Escaping**:
  - Always use `{{ $var }}` for dynamic text interpolation, which automatically escapes HTML via `htmlspecialchars`.
  - Avoid `{!! $var !!}` unless content is strictly sanitized via trusted HTML purifiers.
- [ ] **Safe Attribute Rendering**:
  - Escape attributes in HTML and JavaScript strings using JSON encoding or `e()`.

### E. Cross-Site Request Forgery (CSRF)
- [ ] **State-Changing Requests**:
  - Every `POST`, `PUT`, `PATCH`, and `DELETE` HTTP request from forms must include a valid `@csrf` token.
  - `VerifyCsrfToken` middleware must remain active for web routes.

### F. Information Leakage & Environment Hardening
- [ ] **Production Configuration**:
  - `APP_DEBUG=false` in production.
  - Database credentials, mail passwords, and app keys must be stored in secure environment variables, never hardcoded in source control.
  - System stack traces and SQL query dumps must never be displayed to end-users.

### G. Dependency Auditing
- [ ] **Vulnerability Scans**:
  - Regularly run `composer audit` to scan PHP dependencies for known CVEs.
  - Review flagged advisories and evaluate threat vectors applicable to the runtime architecture.

---

## 3. Verification Protocol

1. **Static Analysis & Code Review**:
   - Inspect all routes in `routes/web.php` and `routes/auth.php`.
   - Verify every route has appropriate authentication and authorization middleware.
2. **Automated Feature & Security Tests**:
   - Write automated tests under `tests/Feature/SecurityAuditTest.php` asserting that:
     - Guests are rejected from protected endpoints.
     - Regular admins are rejected from superadmin management.
     - Public search queries do not return open dumps when unparameterized.
     - SQL wildcard/metacharacters are safely treated as literal search strings.
3. **Execution**:
   - Run `vendor/bin/phpunit` and ensure 100% of security tests pass.

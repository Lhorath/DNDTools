# Manual Test Checklist

Run this checklist after security or auth changes.

## Quick Automated Smoke Test

- Run: `powershell -ExecutionPolicy Bypass -File .\smoke-test.ps1 -BaseUrl "http://localhost"`
- Optional positive login check:
  - `powershell -ExecutionPolicy Bypass -File .\smoke-test.ps1 -BaseUrl "http://localhost" -LoginEmail "you@example.com" -LoginPassword "your-password"`
- CI-friendly report output (JSON + JUnit):
  - `powershell -ExecutionPolicy Bypass -File .\smoke-test-ci.ps1 -BaseUrl "http://localhost" -ReportJsonPath ".\smoke-test-report.json" -ReportJunitPath ".\smoke-test-report.xml"`

## Auth Flows

- Register a new account from `register` and confirm success message appears.
- Login with correct credentials from `login` and confirm redirect to `home`.
- Attempt login with wrong password and confirm error is shown.
- Logout and confirm protected pages (like `profile`) redirect to `login`.

## Profile Flows

- Open `profile` while logged in and verify profile data loads.
- Update first/last/display name and confirm success plus UI updates.
- Change password with valid inputs and confirm success.
- Try change-password with mismatched confirmation and confirm validation error.

## CSRF Protection

- Submit login/register/profile/password requests normally and confirm they succeed.
- In browser devtools, replay one POST request without `X-CSRF-Token` and confirm `403`.
- Replay with an invalid `X-CSRF-Token` and confirm `403`.

## Search and Pagination

- Open `compendium` and verify default category loads results.
- Search for a valid term and verify list results render.
- Test `includes/core/search.php` with `page=0`, `limit=0`, and `limit=999`; confirm stable behavior and bounded pagination.

## Character Sheet + PDF

- Open `sheet` and verify race/class/background dropdowns populate.
- Add a class/race and verify auto-filled fields update as expected.
- Trigger PDF download and verify a file is generated successfully.
- Replay PDF POST without `X-CSRF-Token` and confirm endpoint rejects with `403`.

## Routing and Metadata

- Visit a known route (e.g., `home`, `news`, `profile`) and confirm normal page rendering.
- Visit an unknown route and confirm no PHP warning is shown for missing metadata includes.

# Auth

## Proposal
Si Brahim needs a personal account so his receipts are only visible to him.
Auth gates every route in the app.

## Spec
- Laravel Breeze scaffolding (blade)
- Routes: /register, /login, /logout
- All /recus and /depenses routes protected by auth middleware
- After login → redirect to /recus

## Tasks
- [ ] Install Laravel Breeze
- [ ] Run php artisan breeze:install blade
- [ ] Protect recus and depenses routes with auth middleware
- [ ] Test register, login, logout flow
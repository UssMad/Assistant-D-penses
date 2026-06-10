# AGENTS.md — Assistant Dépenses

## Project
Laravel expense tracking app for a small shopkeeper.
Transforms raw receipt text (Darija) into structured expenses via AI.

## Stack
- Laravel 13, PHP 8.3+
- MySQL
- laravel/ai SDK (Groq provider)
- Laravel Queues (database driver)
- Laravel Breeze (auth)
- Blade templates

## Architecture rules — always follow these
- AI calls go through laravel/ai SDK only — never Http::
- Receipt submission always dispatches a Job — never call AI synchronously
- Validation always happens in a Form Request — never in the controller
- Eloquent casts must be used for enums and json fields
- Every query loading relations must use eager loading — zero N+1

## Commit conventions
- feat: new feature
- fix: bug fix
- docs: documentation only
- refactor: code change without feature
- Always add [AI-assisted] at the end of commits where the agent generated code

## Agent instructions
- Always work in Plan mode before Build mode
- Generate one file at a time
- Never skip validation or error handling
- Always handle the echoue status when AI call fails
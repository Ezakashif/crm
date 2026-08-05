# Contributing

How to contribute safely to this multi-tenant CRM.

## Workflow

1. Create a branch from up-to-date `main`  
   Example: `cursor/short-description-a8c9`
2. Implement with tests
3. Run Pint + tests
4. Open a PR against `main` (non-draft when ready for review)
5. Ensure CI passes once workflows exist ([CI/CD](../operations/cicd.md))

## Before you push

```bash
vendor/bin/pint
php artisan test
# if permissions config changed:
php artisan permissions:sync
```

## PR checklist

- [ ] Tenant isolation considered (CompanyScope / policies)
- [ ] Permissions updated + synced if needed
- [ ] Feature tests for happy path + 403/404 cross-tenant
- [ ] No secrets committed (`.env`, tokens)
- [ ] Docs updated under `/docs` when behavior changes
- [ ] Migrations are MySQL-safe (index name lengths)

## Channels / Meta changes

- Document setup steps in `docs/channels/`
- Fake Graph HTTP in tests
- Note queue worker requirements

## Super Admin vs tenant

- Keep Super Admin routes in `routes/superadmin.php`
- Do not leak platform queries into tenant controllers without `withoutCompanyScope` intent

## Commit messages

Prefer clear imperative subjects:

- `Add Inbox UI with WhatsApp reply support`
- `Fix company settings edit method`

## Related

- [Coding standards](coding-standards.md)
- [Roadmap](../roadmap.md)

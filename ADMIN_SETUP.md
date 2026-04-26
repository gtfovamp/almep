# Admin Panel Setup

## Initial Setup

1. Apply migrations:
```bash
npx wrangler d1 execute almep-db --local --file=migrations/0004_create_admin_auth.sql
npx wrangler d1 execute almep-db --remote --file=migrations/0004_create_admin_auth.sql
```

2. Create admin user (after starting dev server):
```bash
curl -X POST http://localhost:4321/api/admin/create-user \
  -H "Content-Type: application/json" \
  -d '{"username":"fuadadmin2026","password":"fuadadmin2026"}'
```

For production:
```bash
curl -X POST https://your-domain.com/api/admin/create-user \
  -H "Content-Type: application/json" \
  -d '{"username":"fuadadmin2026","password":"fuadadmin2026"}'
```

## Security Notes

- Change default password immediately after first login
- The create-user endpoint should be disabled in production after creating your admin account
- Sessions expire after 7 days
- Cookies are httpOnly, secure, and sameSite=strict

## Admin Panel Access

- Login: `/admin/login`
- Dashboard: `/admin`
- Portfolio: `/admin/portfolio`
- Partners: `/admin/partners`

## Default Credentials

- Username: `fuadadmin2026`
- Password: `fuadadmin2026`

**Note:** These credentials are used for both development and production.

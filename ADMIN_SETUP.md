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
  -d '{"username":"admin","password":"admin123"}'
```

For production, use a strong password:
```bash
curl -X POST https://your-domain.com/api/admin/create-user \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"YOUR_STRONG_PASSWORD"}'
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

## Default Credentials (Development Only)

- Username: `admin`
- Password: `admin123`

**IMPORTANT: Change these credentials in production!**

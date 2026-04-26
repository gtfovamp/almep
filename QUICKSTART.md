# Quick Start Guide

## 1. Start Development Server

```bash
npm run dev
```

## 2. Create Admin User

In a new terminal, run:

```bash
curl -X POST http://localhost:4321/api/admin/create-user \
  -H "Content-Type: application/json" \
  -d '{"username":"fuadadmin2026","password":"fuadadmin2026"}'
```

You should see: `{"success":true}`

## 3. Login to Admin Panel

1. Open http://localhost:4321/admin/login
2. Login with:
   - Username: `fuadadmin2026`
   - Password: `fuadadmin2026`

## 4. Start Managing Content

After login, you'll be redirected to the dashboard where you can:
- Add portfolio projects at `/admin/portfolio`
- Add partners at `/admin/partners`
- Drag and drop to reorder items

## Production Deployment

### 1. Apply Migrations

```bash
npx wrangler d1 execute almep-db --remote --file=migrations/0001_create_portfolio.sql
npx wrangler d1 execute almep-db --remote --file=migrations/0002_seed_portfolio.sql
npx wrangler d1 execute almep-db --remote --file=migrations/0003_create_partners.sql
npx wrangler d1 execute almep-db --remote --file=migrations/0004_create_admin_auth.sql
```

### 2. Deploy to Cloudflare

```bash
npm run deploy
```

### 3. Create Production Admin User

```bash
curl -X POST https://your-domain.com/api/admin/create-user \
  -H "Content-Type: application/json" \
  -d '{"username":"fuadadmin2026","password":"fuadadmin2026"}'
```

### 4. Disable User Creation (Optional)

After creating your admin account, you may want to disable the create-user endpoint by removing or protecting the file:
`src/pages/api/admin/create-user.ts`

## Security Notes

- Sessions expire after 7 days
- Cookies are httpOnly, secure, and sameSite=strict
- Passwords are hashed with bcrypt (10 rounds)
- All admin routes require authentication
- Change default credentials immediately in production

## Troubleshooting

### "Database not available" error
Make sure migrations are applied:
```bash
npx wrangler d1 migrations list almep-db --local
```

### Can't login
1. Check that admin user was created successfully
2. Clear browser cookies
3. Check browser console for errors

### Images not showing
1. Verify KV namespace is configured in wrangler.jsonc
2. Check that images are being uploaded (check Network tab)
3. Verify image URLs in database

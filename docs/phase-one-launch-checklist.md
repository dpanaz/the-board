# The Board — Phase 1 launch checklist

**Separate application:** deploy this repository, never `clubhouse-app-v2`. The legacy Board prototype in Clubhouse PR #142 must remain unmerged and be closed after migration is accepted.

## Infrastructure (must be independently provisioned)
- [ ] Dedicated PHP 8.4 hosting service with web root `public/`; HTTPS for `theboard.clubhouseoncastell.com`.
- [ ] Dedicated MySQL database `the_board` and user, with no grants to Clubhouse databases.
- [ ] Independent `APP_KEY`, `SESSION_COOKIE=the_board_session`, `SESSION_DOMAIN=theboard.clubhouseoncastell.com`; **do not reuse Clubhouse keys, sessions or secrets**.
- [ ] Production `APP_ENV=production`, `APP_DEBUG=false`, unique mail credentials and real `MAIL_FROM_ADDRESS`.
- [ ] Generate VAPID keypair and set `WEBPUSH_VAPID_PUBLIC_KEY`, `WEBPUSH_VAPID_PRIVATE_KEY`, `WEBPUSH_VAPID_SUBJECT`.
- [ ] Ensure independent writable `storage/` and `bootstrap/cache/`; separate logs.
- [ ] Cron for `php artisan schedule:run` every minute, in the **Board service only**.
- [ ] Enable backups, error monitoring, rate limiting and HTTPS-only cookies.

## Deployment sequence
1. Commit the generated and reviewed `composer.lock` from a clean Composer resolution; then deploy using `composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader`. Never run `composer update` during production deployment.
2. Configure environment variables and ensure application points **only** to the Board database.
3. Run `php artisan migrate --force` against the Board database, not the Clubhouse database.
4. Run `php artisan board:create-admin admin@example.com --name="Board Administrator"` and securely transfer/rotate the temporary password.
5. Verify invitation, approval, sign-in, chat, RSVP, referral, asks/offers, push preferences, real-device test push, and scheduled meeting reminders.
6. Check that Board routes do not respond on the Clubhouse hostname and that the Clubhouse app remains unchanged.

## Phase 1 acceptance gates
- [ ] Dedicated repository and Board-only Laravel runtime
- [ ] No shared Clubhouse database, auth, session, cache, queue or scheduler
- [ ] Clean Board feature tests (no warnings) and migration verification
- [ ] Reproducible `composer.lock` checked into this repository
- [ ] Secure invitation-only membership, admin approval and role authorization
- [ ] Directory, direct/group chat, meetings/RSVPs, referrals and asks/offers
- [ ] Web push on supported Android/iPhone installations, with VAPID configured and delivery tested on actual devices
- [ ] Independent production deployment, DNS/TLS and rollback/backup plan

**Current status:** source migration and CI are in progress; do not mark Phase 1 complete until all gates are verified.

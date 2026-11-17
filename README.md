# The Board

Independent, invitation-only business-owner community application.

## Isolation requirements

- Standalone Laravel application and GitHub repository.
- Dedicated database and database credentials; no Clubhouse tables or data connections.
- Dedicated sessions, authentication, queues, cache and scheduler.
- Separate CI, environment secrets, deployment and domain (`theboard.clubhouseoncastell.com`).
- No code, migrations, routes, Composer dependencies or tests added to `clubhouse-app-v2` for Board features.

## Migration status

The original prototype remains in `dpanaz/clubhouse-app-v2`, branch `feature/the-board-phase-1`, PR #142 (unmerged). Its code must be extracted and adapted to this standalone app before deployment. Do not merge PR #142.

## Deployment

Not configured. Do not point production traffic at this repository until standalone infrastructure and tests are verified.

# Campaign Compendium

> An all-in-one TTRPG campaign management system — built for Dungeon Masters and players alike.

Campaign Compendium is a web application for managing tabletop RPG campaigns. It ships with the full D&D 5e SRD dataset (items, weapons, armor, spells, monsters, and more) and gives DMs and players tools to build campaigns, track quests, manage NPCs, create characters, and run encounters — all in one place.

This is also a learning project, built to explore Laravel while creating something genuinely useful.

---

## Tech Stack

- **Framework:** Laravel 12 (PHP 8.2+)
- **Frontend:** Blade templates, Tailwind CSS v3, Alpine.js
- **Database:** SQLite (development), compatible with MySQL/PostgreSQL
- **Auth:** Laravel Breeze
- **Build:** Vite

---

## Current Features

### ✅ Authentication & User Management

- Registration, login, email verification, and password reset via Laravel Breeze
- User profiles with editable display name and email
- Light and dark mode with a full semantic CSS variable theme system

### ✅ Super Admin Panel

- Protected `/admin` route restricted to `is_super_admin` users
- User management: search, filter by role/status, suspend and restore accounts
- "Sign in as user" impersonation flow with secure session restore
- Activity log: append-only record of all admin actions (edits, suspensions, impersonation, notification events), filterable by event type, admin, and date range
- System notifications: broadcast messages to users via inbox fan-out, sitewide dismissible banners, or both; supports `show_at` scheduling and `expires_at` expiry

### ✅ Campaigns

- Create, edit, and delete campaigns
- Invite members by email with token-based campaign invite links
- Existing users can accept from their inbox; new users can register from the invite and join automatically
- Campaign roles (DM, Player) on the membership pivot
- Campaign detail view with tabbed sections for overview, sessions, quests, and NPCs

### ✅ Quests

- Full CRUD for quests, nested under campaigns
- Quest status tracking with active, planned, completed, failed, and abandoned states
- DM notes field on quests
- Attach and detach NPCs from quests
- Quest detail view with associated NPC list

### ✅ NPCs

- Full CRUD for custom NPCs
- Associate NPCs with campaigns and quests

### ✅ Session Logs

- Create, edit, and delete session logs nested under campaigns
- Attach campaign quests and NPCs directly from the create/edit session flow
- Track which NPCs appeared and which quests advanced during a session
- Optional private audio recording upload and playback for each session log

### ✅ Items & Equipment (SRD)

- Full SRD item dataset seeded: items, weapons, armor, categories, rarities, damage types
- Browse the SRD compendium (public, no login required)
- Clone any SRD item to create a custom version with editable stats
- Soft-delete support for custom items

### ✅ Public SRD Reference & Tools

- Polished public homepage and about page
- Public SRD rules reference with searchable sections and conditions
- Public SRD spell lookup with filters for class, level, school, and casting time
- Public SRD monster bestiary with CR, type, and size filters plus full statblocks
- Public dice roller
- Public encounter generator with XP math, suggestion presets, candidate browsing, and saved encounters for authenticated users

### ✅ Notifications

- Bell icon in the navbar with unread count indicator
- Tabbed notifications index: campaign invites and system messages
- Mark all as read; inline accept/decline for campaign invites
- System notification inbox with type-colored icons and responsive desktop/mobile layouts

---

## Roadmap

The roadmap is organized into rough phases. Items within a phase are not necessarily in priority order — they'll be sequenced as development continues.

---

### Phase 2 — Public Presence & Free Tools

The goal of this phase is to give non-registered users a reason to visit and explore the app, and to build awareness of the authenticated features.

- [x] **Polished homepage** — hero section, feature highlights, clear CTAs for sign-up and SRD exploration
- [x] **About page** — project description, tech stack, link to GitHub
- [x] **SRD Rules Lookup** — searchable public reference for conditions, actions, abilities, and rules; no login required
- [x] **Spell Lookup** — full SRD spell list with filtering by class, level, school, and casting time; public
- [x] **Monster/Creature Lookup** — SRD bestiary with CR and type filters and statblock display; public
- [x] **Dice Roller** — accessible, animated multi-dice roller (d4–d100); public tool
- [x] **Encounter Difficulty Calculator / Generator** — input party size/level and monster CR values to get XP thresholds and difficulty rating, with encounter suggestions and authenticated save support
- [ ] **Random Generators** — quick public tools: NPC name generator, random weather, random town events

---

### Phase 3 — Campaign Compendium Core

Deepening the campaign experience to make this feel like a real campaign management tool.

- [x] **Quest status tracking** — active, completed, failed, and abandoned states with notes/description fields
- [x] **Session Logs** — create session records tied to a campaign; log which NPCs appeared, quests advanced, and freeform DM notes
- [ ] **Campaign Notes / Journal** — freeform rich-text notes attached to a campaign, visible to DM and optionally to players
- [ ] **Locations** — create named locations (towns, dungeons, regions) within a campaign; attach NPCs, quests, and notes to them
- [ ] **Factions & Organizations** — define factions within a campaign; associate NPCs and locations; track player reputation
- [ ] **Campaign dashboard** — at-a-glance overview: active quests, recent sessions, party members, and upcoming encounters
- [ ] **DM vs. Player views** — DMs see full information; players see only what their character would know (hidden notes, secret NPCs, etc.)

---

### Phase 4 — Encounters

Encounters as a first-class feature, bridging campaign management and the live table.

- [x] **Encounter model** — saved encounters generated from the public tool, with user-owned encounter snapshots
- [ ] **Add monsters to encounters** — attach SRD creatures or custom NPCs with quantity and initiative modifier
- [ ] **Initiative tracker** — at-the-table tool for managing turn order, HP, and conditions during a live encounter
- [ ] **Encounter CR calculator** — automatic difficulty rating based on party level and monster CR values
- [ ] **Encounter notes & outcomes** — record what happened, loot discovered, and XP awarded

---

### Phase 5 — Player Characters

The character builder is the biggest planned feature — a guided tool for players to create and manage characters using SRD rules and custom content.

- [ ] **SRD Races & Classes** — seed the full SRD race/species and class dataset with traits, features, and proficiencies
- [ ] **SRD Backgrounds & Feats** — seed backgrounds and feats as selectable options during character creation
- [ ] **Ability Scores** — support standard array, point buy, and manual entry methods
- [ ] **Character creation wizard** — guided multi-step flow: race/species → class → background → ability scores → equipment → review (inspired by Roll20's Charactermancer)
- [ ] **Character sheet** — full read/edit sheet: stats, skills, saving throws, HP, AC, speed, spellcasting, inventory, proficiencies, and traits
- [ ] **Level up flow** — guided level-up with class feature prompts, HP roll or average choice, and new spell selection
- [ ] **Spellcasting** — spell slot tracking, prepared spell list, and concentration tracking
- [ ] **Inventory management** — equip/unequip items, encumbrance tracking, currency
- [ ] **Campaign association** — players link characters to campaigns; DMs can view party character sheets
- [ ] **Character export** — printable/PDF character sheet

---

### Phase 6 — Content Generation & Advanced Tools

- [ ] **Random encounter tables** — campaign-configurable tables for wilderness, dungeon, and urban encounters; rollable in-app
- [ ] **Treasure generator** — roll by CR; individual and hoard tables from the SRD
- [ ] **NPC generator** — random NPC with name, race/species, class, personality traits, ideal, bond, flaw, and appearance
- [ ] **AI content generation** — generate plot hooks, item descriptions, NPC backstories, and location descriptions via API integration
- [ ] **Campaign export** — export a full campaign summary (NPCs, quests, sessions, locations) as a PDF

---

## Local Development Setup

```bash
# Clone the repo
git clone https://github.com/kmfranklin/campaign-compendium.git
cd campaign-compendium

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Create and migrate the database
touch database/database.sqlite
php artisan migrate

# Seed SRD data
php artisan db:seed

# Start the development server (runs Laravel, queue worker, Pail, and Vite concurrently)
composer run dev
```

The app will be available at `http://localhost:8000`.

### Local Email Development

By default, the app uses Laravel's `log` mailer in development, so outgoing mail is written to your logs instead of being delivered.

If you want to test the real invite flow locally, Mailpit is the easiest setup:

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS=noreply@campaigncompendium.test
MAIL_FROM_NAME="Campaign Compendium"
```

With Mailpit running, invite emails will appear in the Mailpit inbox UI. Invite delivery is queued, so make sure a queue worker is running locally. `composer run dev` already starts one for you.

### Production Email on Forge

For production or staging, use a real transactional mail provider such as Postmark, Mailgun, SES, or SMTP from a trusted provider.

Recommended deployment checklist:

1. Set the mail environment variables in Forge.
2. Configure a queue worker on the server so queued mail is processed.
3. Use a real sender domain such as `noreply@campaigncompendium.com`.
4. Add the provider's DNS records for SPF and DKIM, and ideally DMARC as well.

Example Postmark configuration:

```env
MAIL_MAILER=postmark
POSTMARK_TOKEN=your-postmark-server-token
MAIL_FROM_ADDRESS=noreply@campaigncompendium.com
MAIL_FROM_NAME="Campaign Compendium"
```

Example SMTP configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_SCHEME=tls
MAIL_FROM_ADDRESS=noreply@campaigncompendium.com
MAIL_FROM_NAME="Campaign Compendium"
```

---

## Contributing

This is a personal learning project and is not currently open to outside contributions. Feedback, suggestions, and bug reports via [GitHub Issues](https://github.com/kmfranklin/campaign-compendium/issues) are very welcome.

---

## License

[MIT](LICENSE)

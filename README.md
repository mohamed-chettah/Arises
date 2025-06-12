# Arises

**Arises** is a minimalist AI productivity assistant that connects to your calendar and helps you plan smarter. With a simple chat interface, Arises understands your goals and proposes optimized planning suggestions.

Inspired by RPG-leveling systems, Arises aims to transform productivity into an engaging, gamified journey — starting with one powerful feature: your AI-powered calendar assistant.

## ✨ What Can You Do Today?

- 💬 **Talk to your AI assistant**  
  Tell Arises what you want to do ("I want to learn React", "Plan gym sessions", etc.).

- 📆 **Connect your calendar**  
  Arises integrates with your Google Calendar (Outlook coming soon).

- 📋 **Receive smart planning suggestions**  
  The AI proposes learning slots, breaks, and routines directly in your calendar — tailored to your goals.

## ⚙️ Tech Stack

- **Frontend**: Nuxt 3 (SSR)
- **Backend**: Laravel (REST API)
- **Database**: PostgreSQL - Neon
- **AI**: OpenAI GPT
- **Auth**: Laravel JWT
- **Infra**: Docker + GitHub Actions CI/CD

## 🛣️ Roadmap (Next Features)

- [x] AI chat connected to calendar (Google)
- [ ] Outlook Calendar integration
- [ ] Multi-goal management (Pro plan)
- [ ] AI Quest planner with daily challenges
- [ ] XP and rank system (E → S)
- [x] Distraction blocking tools
- [ ] Smart time merge system (accept/modify/reject suggestions)
- [ ] Sync with task managers (Notion, Todoist, etc.)
- [ ] Mentoring & community features

## 🧪 Local Development

```bash
# Clone the repo
git clone https://github.com/mohamedevweb/arises.git

# Frontend
cd frontend
npm install
npm run dev

# Backend
cd backend
docker compose up

## 🛠️ Environment Variables

Before running the project, make sure to configure your environment variables.  
👉 Go check the `.env.example` file and copy it to `.env` with your own credentials.

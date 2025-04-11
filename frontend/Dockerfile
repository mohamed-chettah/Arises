# ---- STAGE 1 : BUILD ----
FROM node:20-alpine AS builder

WORKDIR /app

# Copier uniquement les fichiers relatifs aux dépendances
COPY package*.json ./

# Installer toutes les dépendances (y compris devDependencies)
RUN npm install

# Copier l'intégralité du code
COPY . .

# Construire le projet Nuxt
RUN npm run build

# ---- STAGE 2 : RUNTIME ----
FROM node:20-alpine

WORKDIR /app

# Copier le dossier .output (résultat du build Nuxt) depuis l'étape builder
COPY --from=builder /app/.output ./.output

# Copier le node_modules si tu as besoin de dépendances au runtime (pas toujours nécessaire pour Nuxt 3)
COPY --from=builder /app/node_modules ./node_modules

# Copier les fichiers de config (package.json, etc.) si besoin
COPY --from=builder /app/package*.json ./

# On indique qu'on est en environnement de production
ENV NODE_ENV=production

EXPOSE 3000

CMD ["node", ".output/server/index.mjs"]

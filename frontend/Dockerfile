# Utilisation de l'image Node.js
FROM node:20-alpine

# Définition du dossier de travail
WORKDIR /app

# Copier les fichiers package.json et package-lock.json
COPY package*.json ./

# Installer les dépendances
RUN npm install

# Copier tout le projet
COPY . .

# Exposer le port utilisé par Nuxt
EXPOSE 3000

# Lancer Nuxt
CMD ["npm", "run", "dev"]

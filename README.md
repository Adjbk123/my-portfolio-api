# Portfolio API

API REST pour un portfolio professionnel développée avec Symfony 7 et JWT Authentication.

## 🚀 Fonctionnalités

### **Gestion de contenu**
- ✅ **Projets** - Portfolio de projets avec galerie d'images
- ✅ **Témoignages** - Avis clients avec avatars
- ✅ **Expériences** - Parcours professionnel
- ✅ **Articles de blog** - Contenu éditorial
- ✅ **Compétences** - Technologies maîtrisées
- ✅ **Profil** - Informations personnelles et CV

### **Système d'authentification**
- ✅ **JWT Authentication** - Tokens valides 24h
- ✅ **Inscription/Connexion** - Gestion des utilisateurs
- ✅ **Routes publiques/privées** - Sécurité optimisée

### **Fonctionnalités avancées**
- ✅ **Upload de fichiers** - Images et documents
- ✅ **Système de contact** - Messages et demandes de projets
- ✅ **Sérialisation** - API REST complète
- ✅ **Validation** - Données sécurisées

## 🛠️ Installation

### **Prérequis**
- PHP 8.2+
- Composer
- MySQL/MariaDB
- Symfony CLI (optionnel)

### **Installation**
```bash
# Cloner le projet
git clone https://github.com/Adjbk123/my-portfolio-api.git
cd my-portfolio-api

# Installer les dépendances
composer install

# Configurer la base de données
# Créer un fichier .env.local avec vos paramètres
DATABASE_URL="mysql://user:password@127.0.0.1:3306/portfolio_api"

# Générer les clés JWT
php bin/console lexik:jwt:generate-keypair

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Démarrer le serveur
symfony server:start
# ou
php -S localhost:8000 -t public
```

## 📚 Documentation API

### **Routes publiques (sans authentification)**
- `GET /api/projets` - Liste des projets
- `GET /api/projets/{id}` - Détail d'un projet
- `GET /api/temoignages` - Liste des témoignages
- `GET /api/experiences` - Liste des expériences
- `GET /api/articles` - Liste des articles
- `GET /api/competences` - Liste des compétences
- `POST /api/contact/send` - Envoi de message
- `GET /api/profil/public` - Profil public

### **Routes d'authentification**
- `POST /api/auth/login` - Connexion
- `POST /api/auth/register` - Inscription
- `POST /api/auth/logout` - Déconnexion

### **Routes privées (avec token JWT)**
- `POST /api/projets` - Créer un projet
- `PUT /api/projets/{id}` - Modifier un projet
- `DELETE /api/projets/{id}` - Supprimer un projet
- `GET /api/auth/me` - Profil utilisateur connecté
- Et toutes les autres routes d'administration...

## 🔐 Authentification

### **Connexion**
```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "motdepasse123"
}
```

### **Réponse**
```json
{
  "success": true,
  "message": "Connexion réussie",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": 1,
    "email": "admin@example.com",
    "nom": "Dupont",
    "prenom": "Jean",
    "roles": ["ROLE_ADMIN", "ROLE_USER"]
  }
}
```

### **Utilisation du token**
```bash
GET /api/projets
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

## 📁 Structure du projet

```
src/
├── Controller/          # Contrôleurs API
├── Entity/             # Entités Doctrine
├── Repository/         # Repositories
├── Service/            # Services métier
└── EventListener/      # Event listeners JWT

config/
├── packages/           # Configuration des bundles
└── jwt/               # Clés JWT (générées automatiquement)

public/
└── uploads/           # Fichiers uploadés
```

## 🔧 Configuration

### **Variables d'environnement**
```env
# Base de données
DATABASE_URL="mysql://user:password@127.0.0.1:3306/portfolio_api"

# JWT (généré automatiquement)
JWT_PASSPHRASE=""

# Email
MAILER_DSN="smtp://user:pass@smtp.example.com:587"
```

### **Sécurité**
- Routes publiques : Accès libre
- Routes privées : Token JWT requis (24h)
- Upload de fichiers : Validation des types et tailles
- Validation des données : Contraintes Symfony

## 🚀 Déploiement

### **Production**
```bash
# Optimiser l'autoloader
composer install --no-dev --optimize-autoloader

# Vider le cache
php bin/console cache:clear --env=prod

# Générer les clés JWT
php bin/console lexik:jwt:generate-keypair

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction
```

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 👨‍💻 Auteur

**Armand Adjibako**
- GitHub: [@Adjbk123](https://github.com/Adjbk123)
- Email: adjibako123@gmail.com

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commit vos changements
4. Push vers la branche
5. Ouvrir une Pull Request

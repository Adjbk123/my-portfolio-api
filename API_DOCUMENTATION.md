# 📚 API Documentation - Portfolio Armand Adjibako

## 🎯 **Base URL**
```
http://localhost:8000/api
```

## 🔐 **Authentification**
Tous les endpoints admin nécessitent un token d'authentification dans le header :
```
Authorization: Bearer YOUR_TOKEN
```

---

## 📊 **1. PROFIL**

### **GET** `/profil`
Récupérer les informations du profil

**Réponse :**
```json
{
  "id": 1,
  "nom": "Armand S. ADJIBAKO",
  "email": "contact@armandadjibako.com",
  "telephone": "+229 97 00 00 00",
  "localisation": "Akpakpa, Cotonou, Bénin",
  "biographie": "Développeur Full Stack passionné...",
  "avatar": "/uploads/avatars/photo.jpg",
  "cvUrl": "/uploads/documents/cv.pdf",
  "liensSociaux": {
    "github": "https://github.com/armand",
    "linkedin": "https://linkedin.com/in/armand",
    "twitter": "https://twitter.com/armand"
  },
  "createdAt": "2025-01-01T12:00:00+00:00",
  "updatedAt": "2025-01-01T12:00:00+00:00"
}
```

### **POST** `/profil` (Admin)
Créer le profil

**Body :**
```json
{
  "nom": "Armand S. ADJIBAKO",
  "email": "contact@armandadjibako.com",
  "telephone": "+229 97 00 00 00",
  "localisation": "Akpakpa, Cotonou, Bénin",
  "biographie": "Développeur Full Stack...",
  "avatar": "/uploads/avatars/photo.jpg",
  "cvUrl": "/uploads/documents/cv.pdf",
  "liensSociaux": {
    "github": "https://github.com/armand",
    "linkedin": "https://linkedin.com/in/armand"
  }
}
```

### **PUT** `/profil` (Admin)
Modifier le profil

**Body :** Même structure que POST (champs optionnels)

---

## 🎨 **2. PROJETS**

### **GET** `/projets`
Liste des projets

**Paramètres :**
- `?statut=publie` - Filtrer par statut (publie, brouillon, archive)
- `?categorie=Web` - Filtrer par catégorie
- `?en_vedette=true` - Projets en vedette uniquement
- `?page=1&limit=10` - Pagination

**Réponse :**
```json
[
  {
    "id": 1,
    "titre": "Site E-commerce",
    "description": "Site de vente en ligne moderne",
    "descriptionComplete": "Description détaillée du projet...",
    "categorie": "Web",
    "imagePrincipale": "/uploads/images/projet1.jpg",
    "galerie": [
      "/uploads/images/projet1-1.jpg",
      "/uploads/images/projet1-2.jpg"
    ],
    "technologies": ["React", "Node.js", "MongoDB"],
    "fonctionnalites": ["Panier", "Paiement", "Gestion stocks"],
    "duree": "3 mois",
    "client": "Client ABC",
    "lienGithub": "https://github.com/armand/projet1",
    "lienProjet": "https://projet1.com",
    "statut": "publie",
    "enVedette": true,
    "createdAt": "2025-01-01T12:00:00+00:00",
    "updatedAt": "2025-01-01T12:00:00+00:00"
  }
]
```

### **GET** `/projets/{id}`
Détails d'un projet

### **POST** `/projets` (Admin)
Créer un projet

**Body :**
```json
{
  "titre": "Nouveau Projet",
  "description": "Description courte",
  "descriptionComplete": "Description complète",
  "categorie": "Web",
  "imagePrincipale": "/uploads/images/image.jpg",
  "galerie": ["/uploads/images/img1.jpg", "/uploads/images/img2.jpg"],
  "technologies": ["React", "Node.js"],
  "fonctionnalites": ["Fonctionnalité 1", "Fonctionnalité 2"],
  "duree": "2 mois",
  "client": "Client XYZ",
  "lienGithub": "https://github.com/user/repo",
  "lienProjet": "https://projet.com",
  "statut": "publie",
  "enVedette": false
}
```

### **PUT** `/projets/{id}` (Admin)
Modifier un projet

### **DELETE** `/projets/{id}` (Admin)
Supprimer un projet

---

## 📝 **3. ARTICLES DE BLOG**

### **GET** `/articles`
Liste des articles

**Paramètres :**
- `?statut=publie` - Filtrer par statut
- `?categorie=developpement` - Filtrer par catégorie (slug)
- `?tag=react` - Filtrer par tag (slug)
- `?page=1&limit=10` - Pagination

**Réponse :**
```json
[
  {
    "id": 1,
    "titre": "Introduction à React",
    "slug": "introduction-react",
    "extrait": "Découvrez React en 10 minutes...",
    "contenu": "Contenu complet de l'article...",
    "imagePrincipale": "/uploads/images/article1.jpg",
    "auteur": {
      "id": 1,
      "nom": "Armand",
      "email": "contact@armandadjibako.com"
    },
    "statut": "publie",
    "datePublication": "2025-01-01T12:00:00+00:00",
    "nombreVues": 150,
    "categories": [
      {
        "id": 1,
        "nom": "Développement Web",
        "slug": "developpement-web",
        "couleur": "#80a729"
      }
    ],
    "tags": [
      {
        "id": 1,
        "nom": "React",
        "slug": "react",
        "couleur": "#1c3f39"
      }
    ],
    "createdAt": "2025-01-01T12:00:00+00:00",
    "updatedAt": "2025-01-01T12:00:00+00:00"
  }
]
```

### **GET** `/articles/{id}`
Détails d'un article (incrémente les vues)

### **GET** `/articles/slug/{slug}`
Article par slug (incrémente les vues)

### **POST** `/articles` (Admin)
Créer un article

**Body :**
```json
{
  "titre": "Nouvel Article",
  "slug": "nouvel-article",
  "extrait": "Extrait de l'article...",
  "contenu": "Contenu complet...",
  "imagePrincipale": "/uploads/images/article.jpg",
  "statut": "publie",
  "datePublication": "2025-01-01T12:00:00+00:00",
  "categories": ["developpement-web", "design"],
  "tags": ["react", "javascript"]
}
```

### **PUT** `/articles/{id}` (Admin)
Modifier un article

### **DELETE** `/articles/{id}` (Admin)
Supprimer un article

---

## 💼 **4. SERVICES**

### **GET** `/services`
Liste des services

**Paramètres :**
- `?actif=true` - Services actifs uniquement

**Réponse :**
```json
[
  {
    "id": 1,
    "titre": "Développement Web",
    "description": "Création de sites web modernes et responsives",
    "icone": "fas fa-code",
    "fonctionnalites": [
      "Sites vitrines",
      "E-commerce",
      "Applications web"
    ],
    "gammePrix": "500€ - 5000€",
    "ordreAffichage": 1,
    "actif": true,
    "createdAt": "2025-01-01T12:00:00+00:00"
  }
]
```

### **GET** `/services/{id}`
Détails d'un service

### **POST** `/services` (Admin)
Créer un service

**Body :**
```json
{
  "titre": "Design UI/UX",
  "description": "Design d'interfaces modernes",
  "icone": "fas fa-paint-brush",
  "fonctionnalites": ["Wireframes", "Prototypes", "Design System"],
  "gammePrix": "300€ - 2000€",
  "ordreAffichage": 2,
  "actif": true
}
```

### **PUT** `/services/{id}` (Admin)
Modifier un service

### **DELETE** `/services/{id}` (Admin)
Supprimer un service

---

## 💼 **5. EXPÉRIENCES PROFESSIONNELLES**

### **GET** `/experiences`
Liste des expériences

**Paramètres :**
- `?actif=true` - Expériences actives uniquement
- `?entreprise=NomEntreprise` - Filtrer par entreprise

**Réponse :**
```json
[
  {
    "id": 1,
    "periode": "2021 - Present",
    "entreprise": "Themeforest Market",
    "poste": "Web Designer",
    "ordreAffichage": 1,
    "actif": true,
    "createdAt": "2025-01-01T12:00:00+00:00",
    "updatedAt": "2025-01-01T12:00:00+00:00"
  }
]
```

### **GET** `/experiences/{id}`
Détails d'une expérience

### **POST** `/experiences` (Admin)
Créer une expérience

**Body :**
```json
{
  "periode": "2023 - 2024",
  "entreprise": "Ma Société",
  "poste": "Développeur Full Stack",
  "ordreAffichage": 2,
  "actif": true
}
```

### **PUT** `/experiences/{id}` (Admin)
Modifier une expérience

### **DELETE** `/experiences/{id}` (Admin)
Supprimer une expérience

### **PUT** `/experiences/{id}/toggle` (Admin)
Activer/Désactiver une expérience

### **PUT** `/experiences/reorder` (Admin)
Réorganiser l'ordre des expériences

**Body :**
```json
{
  "experiences": [
    {"id": 1, "ordre_affichage": 1},
    {"id": 2, "ordre_affichage": 2},
    {"id": 3, "ordre_affichage": 3}
  ]
}
```

---

## 💬 **6. TÉMOIGNAGES**

### **GET** `/temoignages`
Liste des témoignages

**Paramètres :**
- `?en_vedette=true` - Témoignages en vedette
- `?page=1&limit=10` - Pagination

**Réponse :**
```json
[
  {
    "id": 1,
    "nomClient": "Jean Dupont",
    "posteClient": "CEO",
    "entrepriseClient": "Startup ABC",
    "avatarClient": "/uploads/avatars/client1.jpg",
    "contenu": "Excellent travail, très professionnel...",
    "note": 5,
    "enVedette": true,
    "createdAt": "2025-01-01T12:00:00+00:00"
  }
]
```

### **GET** `/temoignages/{id}`
Détails d'un témoignage

### **POST** `/temoignages` (Admin)
Créer un témoignage

**Body :**
```json
{
  "nomClient": "Marie Martin",
  "posteClient": "Directrice Marketing",
  "entrepriseClient": "Entreprise XYZ",
  "avatarClient": "/uploads/avatars/client2.jpg",
  "contenu": "Service impeccable, je recommande !",
  "note": 5,
  "enVedette": false
}
```

### **PUT** `/temoignages/{id}` (Admin)
Modifier un témoignage

### **DELETE** `/temoignages/{id}` (Admin)
Supprimer un témoignage

---

## 📧 **7. CONTACT**

### **POST** `/contact/send`
Envoyer un message de contact

**Body :**
```json
{
  "nom_expediteur": "Jean Dupont",
  "email_expediteur": "jean@example.com",
  "sujet": "Demande de renseignements",
  "message": "Bonjour, j'aimerais discuter d'un projet..."
}
```

**Réponse :**
```json
{
  "id": 1,
  "nom_expediteur": "Jean Dupont",
  "email_expediteur": "jean@example.com",
  "sujet": "Demande de renseignements",
  "message": "Bonjour, j'aimerais discuter d'un projet...",
  "statut": "nouveau",
  "createdAt": "2025-01-01T12:00:00+00:00",
  "email_sent": true,
  "confirmation_sent": true
}
```

### **POST** `/contact/project-inquiry`
Envoyer une demande de projet

**Body :**
```json
{
  "nom": "Marie Martin",
  "email": "marie@company.com",
  "entreprise": "Ma Société",
  "type_projet": "Site web e-commerce",
  "budget": "5000-10000€",
  "date_souhaitee": "Mars 2025",
  "description": "Je souhaite créer un site e-commerce..."
}
```

**Réponse :**
```json
{
  "success": true,
  "message": "Demande de projet envoyée avec succès",
  "email_sent": true,
  "confirmation_sent": true
}
```

### **GET** `/contact/messages` (Admin)
Liste des messages reçus

**Paramètres :**
- `?statut=nouveau` - Filtrer par statut (nouveau, lu, repondu, archive)
- `?page=1&limit=10` - Pagination

### **GET** `/contact/messages/{id}` (Admin)
Détails d'un message

### **PUT** `/contact/messages/{id}/status` (Admin)
Changer le statut d'un message

**Body :**
```json
{
  "statut": "lu"
}
```

### **DELETE** `/contact/messages/{id}` (Admin)
Supprimer un message

---

## 📁 **8. UPLOAD DE FICHIERS**

### **POST** `/upload/image`
Uploader une image

**Body :** FormData avec champ `image`

**Réponse :**
```json
{
  "success": true,
  "filename": "unique_id.jpg",
  "url": "/uploads/images/unique_id.jpg",
  "size": 1024000,
  "mime_type": "image/jpeg",
  "original_name": "photo.jpg"
}
```

### **POST** `/upload/document`
Uploader un document

**Body :** FormData avec champ `document`

**Réponse :**
```json
{
  "success": true,
  "filename": "unique_id.pdf",
  "url": "/uploads/documents/unique_id.pdf",
  "size": 2048000,
  "mime_type": "application/pdf",
  "original_name": "cv.pdf"
}
```

### **DELETE** `/upload/delete`
Supprimer un fichier

**Body :**
```json
{
  "path": "/uploads/images/old_image.jpg"
}
```

---

## 🔐 **9. AUTHENTIFICATION**

### **POST** `/auth/register` (Admin)
Inscription d'un administrateur

**Body :**
```json
{
  "email": "admin@armandadjibako.com",
  "nom": "Armand",
  "prenom": "Adjibako",
  "password": "motdepasse123"
}
```

### **POST** `/auth/login`
Connexion

**Body :**
```json
{
  "email": "admin@armandadjibako.com",
  "password": "motdepasse123"
}
```

### **GET** `/auth/me`
Profil de l'utilisateur connecté

**Réponse :**
```json
{
  "id": 1,
  "email": "admin@armandadjibako.com",
  "nom": "Armand",
  "prenom": "Adjibako",
  "roles": ["ROLE_USER", "ROLE_ADMIN"],
  "createdAt": "2025-01-01T12:00:00+00:00",
  "updatedAt": "2025-01-01T12:00:00+00:00"
}
```

### **POST** `/auth/logout`
Déconnexion

### **POST** `/auth/change-password`
Changer le mot de passe

**Body :**
```json
{
  "old_password": "ancienmotdepasse",
  "new_password": "nouveaumotdepasse"
}
```

---

## 🚨 **CODES D'ERREUR**

### **Codes de statut HTTP :**
- **200** - Succès
- **201** - Créé avec succès
- **400** - Données invalides
- **401** - Non authentifié
- **403** - Accès refusé
- **404** - Ressource non trouvée
- **500** - Erreur serveur

### **Format des erreurs :**
```json
{
  "error": "Message d'erreur",
  "details": "Détails supplémentaires"
}
```

### **Erreurs de validation :**
```json
{
  "errors": "Le champ 'titre' est requis"
}
```

---

## 🎨 **TYPES DE FICHIERS AUTORISÉS**

### **Images :**
- **Types :** JPEG, PNG, GIF, WebP
- **Taille max :** 5MB

### **Documents :**
- **Types :** PDF, DOC, DOCX
- **Taille max :** 10MB

---

## 🔧 **CONFIGURATION CORS**

L'API accepte les requêtes depuis :
- `http://localhost:3000` (React)
- `http://localhost:5173` (Vite)
- `http://127.0.0.1:3000`
- `http://127.0.0.1:5173`

---

## 📝 **NOTES IMPORTANTES**

1. **Authentification :** Les endpoints marqués "(Admin)" nécessitent une authentification
2. **Pagination :** Par défaut, 10 éléments par page
3. **Filtres :** Tous les paramètres de filtrage sont optionnels
4. **Timestamps :** Tous les timestamps sont au format ISO 8601
5. **Upload :** Utilisez FormData pour les uploads de fichiers
6. **Validation :** Tous les champs requis sont validés côté serveur

---

## 🚀 **EXEMPLES D'UTILISATION**

### **Frontend React :**
```javascript
// Récupérer les projets publiés
const projets = await fetch('/api/projets?statut=publie&en_vedette=true')
  .then(res => res.json());

// Envoyer un message de contact
const message = await fetch('/api/contact/send', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    nom_expediteur: 'Jean Dupont',
    email_expediteur: 'jean@example.com',
    sujet: 'Demande de projet',
    message: 'Bonjour, j\'aimerais...'
  })
}).then(res => res.json());
```

### **Upload d'image :**
```javascript
const formData = new FormData();
formData.append('image', fileInput.files[0]);

const upload = await fetch('/api/upload/image', {
  method: 'POST',
  body: formData
}).then(res => res.json());
```

---

**Version :** 1.0  
**Dernière mise à jour :** Janvier 2025  
**Contact :** contact@armandadjibako.com

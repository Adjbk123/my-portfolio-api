# 📝 Documentation Blog - API Portfolio

## 📋 Vue d'ensemble

Le système de blog comprend plusieurs entités interconnectées pour gérer les articles, catégories, et tags.

## 🗂️ Entités du Blog

### 1. **ArticlesBlog**
Gestion des articles de blog avec système de publication.

**Champs :**
- `id` (int) - Identifiant unique
- `titre` (string) - Titre de l'article
- `slug` (string) - URL-friendly version du titre
- `contenu` (text) - Contenu de l'article
- `extrait` (text) - Résumé de l'article
- `imagePrincipale` (string) - URL de l'image principale
- `datePublication` (datetime) - Date de publication
- `statut` (string) - Statut (brouillon, publié, archivé)
- `nombreVues` (int) - Nombre de vues
- `metaDescription` (text) - Description SEO
- `motsCles` (text) - Mots-clés SEO
- `createdAt` (datetime) - Date de création
- `updatedAt` (datetime) - Date de modification
- `categories` (ManyToMany) - Catégories associées
- `tags` (ManyToMany) - Tags associés

### 2. **CategoriesBlog**
Catégorisation des articles.

**Champs :**
- `id` (int) - Identifiant unique
- `nom` (string) - Nom de la catégorie
- `slug` (string) - URL-friendly version
- `couleur` (string) - Couleur d'affichage
- `dateCreation` (datetime) - Date de création
- `articles` (ManyToMany) - Articles associés

### 3. **TagsBlog**
Système de tags pour les articles.

**Champs :**
- `id` (int) - Identifiant unique
- `nom` (string) - Nom du tag
- `slug` (string) - URL-friendly version
- `couleur` (string) - Couleur d'affichage
- `createdAt` (datetime) - Date de création
- `articles` (ManyToMany) - Articles associés

## 🚀 Endpoints API

### **📝 Articles de Blog**

#### `GET /api/articles`
Récupère la liste des articles avec filtres et pagination.

**Paramètres de requête :**
- `page` (int) - Numéro de page (défaut: 1)
- `limit` (int) - Nombre d'articles par page (défaut: 10)
- `statut` (string) - Filtrer par statut (brouillon, publié, archivé)
- `categorie` (int) - Filtrer par catégorie
- `tag` (int) - Filtrer par tag
- `recherche` (string) - Recherche dans titre et contenu

**Réponse :**
```json
{
  "articles": [
    {
      "id": 1,
      "titre": "Mon premier article",
      "slug": "mon-premier-article",
      "extrait": "Résumé de l'article...",
      "imagePrincipale": "/uploads/images/article1.jpg",
      "datePublication": "2024-01-15T10:30:00+00:00",
      "statut": "publié",
      "nombreVues": 150,
      "metaDescription": "Description SEO",
      "motsCles": "php, symfony, web",
      "createdAt": "2024-01-15T10:30:00+00:00",
      "updatedAt": "2024-01-15T10:30:00+00:00",
      "categories": [
        {
          "id": 1,
          "nom": "Développement",
          "slug": "developpement",
          "couleur": "#007bff"
        }
      ],
      "tags": [
        {
          "id": 1,
          "nom": "PHP",
          "slug": "php",
          "couleur": "#777bb4"
        }
      ]
    }
  ],
  "total": 25,
  "page": 1,
  "limit": 10,
  "status": "found"
}
```

#### `GET /api/articles/{id}`
Récupère un article spécifique par son ID.

**Réponse :**
```json
{
  "id": 1,
  "titre": "Mon premier article",
  "slug": "mon-premier-article",
  "contenu": "Contenu complet de l'article...",
  "extrait": "Résumé de l'article...",
  "imagePrincipale": "/uploads/images/article1.jpg",
  "datePublication": "2024-01-15T10:30:00+00:00",
  "statut": "publié",
  "nombreVues": 150,
  "metaDescription": "Description SEO",
  "motsCles": "php, symfony, web",
  "createdAt": "2024-01-15T10:30:00+00:00",
  "updatedAt": "2024-01-15T10:30:00+00:00",
  "categories": [...],
  "tags": [...]
}
```

#### `GET /api/articles/slug/{slug}`
Récupère un article par son slug (URL-friendly).

**Réponse :** (même structure que GET /api/articles/{id})

#### `POST /api/articles`
Crée un nouvel article.

**Body :** (multipart/form-data ou application/json)

**Format multipart/form-data (avec image) :**
```javascript
const formData = new FormData();
formData.append('titre', 'Mon article');
formData.append('extrait', 'Résumé de l\'article');
formData.append('contenu', 'Contenu complet de l\'article');
formData.append('statut', 'publié');
formData.append('date_publication', '2025-01-01');
formData.append('categories', JSON.stringify(['Catégorie 1', 'Catégorie 2']));
formData.append('tags', JSON.stringify(['Tag 1', 'Tag 2']));
formData.append('image', imageFile); // Fichier image

fetch('/api/articles', {
  method: 'POST',
  body: formData
});
```

**Format JSON (sans image) :**
```json
{
  "titre": "Nouvel article",
  "contenu": "Contenu de l'article...",
  "extrait": "Résumé...",
  "statut": "brouillon",
  "datePublication": "2024-01-15T10:30:00+00:00",
  "categories": ["Développement", "Tutoriel"],
  "tags": ["PHP", "Symfony", "Web"]
}
```

**Exemple de requête JSON :**
```javascript
const article = {
  titre: "Mon nouvel article",
  contenu: "Contenu de l'article...",
  extrait: "Résumé...",
  statut: "brouillon",
  categories: ["Développement", "Tutoriel"],
  tags: ["PHP", "Symfony", "Web"]
};

fetch('/api/articles', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify(article)
});
```

**💡 Création automatique des catégories et tags :**
Le système crée automatiquement les catégories et tags s'ils n'existent pas (comme WordPress) :

**Format simple (string) :**
```json
{
  "categories": ["Développement", "Tutoriel"],
  "tags": ["PHP", "Symfony", "Web"]
}
```

**Format avancé (objet) :**
```json
{
  "categories": [
    {
      "nom": "Développement",
      "slug": "developpement",
      "couleur": "#007bff"
    }
  ],
  "tags": [
    {
      "nom": "PHP",
      "slug": "php",
      "couleur": "#777bb4"
    }
  ]
}
```

**Réponse :**
```json
{
  "id": 1,
  "titre": "Nouvel article",
  "slug": "nouvel-article",
  "contenu": "Contenu de l'article...",
  "extrait": "Résumé...",
  "imagePrincipale": "/uploads/images/article.jpg",
  "datePublication": "2024-01-15T10:30:00+00:00",
  "statut": "brouillon",
  "nombreVues": 0,
  "metaDescription": "Description SEO",
  "motsCles": "php, symfony",
  "createdAt": "2024-01-15T10:30:00+00:00",
  "updatedAt": "2024-01-15T10:30:00+00:00",
  "categories": [...],
  "tags": [...]
}
```

#### `PUT /api/articles/{id}`
Met à jour un article existant.

**Body :** (même structure que POST)

**Réponse :** (article mis à jour)

#### `DELETE /api/articles/{id}`
Supprime un article.

**Réponse :**
```json
{
  "message": "Article supprimé avec succès"
}
```

#### `POST /api/articles/{id}/image`
Upload l'image principale d'un article.

**Body :** (multipart/form-data)
- `image_principale` (file) - Fichier image à uploader

**Réponse :**
```json
{
  "message": "Image de l'article uploadée avec succès",
  "image": "nom_du_fichier.jpg",
  "imageUrl": "/uploads/articles/nom_du_fichier.jpg"
}
```

**Exemple de requête :**
```javascript
const formData = new FormData();
formData.append('image_principale', imageFile);

fetch(`/api/articles/${articleId}/image`, {
  method: 'POST',
  body: formData
});
```

### **🏷️ Catégories de Blog**

#### `GET /api/categories-blog`
Récupère toutes les catégories.

**Réponse :**
```json
{
  "categories": [
    {
      "id": 1,
      "nom": "Développement",
      "slug": "developpement",
      "couleur": "#007bff",
      "dateCreation": "2024-01-01T00:00:00+00:00",
      "articles": []
    }
  ],
  "total": 5,
  "status": "found"
}
```

#### `GET /api/categories-blog/{id}`
Récupère une catégorie spécifique.

**Réponse :**
```json
{
  "id": 1,
  "nom": "Développement",
  "slug": "developpement",
  "description": "Articles sur le développement web",
      "couleur": "#007bff",
      "dateCreation": "2024-01-01T00:00:00+00:00",
  "articles": []
}
```

#### `POST /api/categories-blog`
Crée une nouvelle catégorie.

**Body :**
```json
{
  "nom": "Nouvelle catégorie",
  "slug": "nouvelle-categorie",
  "couleur": "#28a745"
}
```

**Réponse :**
```json
{
  "id": 1,
  "nom": "Nouvelle catégorie",
  "slug": "nouvelle-categorie",
  "couleur": "#28a745",
  "dateCreation": "2024-01-15T10:30:00+00:00",
  "articles": []
}
```

#### `PUT /api/categories-blog/{id}`
Met à jour une catégorie.

**Body :** (même structure que POST)

**Réponse :** (catégorie mise à jour)

#### `DELETE /api/categories-blog/{id}`
Supprime une catégorie.

**Réponse :**
```json
{
  "message": "Catégorie supprimée avec succès"
}
```

### **🔖 Tags de Blog**

#### `GET /api/tags-blog`
Récupère tous les tags.

**Réponse :**
```json
{
  "tags": [
    {
      "id": 1,
      "nom": "PHP",
      "slug": "php",
      "couleur": "#777bb4",
      "createdAt": "2024-01-01T00:00:00+00:00",
      "articles": []
    }
  ],
  "total": 10,
  "status": "found"
}
```

#### `GET /api/tags-blog/{id}`
Récupère un tag spécifique.

**Réponse :**
```json
{
  "id": 1,
  "nom": "PHP",
  "slug": "php",
  "couleur": "#777bb4",
  "createdAt": "2024-01-01T00:00:00+00:00",
  "articles": []
}
```

#### `POST /api/tags-blog`
Crée un nouveau tag.

**Body :**
```json
{
  "nom": "Nouveau tag",
  "slug": "nouveau-tag",
  "couleur": "#dc3545"
}
```

**Réponse :**
```json
{
  "id": 1,
  "nom": "Nouveau tag",
  "slug": "nouveau-tag",
  "couleur": "#dc3545",
  "createdAt": "2024-01-15T10:30:00+00:00",
  "articles": []
}
```

#### `PUT /api/tags-blog/{id}`
Met à jour un tag.

**Body :** (même structure que POST)

**Réponse :** (tag mis à jour)

#### `DELETE /api/tags-blog/{id}`
Supprime un tag.

**Réponse :**
```json
{
  "message": "Tag supprimé avec succès"
}

## 🔧 Fonctionnalités Avancées

### **🏷️ Création Automatique des Tags et Catégories**

Le système fonctionne comme WordPress : vous pouvez envoyer des noms de tags/catégories qui n'existent pas encore, et ils seront automatiquement créés et liés à l'article.

**Comment ça fonctionne :**

1. **Recherche existante** : Le système cherche d'abord si le tag/catégorie existe
2. **Création automatique** : S'il n'existe pas, il le crée avec un slug généré automatiquement
3. **Liaison** : Le tag/catégorie est automatiquement lié à l'article

**Exemples d'utilisation frontend :**

```javascript
// Format simple - juste les noms
const article = {
  titre: "Mon article",
  contenu: "Contenu...",
  categories: ["Développement", "Tutoriel"], // Créés automatiquement
  tags: ["PHP", "Symfony", "API"] // Créés automatiquement
};

// Format avancé - avec propriétés supplémentaires
const article = {
  titre: "Mon article",
  contenu: "Contenu...",
  categories: [
    {
      nom: "Développement",
      description: "Articles sur le développement web",
      couleur: "#007bff",
      icone: "code"
    }
  ],
  tags: [
    {
      nom: "PHP",
      description: "Articles sur PHP",
      couleur: "#777bb4"
    }
  ]
};
```

### **Recherche et Filtrage**

#### Recherche textuelle
```
GET /api/articles?recherche=symfony
```

#### Filtrage par statut
```
GET /api/articles?statut=publié
```

#### Filtrage par catégorie
```
GET /api/articles?categorie=1
```

#### Filtrage par tag
```
GET /api/articles?tag=3
```

#### Combinaison de filtres
```
GET /api/articles?statut=publié&categorie=1&recherche=php&page=2&limit=5
```

### **Pagination**

Tous les endpoints de liste supportent la pagination :
- `page` - Numéro de page (commence à 1)
- `limit` - Nombre d'éléments par page (max 50)

### **Tri**

Les articles sont triés par date de publication décroissante par défaut.

## 📊 Codes de Réponse

- **200 OK** - Succès
- **201 Created** - Création réussie
- **400 Bad Request** - Données invalides
- **404 Not Found** - Ressource non trouvée
- **500 Internal Server Error** - Erreur serveur

## 🎯 Groupes de Sérialisation

### **Articles**
- `articles:read` - Lecture des articles
- `articles:write` - Écriture des articles

### **Catégories**
- `categories:read` - Lecture des catégories
- `categories:write` - Écriture des catégories

### **Tags**
- `tags:read` - Lecture des tags
- `tags:write` - Écriture des tags

## 🚀 Utilisation Frontend

### **Récupérer les articles publiés**
```javascript
const response = await fetch('/api/articles?statut=publié&limit=6');
const data = await response.json();
console.log(data.articles);
```

### **Rechercher des articles**
```javascript
const response = await fetch('/api/articles?recherche=symfony&statut=publié');
const data = await response.json();
```

### **Créer un article**
```javascript
const article = {
  titre: "Mon nouvel article",
  slug: "mon-nouvel-article",
  contenu: "Contenu de l'article...",
  statut: "brouillon",
  categories: [1, 2],
  tags: [1, 3]
};

const response = await fetch('/api/articles', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify(article)
});
```

### **Mettre à jour un article**
```javascript
const response = await fetch('/api/articles/1', {
  method: 'PUT',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ statut: 'publié' })
});
```

## 📝 Notes Importantes

1. **Slugs** : Les slugs sont générés automatiquement à partir des titres
2. **Statuts** : Les articles peuvent être en brouillon, publiés ou archivés
3. **Relations** : Les articles peuvent avoir plusieurs catégories et tags
4. **SEO** : Chaque article a des métadonnées pour le référencement
5. **Vues** : Le compteur de vues est automatiquement incrémenté
6. **Images** : Les images sont stockées dans `/public/uploads/images/`

## 🔒 Sécurité

- Validation des données d'entrée
- Protection contre les injections SQL
- Gestion des erreurs appropriée
- Limitation de la taille des fichiers uploadés

## 📋 Récapitulatif des Endpoints

### **📝 Articles (7 endpoints)**
- `GET /api/articles` - Liste des articles
- `GET /api/articles/{id}` - Article par ID
- `GET /api/articles/slug/{slug}` - Article par slug
- `POST /api/articles` - Créer un article
- `PUT /api/articles/{id}` - Modifier un article
- `DELETE /api/articles/{id}` - Supprimer un article
- `POST /api/articles/{id}/image` - Upload image principale

### **🏷️ Catégories (5 endpoints)**
- `GET /api/categories-blog` - Liste des catégories
- `GET /api/categories-blog/{id}` - Catégorie par ID
- `POST /api/categories-blog` - Créer une catégorie
- `PUT /api/categories-blog/{id}` - Modifier une catégorie
- `DELETE /api/categories-blog/{id}` - Supprimer une catégorie

### **🔖 Tags (5 endpoints)**
- `GET /api/tags-blog` - Liste des tags
- `GET /api/tags-blog/{id}` - Tag par ID
- `POST /api/tags-blog` - Créer un tag
- `PUT /api/tags-blog/{id}` - Modifier un tag
- `DELETE /api/tags-blog/{id}` - Supprimer un tag

**Total : 17 endpoints pour le système de blog complet !**

## 🎯 Statuts de Réponse

- **200 OK** - Succès
- **201 Created** - Création réussie
- **400 Bad Request** - Données invalides
- **404 Not Found** - Ressource non trouvée
- **500 Internal Server Error** - Erreur serveur

## 🔗 Relations entre Entités

```
ArticlesBlog
├── ManyToMany → CategoriesBlog
└── ManyToMany → TagsBlog

CategoriesBlog
└── ManyToMany → ArticlesBlog

TagsBlog
└── ManyToMany → ArticlesBlog
```

---

*Documentation générée automatiquement - API Portfolio Blog System*

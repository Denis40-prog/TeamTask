# 🗑️ Système de Permissions de Suppression

## ✅ Fonctionnalités Implémentées

### 🏢 **Équipes**
- **Admin du Site** : Peut supprimer n'importe quelle équipe
- **Admin d'Équipe** : Peut supprimer uniquement son équipe
- **Utilisateur Normal** : Ne peut pas supprimer d'équipes

**Localisation des boutons :**
- Dashboard : Icône poubelle sur chaque carte d'équipe
- Page équipes dédiée : Bouton "Supprimer" dans la liste

### 📂 **Projets**
- **Admin du Site** : Peut supprimer n'importe quel projet
- **Admin d'Équipe** : Peut supprimer les projets de son équipe
- **Utilisateur Normal** : Ne peut pas supprimer de projets

**Localisation des boutons :**
- Page projets : Icône poubelle à côté du titre du projet

### 📝 **Tâches**
- **Admin du Site** : Peut supprimer n'importe quelle tâche
- **Admin d'Équipe** : Peut supprimer les tâches de son équipe
- **Utilisateur Normal** : Ne peut pas supprimer de tâches

**Localisation des boutons :**
- Page tâches : Bouton "Supprimer" à côté du bouton "Modifier"

### 💬 **Commentaires**
- **Admin du Site** : Peut supprimer n'importe quel commentaire
- **Admin d'Équipe** : Peut supprimer les commentaires dans son équipe
- **Utilisateur Normal** : Peut supprimer UNIQUEMENT ses propres commentaires

**Localisation des boutons :**
- Page tâches : Icône 🗑️ à côté de la date du commentaire

## 🔧 Architecture Technique

### Permissions (Trait HasTeamPermissions)
```php
- canDeleteTeam(Team $team)      // Admin site OU Admin équipe
- canDeleteProject(Project $project)  // Admin site OU Admin équipe du projet
- canDeleteTask(Task $task)      // Admin site OU Admin équipe du projet de la tâche
- canDeleteComment(Comment $comment) // Auteur OU Admin site OU Admin équipe
```

### Gates (AuthServiceProvider)
```php
- deleteTeam
- deleteProject
- deleteTask
- deleteComment
```

### Méthodes Livewire
```php
// Dashboard.php & TeamComponent.php
- deleteTeam(Team $team)

// ProjectComponent.php
- deleteProject(Project $project)

// TaskComponent.php
- deleteTask(Task $task)
- deleteComment(Comment $comment)
```

## 🛡️ Sécurité

- ✅ Vérification des permissions via Gates
- ✅ Confirmations JavaScript avant suppression
- ✅ Messages d'erreur si permissions insuffisantes
- ✅ Suppression en cascade via migrations (CASCADE)
- ✅ Gestion des exceptions

## 🎯 Tests Recommandés

1. **Connectez-vous en tant qu'admin** (root@teamtask.com)
   - Testez la suppression d'équipes, projets, tâches, commentaires

2. **Créez un admin d'équipe**
   - Testez qu'il peut supprimer dans son équipe seulement

3. **Connectez-vous en utilisateur normal**
   - Vérifiez qu'il ne peut supprimer que ses commentaires

4. **Testez la suppression en cascade**
   - Supprimez une équipe → tous ses projets/tâches/commentaires disparaissent

## 🚨 Messages de Confirmation

- **Équipe** : "Êtes-vous sûr de vouloir supprimer cette équipe ? Cette action est irréversible et supprimera tous les projets, tâches et commentaires associés."
- **Projet** : "Êtes-vous sûr de vouloir supprimer ce projet ? Cette action est irréversible."
- **Tâche** : "Êtes-vous sûr de vouloir supprimer cette tâche ? Cette action est irréversible."
- **Commentaire** : "Êtes-vous sûr de vouloir supprimer ce commentaire ?"

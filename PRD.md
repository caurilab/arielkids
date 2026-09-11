# PRD — Ariel Kid's

## 1. Vue d'ensemble

**Ariel Kid's** est une boutique e-commerce mono-marque (pas de marketplace) pour la vente d'articles de mode. Le socle est l'enfant ; des rubriques Femmes et Hommes existent mais restent secondaires.

- **Marque unique**, une seule entité vendeuse.
- **Marché** : Côte d'Ivoire.
- **Devise** : FCFA (XOF), sans décimales, format `12 500 F CFA`.
- **Langue** : français uniquement.
- **Ton visuel attendu** : moderne, soigné, élégant, fluide. S'inspirer des designs fournis dans `reference/`.

## 2. Stack technique (versions à jour, obligatoires)

- **Laravel 13** (PHP 8.3 minimum).
- **Inertia 2** comme pont front/back.
- **Vue 3** en `<script setup lang="ts">` (TypeScript partout).
- **Vite** pour le build.
- **Tailwind CSS** (dernière version) pour le style.
- **Monorepo** : un seul dépôt, un seul déploiement. Front et back-office vivent dans le même projet Laravel. Aucune API découplée, aucun second framework. Auth par session Laravel.

Le back-office (administration) utilise la même stack : Inertia + Vue 3 TypeScript, sous un `AdminLayout` distinct.

## 3. Architecture du projet

```
ariel-kids/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Shop/           # Catalogue, Produit, Panier, Checkout
│   │   │   ├── Admin/          # CRUD back-office
│   │   │   └── Webhooks/       # CinetPayWebhookController
│   │   ├── Requests/
│   │   └── Middleware/
│   ├── Models/                 # Product, Category, Order, OrderItem, Customer, Media
│   ├── Services/
│   │   ├── Payment/
│   │   │   ├── PaymentGateway.php        # interface (contrat)
│   │   │   ├── CinetPayGateway.php       # Mobile Money + carte
│   │   │   └── CashOnDeliveryGateway.php # paiement à la livraison
│   │   ├── Instagram/          # récupération du flux (API Graph)
│   │   └── Cart/
│   └── Enums/                  # Audience, OrderStatus, PaymentMethod
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Shop/           # Home, Category, Product, Cart, Checkout, Confirmation
│   │   │   └── Admin/          # Dashboard, Products, Orders, Categories, Media
│   │   ├── Components/         # ProductCard, CategoryNav, WhatsAppButton, InstaFeed...
│   │   ├── Layouts/            # ShopLayout, AdminLayout
│   │   ├── Composables/        # useCart.ts, useCurrency.ts
│   │   ├── types/              # index.ts (types partagés Product, Order...)
│   │   └── app.ts
│   └── css/app.css
├── routes/
│   ├── web.php                # boutique
│   ├── admin.php              # back-office
│   └── webhooks.php           # callbacks CinetPay
├── storage/app/public/products/   # images produits
└── config/cinetpay.php
```

## 4. Modèle de données

### `categories` (arborescent)
- `id`, `parent_id` (nullable, self-référence), `name`, `slug`, `position` (int, pour l'ordre d'affichage), `is_active` (bool), timestamps.
- Une catégorie peut avoir des sous-catégories (ex. Filles → Vêtements, Chaussures…).

### `products`
- `id`, `category_id`, `name`, `slug`, `description` (text), `price` (integer, en FCFA sans décimales), `audience` (enum : `enfant_fille`, `enfant_garcon`, `bebe`, `femme`, `homme`), `sku` (nullable), `stock` (int), `is_active` (bool), `is_featured` (bool), timestamps.

### `media`
- `id`, `product_id`, `path`, `alt`, `position`, timestamps. Un produit a plusieurs images.

### `customers`
- `id`, `name`, `phone`, `email` (nullable), `address` (text), timestamps.

### `orders`
- `id`, `customer_id`, `reference` (unique), `status` (enum : `en_attente`, `payee`, `en_preparation`, `expediee`, `livree`, `annulee`), `payment_method` (enum : `mobile_money`, `livraison`), `payment_status` (enum : `en_attente`, `paye`, `echoue`), `total` (integer FCFA), `cinetpay_transaction_id` (nullable), timestamps.

### `order_items`
- `id`, `order_id`, `product_id`, `product_name` (snapshot), `unit_price` (snapshot), `quantity`, timestamps.

## 5. Logique métier « enfants d'abord »

- La navigation principale met toujours les rayons **Enfants** en tête.
- La page d'accueil affiche en priorité les produits enfants (`is_featured` + audience enfant).
- **Fallback** : si aucun produit enfant actif n'est disponible dans une zone de mise en avant, les articles adultes (Femmes/Hommes) prennent la place. Cette bascule est automatique, pilotée par le stock, pas codée en dur.
- L'arbre de catégories est entièrement gérable depuis le back-office (ajout/renommage/réordonnancement sans toucher au code).

## 6. Catalogue (structure de navigation cible)

**ENFANTS** (socle prioritaire)
- Filles → Vêtements, Chaussures, Sacs, Accessoires
- Garçons → Vêtements, Chaussures, Sacs, Accessoires
- Bébé (0–2 ans) → Vêtements, Chaussures, Accessoires

**FEMMES** → Vêtements, Chaussures, Sacs, Montres, Accessoires

**HOMMES** → Vêtements, Chaussures, Sacs, Montres, Accessoires

Ces catégories sont créées via un seeder, mais restent modifiables en back-office.

## 7. Boutique (front)

- **Accueil** : hero, mise en avant enfants (avec fallback adultes), nouveautés, accès rapide aux rayons, flux Instagram, bouton WhatsApp flottant.
- **Page catégorie** : grille de produits, filtres (rayon, prix, disponibilité), tri.
- **Fiche produit** : galerie d'images, description, prix, sélection quantité, ajout au panier, bouton « Commander sur WhatsApp » pré-rempli avec le nom du produit.
- **Panier** : récap, modification quantités, total FCFA.
- **Checkout** : formulaire client (nom, téléphone, adresse), choix du mode de paiement (Mobile Money via CinetPay OU paiement à la livraison), validation.
- **Confirmation** : récap de commande + référence.

## 8. Paiement

Couche abstraite : interface `PaymentGateway` avec deux implémentations.

### `CinetPayGateway` (Mobile Money + carte)
- Agrégateur ivoirien : Orange Money, MTN, Moov, Wave, cartes.
- Flux : création de transaction → URL/guichet de paiement → redirection client → notification asynchrone via webhook (`notify_url`).
- Config en `.env` : `CINETPAY_API_KEY`, `CINETPAY_SITE_ID`, `CINETPAY_SECRET_KEY`, `CINETPAY_NOTIFY_URL`, `CINETPAY_RETURN_URL`.
- `CinetPayWebhookController` : reçoit la notification, vérifie le statut réel de la transaction côté CinetPay (ne jamais faire confiance au seul callback), met à jour `orders.payment_status` et `orders.status`.
- **Important** : consulter la documentation officielle CinetPay au moment de l'intégration pour les noms de champs exacts et la vérification de signature. Ne pas inventer les paramètres de l'API.

### `CashOnDeliveryGateway` (paiement à la livraison)
- Marque la commande `en_attente` de paiement, statut `en_preparation`.

## 9. WhatsApp

- Bouton flottant présent sur tout le site.
- Lien `https://wa.me/<NUMERO>?text=<message>`. Sur une fiche produit, le message est pré-rempli avec le nom et l'URL du produit.
- Numéro configurable en `.env` : `WHATSAPP_NUMBER`.

## 10. Instagram (API Graph)

- Compte Instagram **Business/Creator** relié à une Page Facebook + app Meta.
- Token longue durée (60 jours, à rafraîchir).
- `InstagramService` : appelle l'endpoint media du compte → récupère images, légendes, permaliens → **cache local 24 h** (éviter de taper l'API à chaque visite et de dépendre de sa latence).
- Composant `InstaFeed` affiche la grille sur l'accueil.
- Config `.env` : `IG_ACCESS_TOKEN`, `IG_BUSINESS_ACCOUNT_ID`, `IG_APP_ID`, `IG_APP_SECRET`.
- **Important** : vérifier la version courante de l'API Graph et les endpoints au moment de l'intégration ; Meta change souvent ses versions.

## 11. Back-office (admin)

Accès protégé par auth. Sous `AdminLayout`, pages `Pages/Admin/`.

- **Dashboard** : chiffres clés (commandes, CA, produits actifs).
- **Produits** : CRUD complet, upload multi-images (le lot d'images existant sera ajouté ici), gestion stock, mise en avant, audience.
- **Catégories** : gestion de l'arbre (créer, renommer, réordonner, activer/désactiver).
- **Commandes** : liste, détail, changement de statut, suivi paiement.
- **Médias** : bibliothèque d'images.

## 12. Devise & formatage

- Prix stockés en **entier** (FCFA sans décimales).
- Formatage centralisé : helper PHP + composable `useCurrency.ts` → `12 500 F CFA` (espace insécable comme séparateur de milliers).
- Ne jamais dupliquer la logique de formatage.

## 13. Qualité & conventions

- TypeScript strict sur tout le front.
- Types partagés dans `resources/js/types/index.ts`.
- Form Requests pour toute validation serveur.
- Enums PHP pour les statuts et énumérations.
- Composants Vue réutilisables, pas de duplication.
- Design responsive (mobile-first : l'essentiel du trafic sera mobile).
- Respecter l'inspiration visuelle du dossier `reference/`.

## 14. Ordre de construction recommandé

1. Setup projet (Laravel 13, Inertia, Vue 3 TS, Tailwind, Vite).
2. Modèles + migrations + enums + seeder catégories.
3. Layouts (Shop, Admin) + design system de base (couleurs, typo tirées de `reference/`).
4. Boutique : accueil → catégorie → fiche produit → panier.
5. Checkout + couche paiement (interface + CashOnDelivery d'abord, puis CinetPay).
6. Webhook CinetPay.
7. Back-office : produits, catégories, commandes, médias.
8. WhatsApp + Instagram.
9. Peaufinage visuel et responsive.

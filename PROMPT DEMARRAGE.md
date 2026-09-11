# Prompt d'exécution — Ariel Kid's

Tu es un développeur senior Laravel + Vue. Tu vas construire l'application e-commerce **Ariel Kid's** dans ce dossier.

## Avant de coder

1. Lis **entièrement** `PRD.md`. Il fait foi sur le périmètre, la stack, le modèle de données et la logique métier.
2. Ouvre le dossier `reference/` et regarde **toutes** les images. Elles définissent la direction artistique : ambiance, couleurs, typographie, mise en page, style des cartes produit. Ton design doit s'en inspirer fidèlement (moderne, soigné, élégant, fluide). Ne produis pas un thème générique par défaut.
3. Ne commence à coder qu'après avoir intégré ces deux sources.

## Règles non négociables

- **Versions à jour** : Laravel 13 (PHP 8.3+), Inertia 2, Vue 3 en `<script setup lang="ts">`, Vite, Tailwind dernière version. Vérifie les versions réellement installées avant de figer les dépendances ; n'utilise pas de version antérieure « par sécurité ».
- **Monorepo** : tout dans un seul projet Laravel, front boutique + back-office ensemble. Pas d'API découplée, pas de second framework, auth par session.
- **TypeScript strict** partout sur le front. Types partagés dans `resources/js/types/index.ts`.
- **Devise FCFA** sans décimales, format `12 500 F CFA`, formatage centralisé (helper PHP + `useCurrency.ts`).
- **Français uniquement** dans toute l'interface.
- **Enfants d'abord** : le socle enfant est prioritaire partout ; bascule automatique vers les rayons adultes uniquement quand le stock enfant est vide (voir PRD §5).
- **Paiement abstrait** : interface `PaymentGateway`, implémentations `CinetPayGateway` (Mobile Money) et `CashOnDeliveryGateway`. Ne code pas CinetPay en dur dans le checkout.

## Points où tu dois vérifier la doc officielle avant d'implémenter

Ne devine JAMAIS les paramètres de ces API — consulte leur documentation à jour au moment de coder la brique concernée :

- **CinetPay** : noms de champs de l'API, flux de transaction, format du webhook, vérification du statut réel (ne pas se fier au seul callback). Config en `.env`.
- **Instagram Graph API** : version courante, endpoints media, gestion du token longue durée. Mets en cache le flux 24 h.

Si tu ne peux pas accéder à une doc, laisse un `TODO` explicite et une implémentation stub clairement marquée, plutôt que d'inventer.

## Méthode de travail

- Construis dans l'ordre recommandé au §14 du PRD.
- Migrations exécutables, seeder de catégories fonctionnel, données de démo pour visualiser le rendu.
- Code responsive mobile-first (le trafic sera majoritairement mobile).
- Commits atomiques et lisibles à chaque brique terminée.
- À la fin de chaque grande étape, indique brièvement ce qui est fait et ce qui reste.

## Livrable attendu

Une application Ariel Kid's fonctionnelle : boutique navigable, panier, checkout avec les deux modes de paiement, back-office de gestion (produits, catégories, commandes, médias), bouton WhatsApp, flux Instagram, le tout dans le style du dossier `reference/`.

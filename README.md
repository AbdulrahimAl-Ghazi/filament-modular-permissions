# Filament Modular Permissions

[![Latest Version on Packagist](https://img.php.net/images/logos/php-logo.svg)](https://packagist.org/packages/abdulrahim/filament-modular-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/abdulrahim/filament-modular-permissions.svg?style=flat-square)](https://packagist.org/packages/abdulrahim/filament-modular-permissions)
[![License](https://img.shields.io/packagist/l/abdulrahim/filament-modular-permissions.svg?style=flat-square)](https://packagist.org/packages/abdulrahim/filament-modular-permissions)

A professional Laravel package for modular roles and permissions in Filament V3. Supporting multi-panel, auto-syncing, and **Global Zero-Config Protection**.

---

[العربية](#نظام-الصلاحيات-والأدوار-الموديولر-لـ-filament) | [English](#features)

---

## Features

- **Global Zero-Config Shield**: Protect and hide all resources and widgets automatically based on permissions.
- **Smart Sync**: Sync all resources and widgets with Spatie permissions in one command.
- **Multi-panel Support**: Publish and manage permissions for each panel independently.
- **Super Admin Gate**: Automatically grants all permissions to the `super_admin` role.
- **Interactive CLI**: Select your target panel via an interactive CLI menu.
- **User Management**: Pre-configured User Resource with role management.

## Installation

1. Install the package via composer:

```bash
composer require abdulrahim/filament-modular-permissions
```

2. Sync initial permissions:

```bash
php artisan permissions:sync
```

3. Publish Resources:

```bash
php artisan permissions:publish-resources
php artisan permissions:publish-user-resource
```

## Advanced Concepts

### 1. Multi-Guard Architecture

The package is built to handle multi-panel environments where each panel might use a different Auth Guard (e.g., `web` for Users, `admin` for Admins).

- When syncing or publishing, the package automatically detects the panel's guard.
- Permissions are created and checked specifically for the guard associated with the current panel, preventing permission conflicts between panels.

### 2. Intelligent Super Admin

The `super_admin` role is treated as a master role.

- The package registers a global `Gate::before` check.
- If a user has the `super_admin` role (for their specific guard), they bypass all permission checks and are granted full access automatically.
- This works zero-config; you only need to create the role and assign it to a user.

## Manual Control (Optional)

If you prefer to control each resource manually, disable the global shield in `config/filament-modular-permissions.php`:

```php
'auto_hide_resources' => false,
```

Then, use the traits: `use HandlesResourcePermissions;` or `use HandlesWidgetPermissions;`

## Contact

Email: [abaad.dev8@gmail.com](mailto:abaad.dev8@gmail.com)  
Website: [abaad.dev](https://abaad.dev)

---

# نظام الصلاحيات والأدوار الموديولر لـ Filament

مكتبة احترافية لإدارة الأدوار والصلاحيات في Filament V3 تعتمد على المبدأ الموديولر، مع دعم كامل لتعدد لوحات التحكم والحماية الشاملة التلقائية.

## المميزات الرئيسية

- **الحماية الشاملة التلقائية**: حماية المسارات وإخفاء الموارد من القائمة الجانبية تلقائياً بمجرد التثبيت.
- **نظام مزامنة ذكي**: أمر واحد لمزامنة جميع الموارد والويدجت مع نظام Spatie.
- **دعم تعدد اللوحات**: إدارة الصلاحيات لكل لوحة تحكم بشكل مستقل تماماً عبر أوامر تفاعلية.
- **السوبر أدمن**: نظام Gate يعطي كافة الصلاحيات لدور `super_admin` تلقائياً.
- **إدارة المستخدمين**: مورد إدارة مستخدمين جاهز مع إمكانية ربط الأدوار والترجمة الكاملة.

## التثبيت

1. تحميل المكتبة:

```bash
composer require abdulrahim/filament-modular-permissions
```

2. مزامنة الصلاحيات:

```bash
php artisan permissions:sync
```

3. نشر واجهات الإدارة:

```bash
php artisan permissions:publish-resources
php artisan permissions:publish-user-resource
```

## مفاهيم متقدمة

### 1. معمارية الحراس المتعددة (Multi-Guard)

تم تصميم المكتبة لتتعامل مع بيئات اللوحات المتعددة حيث قد تستخدم كل لوحة حارس أمان مختلف (مثل `web` للمستخدمين و `admin` للمدراء).

- عند المزامنة أو النشر، تكتشف المكتبة تلقائياً الحارس (Guard) الخاص باللوحة.
- يتم إنشاء وفحص الصلاحيات بناءً على الحارس المرتبط باللوحة الحالية، مما يمنع تداخل الصلاحيات بين اللوحات المختلفة.

### 2. السوبر أدمن الذكي (Intelligent Super Admin)

يتم التعامل مع دور `super_admin` كدور رئيسي (Master Role).

- تقوم المكتبة بتسجيل فحص `Gate::before` عالمي.
- إذا كان المستخدم يملك دور `super_admin` (المرتبط بالحارس الخاص به)، فإنه يتجاوز جميع فحوصات الصلاحيات ويمنح وصولاً كاملاً تلقائياً.
- يعمل هذا النظام بدون أي إعدادات إضافية؛ فقط قم بإنشاء الدور واسنده للمستخدم.

## التحكم اليدوي (اختياري)

إذا كنت تفضل التحكم في كل مورد بشكل يدوي، قم بتعطيل الحماية الشاملة في ملف `config/filament-modular-permissions.php`:

```php
'auto_hide_resources' => false,
```

ثم استخدم الـ Traits يدوياً: `use HandlesResourcePermissions;` أو `use HandlesWidgetPermissions;`

## التواصل

البريد الإلكتروني: [abaad.dev8@gmail.com](mailto:abaad.dev8@gmail.com)  
الموقع الإلكتروني: [abaad.dev](https://abaad.dev)

## License

MIT License.

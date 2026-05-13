# دليل النشر والتحديث (Publishing Guide)

هذا الملف مرجع سريع للأوامر المستخدمة لإدارة ورفع المكتبة إلى GitHub و Packagist.

---

## 1. الرفع لأول مرة (Initial Setup)
نفذ هذه الأوامر عند رفع المكتبة لأول مرة فقط:

```bash
# تأكد أنك داخل مجلد المكتبة
cd packages/abdulrahim/filament-modular-permissions

git init
git add .
git commit -m "Initial Release v1.0.0"
git branch -M main

# استبدل YOUR_GITHUB_URL برابط المستودع الذي أنشأته
git remote add origin YOUR_GITHUB_URL

git push -u origin main
```

---

## 2. التحديثات الدورية (Daily Updates)
استخدم هذه الأوامر عندما تقوم بتعديل الكود وتريد حفظه على GitHub:

```bash
git add .
git commit -m "وصف مختصر للتعديلات التي قمت بها"
git push origin main
```

---

## 3. إصدار نسخة جديدة (Releasing a New Version)
عندما تصبح التعديلات جاهزة لتصل لجميع المستخدمين كنسخة مستقرة، يفضل استخدام الـ Tags:

```bash
# قم بتغيير رقم الإصدار في كل مرة (مثلاً v1.0.1 ثم v1.0.2 وهكذا)
git tag v1.0.1
git push origin v1.0.1
```
*ملاحظة: يمكنك أيضاً القيام بهذه الخطوة عبر واجهة GitHub (قسم Releases).*

---

## 4. تحديث المكتبة في مشروعك الحالي
بما أنك تستخدم `symlink` في مشروعك الحالي، فالتعديلات تظهر فوراً. ولكن إذا أردت التأكد من تحديث ملف الـ `composer.lock` أو التحميل من Packagist مستقبلاً:

```bash
composer update abdulrahim/filament-modular-permissions
```

---

## 5. روابط هامة
- **مستودع الكود:** [GitHub](https://github.com/new)
- **إدارة الحزم:** [Packagist](https://packagist.org/packages/submit)

---
**تم إعداد هذا الدليل بواسطة Antigravity لمساعدة المطور Abdulrahim.**

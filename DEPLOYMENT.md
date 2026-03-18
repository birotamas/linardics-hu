# Deployment – cPanel

## Előfeltételek a szerveren

- PHP 8.1+ (ellenőrzés: cPanel → PHP Selector)
- MySQL 8.0 (cPanel → MySQL Databases)
- Git Version Control (cPanel → Git Version Control)

---

## 1. MySQL adatbázis létrehozása

1. cPanel → **MySQL Databases**
2. **Create New Database:** pl. `linardics_cms`
3. **Create New User:** pl. `linardics_user` + erős jelszó
4. **Add User To Database:** linardics_user → linardics_cms → **All Privileges**
5. Jegyezd fel: host (általában `localhost`), DB név, user, jelszó

---

## 2. Git repository csatlakoztatása

1. cPanel → **Git Version Control** → **Create**
2. Clone URL: `https://github.com/birotamas/linardics-hu.git`
3. Repository Path: `/home/[username]/public_html` (vagy almappa)
4. Branch: `main`
5. **Create** → cPanel klónozza a repót

---

## 3. CMS telepítése (setup.php)

1. Böngészőben: `https://linardics.o11.hu/setup.php`
2. Töltsd ki az adatokat:
   - **DB Host:** `localhost`
   - **Adatbázis neve:** az 1. lépésben létrehozott neve
   - **DB Felhasználó / Jelszó:** az 1. lépésből
   - **Admin e-mail + jelszó:** tetszőleges (min. 8 karakter)
3. **Telepítés indítása** → létrehozza a táblákat, betölti a 19 gépet
4. Sikeres telepítés után: **töröld a `setup.php`-t!**
   - cPanel → File Manager → `setup.php` → Delete

---

## 4. Képfeltöltési limit beállítása

Ha a képfeltöltés nem működik (max. fájlméret hiba), hozz létre egy `.htaccess` fájlt a gyökérkönyvtárban (vagy egészítsd ki a meglévőt):

```apache
php_value upload_max_filesize 5M
php_value post_max_size 6M
```

Vagy hozz létre egy `php.ini` fájlt a gyökérkönyvtárban:

```ini
upload_max_filesize = 5M
post_max_size = 6M
```

---

## 5. Images mappa jogosultságok

A feltöltött képek mappájának írhatónak kell lennie:

1. cPanel → **File Manager** → `assets/images/machines/`
2. Jobb klik → **Change Permissions** → `755`

---

## 6. Frissítés (deploy)

Minden `git push origin main` után:

1. cPanel → **Git Version Control**
2. A repo sorában: **Manage** → **Pull or Deploy**
3. Kattints: **Update from Remote**

A `cms/config.php` **nem kerül felülírásra** (gitignore-ban van) – a DB konfiguráció megmarad.

---

## URL-ek production után

| Oldal | URL |
|---|---|
| Dinamikus géppark | `https://linardics.o11.hu/geppark.php` |
| Admin belépés | `https://linardics.o11.hu/admin/login.php` |
| Admin dashboard | `https://linardics.o11.hu/admin/index.php` |

---

## Visszaállítás (rollback)

Ha valami nem működik:

```bash
git revert HEAD
git push origin main
# → cPanel → Pull or Deploy
```

---

## Biztonsági ellenőrzőlista deploy után

- [ ] `setup.php` törölve a szerverről
- [ ] `cms/config.php` nem látható publikusan (nem szükséges – PHP nem adja ki a forrást)
- [ ] Admin jelszó erős (min. 12 karakter, szám + speciális karakter)
- [ ] `assets/images/machines/` mappa jogosultsága: `755`
- [ ] HTTPS aktív (SSL tanúsítvány érvényes)

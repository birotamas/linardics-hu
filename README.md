# Linardics Kft. – Weboldal + CMS

Precíziós lemezmegmunkálás, Székesfehérvár. ISO 9001 tanúsított.

## Tech stack

- **Frontend:** HTML5, Tailwind CSS (CDN), Vanilla JS – nincs build tool
- **CMS:** Plain PHP 8.x + PDO, MySQL 8.0 – Composer nélkül
- **Deploy:** GitHub → cPanel Git Version Control

---

## Struktúra

```
linardics_hu/
├── index.html                  # Főoldal
├── geppark.html                # Statikus géppark (archív)
├── geppark.php                 # Dinamikus géppark (CMS-ből tölt)
├── referenciak.html
├── rolunk.html
├── kapcsolat.html
├── gyik.html
├── sitemap.xml / robots.txt
│
├── setup.php                   # Egyszeri DB telepítő varázsló
├── admin/                      # Admin panel
│   ├── login.php / logout.php
│   ├── index.php               # Dashboard
│   ├── machines.php            # Gép lista + drag-and-drop sorrend
│   ├── machine-edit.php        # Gép hozzáadás / szerkesztés
│   ├── reorder.php             # AJAX sorrendmentés
│   ├── settings.php            # Oldal szövegek
│   └── includes/               # auth, header, footer
│
├── cms/
│   ├── config.php              # DB konfig (gitignore-ban!)
│   └── config.sample.php       # Minta konfiguráció
│
└── assets/
    ├── images/
    │   └── machines/           # Feltöltött gép képek
    └── videos/
```

---

## Lokális fejlesztés

### Követelmények
- PHP 8.x (XAMPP, MAMP, vagy `brew install php`)
- MySQL 8.x

### Indítás

```bash
cd linardics_hu
php -S localhost:8000
# → http://localhost:8000
```

### Első futtatás (CMS setup)

1. Hozz létre egy MySQL adatbázist
2. Nyisd meg: `http://localhost:8000/setup.php`
3. Töltsd ki a DB adatokat + admin fiókot → **Telepítés indítása**
4. A setup.php létrehozza a táblákat és betölti a 19 gép adatait
5. Töröld a `setup.php`-t deploy után

### Admin panel

```
http://localhost:8000/admin/login.php
```

### Dinamikus géppark

```
http://localhost:8000/geppark.php
```

---

## CMS – Admin panel funkciók

| Funkció | Leírás |
|---|---|
| Gépek CRUD | Hozzáadás, szerkesztés, törlés |
| Drag-and-drop | Sorrend módosítása húzással |
| Aktív toggle | Gép elrejtése / megjelenítése a weboldalon |
| Specs repeater | Tetszőleges számú műszaki adat sor |
| Képfeltöltés | JPG, PNG, WebP – max. 3 MB |
| Oldal beállítások | Cím, bevezető szöveg, szekció nevek, badge-ek |

---

## Deploy

```bash
git push origin main
# → cPanel automatikusan lehúzza (Git Version Control)
```

Részletes deploy lépések: [DEPLOYMENT.md](DEPLOYMENT.md)

# Linardics Kft. – Weboldal Termékspecifikáció

> Alapja: kattintható HTML demo (2026. március). Cél: production-ready weboldal gyártása ebből a demóból.

---

## 1. Projekt háttér

**Cég:** Linardics Kft. – precíziós lemezmegmunkálás, Székesfehérvár
**Alapítva:** 1994
**Telephely:** 8000 Székesfehérvár, Sóstói Ipari Park
**Terület:** 8500 m² gyártóterület
**Tanúsítvány:** ISO 9001:2015 (2004 óta, évente auditálva)
**Telefon:** +36 22 503 865
**E-mail:** linardicskft@linardics.hu
**Domain:** linardics.hu
**Versenytárs referencia:** meliorlaser.hu (2024-es modern B2B site)

**Célcsoport:** Magyar nagyvállalatok beszerzői, mérnökei — B2B, prémium pozicionálás

**Projekt motivációja:** A meglévő WordPress oldal (2015-ös alap) nem teljesít elég jól. Kutassy Funnels audit alapján: az újraépítés gyorsabb és hatékonyabb, mint a meglévő javítása.

---

## 2. Design rendszer

### Színpaletta

| Szín | Hex | Használat |
|---|---|---|
| Háttér alap | `#060f1a` | Body, footer |
| Kártya háttér | `#0d1b2a` | Szekció háttér, card bg |
| Kártya sötét | `#122135` | Stat kártyák, mélységi elemek |
| Akcentszín | `#cc2222` | CTA, highlight, ikonok, vonalak |
| Fehér | `#ffffff` | Szöveg, logó |
| Szöveg halvány | `rgba(255,255,255,0.50)` | Leírások, metaadatok |
| Szöveg nagyon halvány | `rgba(255,255,255,0.25–0.40)` | Footer, jogi szöveg |
| Border | `rgba(255,255,255,0.08)` | Kártya körvonalak |

### Tipográfia

| Szerep | Font | Súly | Megjegyzés |
|---|---|---|---|
| Cím (heading) | Barlow Condensed | 600 (semibold) | Uppercase, tömör megjelenés |
| Body szöveg | Inter | 400–600 | Folyószöveg, gombok, UI elemek |

**Google Fonts URL:** `Barlow+Condensed:wght@400;600;700;800;900&family=Inter:wght@400;500;600`

### Spacing & Layout

- **Max container szélesség:** `min(90vw, 96.875rem)` – minden oldalon egységesen
- **Mobil padding:** `max(1rem, env(safe-area-inset-left/right))` – safe area kezelés notch-os eszközökhöz
- **Breakpoints (Tailwind alapú):** `md: 768px`, `lg: 1024px`, `xl: 1280px`

### Alapelemek

**Levágott sarkú gomb (`.btn-clipped`):**
`clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 10px, 100% 100%, 10px 100%, 0 calc(100% - 10px))`
Jobb felső + bal alsó sarok vágva, border-radius: 0

**Szekció eyebrow felirat:**
Piros bal oldali vonal (`w-1 h-6 bg-[#cc2222]`) + kis caps szöveg átlátszó stroke-kal
(`-webkit-text-stroke: 1.5px rgba(255,255,255,0.45); color: transparent`)

---

## 3. Oldalak leltára

### 3.1 Főoldal — `index.html`

**Title:** `Linardics Kft. | Precíziós Lemezmegmunkálás`
**Meta description:** `30 éve szállítunk precíziós lemezalkatrészeket nagyvállalatok számára. ISO 9001 tanúsított minőség, TRUMPF és AMADA géppark, 8500 m² gyártóterületen. Székesfehérvár.`
**Canonical:** `https://linardics.hu/`

**Szekciók (sorrendben):**

1. **Hero** — teljes képernyős, háttérkép + párhuzamos animáció (lásd Animációk fejezet). Bal oldal: főcím + alcím + 2 gomb. Jobb oldal (desktop): 3 kártya (Ajánlatkérés, Géppark, Referenciák). Scroll indicator lent.
2. **Szolgáltatások** — „Egykézből" főcím, 5 expandáló panel desktop-on (hover-re kitágul), mobilon 5 × álló kártyakép.
3. **Miért mi?** — Háttérkép overlay-el, bal oldal: 4 feature sor (ISO, 30 év, géppark, komplett gyártás), jobb oldal: 4 számláló kártya (30+, ISO, 8500 m², 15+).
4. **CTA szekció** — piros háttér, „Kész az ajánlatra? Írjon nekünk." + gomb.

**Schemas (JSON-LD):** `LocalBusiness`, `WebSite` (SearchAction)

---

### 3.2 Szolgáltatások áttekintő — `szolgaltatasok/index.html`

**Title:** `Szolgáltatásaink | Linardics Kft.`
**Meta description:** `CNC lézervágás, lemezhajlítás, élhajlítás, csőhajlítás és porfestés ISO 9001 minőségben. TRUMPF és AMADA géppark. Székesfehérvár – ajánlatkérés 24 h-n belül.`

5 szolgáltatás kártya, mindegyik saját aloldalra mutat. CTA szekció az alján.

**Schema:** `BreadcrumbList`

---

### 3.3 CNC Lemezmegmunkálás — `szolgaltatasok/cnc-lemezmegmunkalas.html`

**Title:** `CNC Lemezmegmunkálás | Linardics Kft.`
**Meta description:** `Precíziós CNC lézervágás TRUMPF TruLaser gépekkel. Acél, rozsdamentes, alumínium – ±0,1 mm pontossággal. ISO 9001. Ajánlatkérés 24 h-n belül.`

Technológia leírás, gép specifikációk, anyagok, toleranciák. CTA alján.
**Schema:** `Service`, `BreadcrumbList`

---

### 3.4 Lemezhajlítás — `szolgaltatasok/lemezhajlitas.html`

**Title:** `Lemezhajlítás | Linardics Kft.`
**Meta description:** `Precíziós lemezhajlítás és élhajlítás TRUMPF TruBend és AMADA HFE gépekkel. Acél, rozsdamentes, alumínium – 0,5–12 mm vastagságig. ISO 9001 minőség.`

**Schema:** `Service`, `BreadcrumbList`

---

### 3.5 Csőhajlítás — `szolgaltatasok/csohajlitas.html`

**Title:** `Csőhajlítás | Linardics Kft.`
**Meta description:** `CNC csőhajlítás SOCO SB-52X10A géppel. Kör, négyzet, téglalap keresztmetszetű csövek 1–3D sugarú ívvel. Acél, rozsdamentes, alumínium anyagokból.`

**Schema:** `Service`, `BreadcrumbList`

---

### 3.6 Porfestés — `szolgaltatasok/porfestes.html`

**Title:** `Porfestés | Linardics Kft.`
**Meta description:** `Ipari elektrostatikus porfestés Gema OptiCenter rendszerrel. RAL teljes paletta, kültéri és beltéri alkalmazás. ISO 9001 minőség, komplex projektek.`

**Schema:** `Service`, `BreadcrumbList`

---

### 3.7 Komplex projektek — `szolgaltatasok/komplex-projektek.html`

**Title:** `Komplex Projektek | Linardics Kft.`
**Meta description:** `Lézervágástól kész alkatrészig – komplex lemezmegmunkálási projektek menedzselése egyablakos kiszolgálással. ISO 9001, nagyvállalati referenciák.`

**Schema:** `Service`, `BreadcrumbList`

---

### 3.8 Géppark — `geppark.html`

**Title:** `Géppark | Linardics Kft.`
**Meta description:** `Prémium géppark: TRUMPF TruLaser lézervágók, TruBend hajlítók, AMADA élhajlítók, SOCO CNC csőhajlító, Gema porfestő rendszer. Székesfehérvár.`

**Gépek listája:**

| Kategória | Gép |
|---|---|
| TRUMPF Lézervágó | TruLaser 3030 Fiber, TruLaser 5030 TLF, TruMatic 7000, TruLaser Tube 5000 |
| TRUMPF Hajlító | TruBend Center 7030, TruBend 5130 ×2, TruBend 3100 ×2, TruBend 7036 |
| AMADA Hajlító | AMADA IT 1250/20t, AMADA HFE 3000/100t, AMADA HFE 3000/130Lt, AMADA HFP 4000/220t |
| Csőhajlítás | SOCO SB-52X10A |
| Felületkezelés | Gema OptiCenter rendszer, Wagner rendszer |
| Kötéstechnika | (felsorolva az oldalon) |

---

### 3.9 Referenciák — `referenciak.html`

**Title:** `Referenciák | Linardics Kft.`
**Meta description:** `30 év alatt felépített nagyvállalati referenciák lemezmegmunkálás, lézervágás, hajlítás és porfestés területén. Nézze meg elkészült projektjeinket.`

**Megjegyzés (hiányzó elem):** Partner logók placeholder — produkciós verzióhoz valós SVG/WebP logók kellenek a partnerek nevével.

---

### 3.10 Rólunk — `rolunk.html`

**Title:** `Rólunk | Linardics Kft.`
**Meta description:** `1994 óta foglalkozunk precíziós lemezmegmunkálással. ISO 9001 tanúsított, TRUMPF & AMADA géppark, 8500 m² modern gyártóterület Székesfehérváron.`

**Szekciók:** Értékeink (Precizitás, Megbízhatóság, Fejlődés), 30 év mérföldkövei (timeline), Székesfehérvár / Sóstói Ipari Park, Tanúsítványok & minőség (ISO 9001:2015, Minőség-ellenőrzés)

---

### 3.11 Ajánlatkérés — `kapcsolat.html`

**Title:** `Ajánlatkérés | Linardics Kft.`
**Meta description:** `Kérjen precíziós lemezmegmunkálási árajánlatot 3 egyszerű lépésben. 24 órán belül visszajelzünk. Tel.: +36 22 503 865 | linardicskft@linardics.hu`

**3-lépéses wizard:**

| Lépés | Mezők |
|---|---|
| 1. Kapcsolat | Cég neve, Kapcsolattartó neve, E-mail cím, Telefonszám, Preferált elérhetőség (chip: e-mail / telefon / mindkettő) |
| 2. Igény | Munkadarab típusa (chip: lemez / cső / egyéb), Anyag (select), Vastagság (mm), Darabszám (chip: prototípus / kis sorozat / nagysorozat / rendszeres), Határidő (chip: sürgős / normál / rugalmas) |
| 3. Dokumentumok | Drag-and-drop fájlfeltöltés (DXF, DWG, PDF, JPG), Üzenet szabad szöveg, GDPR checkbox |

**Siker képernyő:** beküldés után megjelenik visszajelző üzenet (fake 1200ms delay a demóban, produkciós verzióban valós e-mail küldés).

**Validáció:** minden lépésnél mező-szintű, piros highlight hibás mezőkön.

---

### 3.12 GYIK — `gyik.html`

**Title:** `GYIK | Linardics Kft.`
**Meta description:** `Válaszok a lemezmegmunkálással kapcsolatos leggyakoribb kérdésekre: anyagok, toleranciák, határidők, ajánlatkérés menete. Linardics Kft. – Székesfehérvár.`

**8 kérdés-válasz pár, 3 kategóriában:**
- Ajánlatkérés & Folyamat (3 kérdés)
- Technológia & Minőség (3 kérdés)
- Szállítás & Komplex projektek (2 kérdés)

**Schema:** `FAQPage` JSON-LD

---

## 4. Navigáció

### Navbar
- Fix, top-0, z-50
- Alap: `background: transparent`, scroll után: `rgba(6,15,26,0.88)` + `backdrop-filter: blur(12px)` + alsó border `rgba(255,255,255,0.06)`
- Padding: `py-5` → `py-3` scroll után (smooth transition)
- Tartalom: Logó (bal) + desktop nav linkek (középen/jobb) + „Ajánlatot kérek" gomb (jobb)
- Mobil: hamburger ikon → teljes képernyős overlay menü (nav linkek + CTA gomb)
- **Fontos:** mobilon is látszik a „LINARDICS KFT." felirat a logó mellett

### Footer
- Háttér: `#060f1a`, felső border: `rgba(255,255,255,0.08)`
- Bal: logó + cégnév + cím + elérhetőség
- Jobb: navigációs linkek (Szolgáltatásaink, Géppark, Referenciák, Rólunk, GYIK, Kapcsolat)
- Alul: copyright + ISO szöveg

### Navigációs linkek
- Szolgáltatásaink → `szolgaltatasok/index.html`
- Géppark → `geppark.html`
- Referenciák → `referenciak.html`
- Rólunk → `rolunk.html`
- GYIK → `gyik.html`
- Kapcsolat / Ajánlatkérés → `kapcsolat.html`

---

## 5. Komponensek

### 5.1 Hero kártyák (jobb oldal, desktop)
3 kártya, egymás alatt, `width: 320px`, alul igazítva a bal oldali tartalomhoz.
- Felső kártya: Ajánlatkérés (mérnök portré fotóval, piros overlay)
- Középső: Géppark (épület fotóval)
- Alsó: Referenciák (lézervágás fotóval)
- Belépési animáció: alulról felúszás, staggered delay

### 5.2 Expandáló service panelek
5 panel, flex container, `height: 480px`.
- Default: `flex: 1` (egyenlő szélesség)
- Container hover: `flex: 0.75` (összes kicsit összeszorul)
- Panel hover: `flex: 1.8` (aktív panel kitágul)
- `transition: flex 0.55s cubic-bezier(0.4, 0, 0.2, 1)`
- GPU layer: `transform: translateZ(0)`, `will-change: flex-grow`
- Hover-re: szám (`01`–`05`) eltűnik, szöveg body beúszik (`opacity + translateY`)
- Háttérkép: `transform: scale(1.06)` default, `scale(1.13)` hover-re

### 5.3 CTA szekció (minden oldalon)
Piros háttér (`#cc2222`), radial vignette overlay (ellipszis, sarkok sötétednek), bal felső sarkdísz (2 fehér vonal), fehér szöveg + fehér clip-path gomb.
**Szöveg:** „Kész az ajánlatra? Írjon nekünk." + alcím + „Ajánlatot kérek" gomb.

### 5.4 Statisztika kártyák (Miért mi? szekció)
2×2 grid, `lg:w-80 xl:w-96`:
- 30+ Év tapasztalat (sötét háttér)
- ISO 9001 (piros háttér)
- 8500 m² gyártóterület (sötét háttér)
- 15+ CNC gép (sötét háttér)

### 5.5 Gombok
**Elsődleges (piros):**
`bg-[#cc2222]` + `btn-clipped` + fehér szöveg + hover animáció

**Másodlagos (keretes):**
Átlátszó háttér + fehér border + fehér szöveg

**Hover animáció (minden `.btn-clipped` gomb):**
Szöveg (`.btn-label`) balra tolódik 8px-t, jobb oldalon nyíl ikon becsúszik belülről. Csak `transform` + `opacity` animál — GPU-accelerated.

### 5.6 Preloader
- Teljes képernyős overlay (`position: fixed; z-index: 9999`)
- Logó + piros progress bar
- `DOMContentLoaded + 350ms` után eltűnik, `opacity: 0 + visibility: hidden` fade-del
- `prefers-reduced-motion` esetén azonnal eltűnik

---

## 6. Animációk és interakciók

### Scroll reveal
- Osztály: `.reveal`
- Alap: `opacity: 0; transform: translateY(56px)`
- Trigger: `IntersectionObserver`, threshold `0.08`, rootMargin `0px 0px -60px 0px`
- Megjelenés: `opacity: 1; transform: translateY(0)`, `transition: 1.6s cubic-bezier(0.22, 1, 0.36, 1)`
- Opcionális delay: `data-delay="0.15"` (másodpercben)
- Alkalmazva: szekció fejlécek, service panelek, CTA szöveg + gomb

### Hero parallax
- A hero szekció háttere egy különálló `<div id="hero-parallax-bg">` réteg
- A hero section `overflow: hidden`, a háttér div `inset: -20% 0` (extrafej, hogy ne látszódjon vége scroll közben)
- `scroll` event → `requestAnimationFrame` → `translateY(scrollY * 0.45px)`
- `will-change: transform` a teljesítményért
- `prefers-reduced-motion` esetén kikapcsol
- Mobilon is működik (ez a megközelítés nem törli a Safari `background-attachment: fixed` bug-ja)

### Számláló animáció
- Osztály: `.counter`, attribútumok: `data-target`, `data-suffix`
- IntersectionObserver triggereli a "Miért mi?" szekción
- Időtartam: 1400ms, 16ms lépésköz, lineáris increment
- 300ms késleltetéssel indul a szekció megjelenése után

### Miért mi? szekció
- Háttérkép: `scale(1.08)` alapból, `scale(1)` animál 8 másodperc alatt megjelenéskor (zoom-out)
- Feature sorok: staggered `opacity + translateY` (`0s`, `0.12s`, `0.24s`, `0.36s` delay)
- Stat kártyák: staggered `opacity + scale` (`0.1s`–`0.5s` delay)

### Service slider (mobil / thumbnail)
- 5 kép, 3200ms időközönként vált
- Aktív slide: `opacity: 1; transform: scale(1)`, kilépő: `opacity: 0; scale(1.06)`
- Progress bar: CSS `width` transition `3200ms linear`
- Cím fade-el vált (opacity + translateY)

### Navbar scroll
- `window.scrollY > 50` → háttér + blur + border + `py-3`
- `window.scrollY ≤ 50` → visszaáll `py-5`-re, átlátszó háttér

---

## 7. SEO implementáció

### Meta tagek (minden oldalon)
- `<title>` — oldalanként egyedi, max ~60 karakter
- `<meta name="description">` — 120–160 karakter
- `<meta name="robots" content="index, follow">`
- `<link rel="canonical">`
- Open Graph: `og:type`, `og:url`, `og:title`, `og:description`, `og:image`, `og:locale`, `og:site_name`
- Twitter Card: `summary_large_image`, `twitter:title`, `twitter:description`, `twitter:image`

### Schema.org JSON-LD

| Oldal | Schema típus |
|---|---|
| Főoldal | `LocalBusiness`, `WebSite` (SearchAction) |
| Szolgáltatás aloldalak | `Service`, `BreadcrumbList` |
| Géppark, Rólunk | `BreadcrumbList` |
| GYIK | `FAQPage` |

### Fájlok
- `sitemap.xml` — 12 URL, prioritások és changefreq értékekkel
- `robots.txt` — `User-agent: *`, `Allow: /`, Sitemap hivatkozás

### Képek
- `alt` szövegek minden `<img>`-en
- `loading="lazy"` minden nem-above-fold képen
- `og:image` = `https://linardics.hu/assets/images/og-image.jpg` (produkciós verzióhoz generálni kell)

---

## 8. Teljesítmény

### Képek
Minden kép WebP formátum, `q=82` minőséggel kódolva.

| Fájl | Méret |
|---|---|
| hero-building.webp | 144 KB |
| engineer.webp | 36 KB |
| laser-cutting.webp | 80 KB |
| csohajlitas.webp | 88 KB |
| porfestes.webp | 36 KB |
| lemezhajlitas.webp | 40 KB |
| komplex-projektek.webp | 164 KB |
| favicon.png | 27 KB |
| linardics-logo.png | 27 KB (átmeneti — fehér WebP logó szükséges) |

### Betöltési stratégia
- Above-fold képek (`engineer.jpg` = mérnök portré a hero kártyában): `eager` (nincs lazy load)
- Minden többi kép: `loading="lazy"`
- Hero videó: `preload="none"`, IntersectionObserver tölti csak akkor ha látható
- Fontok: `preconnect` Google Fonts-hoz

### Produkciós verzióhoz szükséges
- Tailwind CSS CDN → build-elt, purge-olt CSS csere (jelenleg ~3 MB CDN vs ~10–30 KB build)
- Minified HTML/CSS/JS
- HTTP/2, gzip/Brotli szerver oldalon
- Cache headers statikus fájlokhoz

---

## 9. Akadálymentesség

- `lang="hu"` az `<html>` tagen
- `alt` szövegek képeken
- `aria-label` hamburger menün
- Fókusz állapotok gombokon (produkciós verzióban explicit `:focus-visible` stílus szükséges)
- `prefers-reduced-motion` figyelembe véve: hero videó és parallax kikapcsol, preloader azonnali

---

## 10. Hiányos elemek (production-ready verzióhoz szükséges)

### Tartalom
- **Partner/referencia logók:** Jelenleg placeholder szekció. Valós partner cégek SVG vagy WebP logói kellenek.
- **Fehér logó WebP:** `linardics-logo.png` helyett dedikált fehér háttér nélküli WebP logó kell a navbarhoz és footerhez.
- **OG image:** `og-image.jpg` hiányzik — 1200×630px social share kép kell.
- **Referencia projektek:** `referenciak.html` placeholder tartalommal van — valós projekt fotók, leírások.
- **Rólunk oldal fotók:** Csapat/épület fotók a story szekcióhoz.

### Funkcionális
- **Kapcsolati form backend:** A wizard csak frontend demo. Produkciós verzióban e-mail küldés szükséges (pl. SMTP / SendGrid / Mailgun).
- **GDPR / Cookie consent:** Banner hiányzik — jogi kötelező EU-ban.
- **404 oldal:** Egyedi hibaoldal nincs.
- **Fájlfeltöltés:** A drag-and-drop csak UI — valós fájlfeltöltési endpoint kell.
- **Google Analytics / Search Console:** Tracking kód nincs az oldalon.

### Technikai
- **Tailwind CSS build:** A CDN verziót le kell cserélni.
- **HTTPS:** Szerver oldalon HTTPS kötelező (canonical URL-ek már https://-t feltételeznek).
- **PHP/Laravel backend** (ha egyedi CMS): Az audit alapján tervezett produkciós stack.

---

## 11. Deploy struktúra

**Szerver:** cPanel, `public_html/linardics.o11.hu`
**GitHub repo:** `https://github.com/birotamas/linardics-hu` (privát)
**Deploy módszer:** cPanel Git Version Control → Manual Pull

**Fájlstruktúra:**
```
/
├── index.html
├── geppark.html
├── gyik.html
├── kapcsolat.html
├── referenciak.html
├── rolunk.html
├── sitemap.xml
├── robots.txt
├── assets/
│   └── images/
│       ├── hero-building.webp
│       ├── engineer.webp
│       ├── laser-cutting.webp
│       ├── csohajlitas.webp
│       ├── porfestes.webp
│       ├── lemezhajlitas.webp
│       ├── komplex-projektek.webp
│       ├── favicon.png
│       └── linardics-logo.png
└── szolgaltatasok/
    ├── index.html
    ├── cnc-lemezmegmunkalas.html
    ├── lemezhajlitas.html
    ├── csohajlitas.html
    ├── porfestes.html
    └── komplex-projektek.html
```

---

## 12. Audit megfelelőség (Kutassy Funnels)

| Audit pont | Státusz |
|---|---|
| Mobile-first responsive layout | ✅ |
| Gyors betöltés (optimalizált képek) | ✅ WebP, lazy load |
| Konverziós CTA minden oldalon | ✅ Egységes piros szekció |
| Egyértelmű értékpropozíció (főcím) | ✅ „Precíziós Lemezmegmunkálás" |
| Bizalomépítő elemek (ISO, tapasztalat, géppark) | ✅ |
| SEO meta tagek | ✅ Minden oldalon |
| Schema.org strukturált adatok | ✅ |
| Sitemap + robots.txt | ✅ |
| Könnyen kitölthető kapcsolati form | ✅ 3-lépéses wizard |
| Gyors kapcsolati lehetőség (telefon kiemelve) | ✅ |
| Partner/referencia logók | ⚠️ Placeholder — valós logók kellenek |
| GDPR cookie consent | ❌ Hiányzik |
| Google Analytics | ❌ Nincs tracking kód |
| 404 oldal | ❌ Hiányzik |
| Kép alt szövegek | ✅ |
| Canonical URL-ek | ✅ |

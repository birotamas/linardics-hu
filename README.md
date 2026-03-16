# Linardics Kft. – Weboldal

Precíziós lemezmegmunkálás, Székesfehérvár. ISO 9001 tanúsított.

## Tech stack

- HTML5 + Tailwind CSS (CDN)
- Vanilla JS
- Nincs build tool

## Lokális fejlesztés

```bash
cd linardics_hu
python3 -m http.server 8000
# → http://localhost:8000
```

## Struktúra

```
linardics_hu/
├── index.html                          # Főoldal
├── geppark.html                        # Géppark
├── referenciak.html                    # Referenciák
├── rolunk.html                         # Rólunk
├── kapcsolat.html                      # Ajánlatkérés (3-lépéses wizard)
├── gyik.html                           # GYIK (FAQPage schema)
├── sitemap.xml
├── robots.txt
├── assets/
│   ├── images/                         # WebP képek
│   └── videos/
└── szolgaltatasok/
    ├── index.html                      # Szolgáltatások áttekintő
    ├── cnc-lemezmegmunkalas.html
    ├── lemezhajlitas.html
    ├── csohajlitas.html
    ├── porfestes.html
    └── komplex-projektek.html
```

## Deploy

GitHub → cPanel Git Version Control → Pull or Deploy

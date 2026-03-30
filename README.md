Backend Branding für REDAXO 5
========================

Mit dem AddOn lässt sich das Backend von REDAXO individuell gestalten. Ob Agentur-CI, Projektfarben oder Multidomain-Installationen – be_branding macht verschiedene REDAXO-Projekte auf einen Blick unterscheidbar. Inkl. Favicon-Generator für das Frontend aus dem Medienpool heraus.

![Backend Branding für REDAXO 5](https://raw.githubusercontent.com/danspringer/be_branding/master/assets/img/splashscreen.png "Backend Branding Redaxo 5")

> **Hinweis für Umsteiger von v1:** Das AddOn hat keinen eigenen Menüpunkt mehr in der linken Navigation. Die Einstellungen sind ab v2 unter **System &rsaquo; Backend-Branding** zu finden.

Features
--------

* Einfärben des Headers und REDAXO-Logos im Backend
* **Dark Mode:** Separate Farben für helles und dunkles Backend-Theme
* **Backend-Favicon einfärben** – normal (gefärbtes REDAXO-R) oder invertiert (weißes R auf farbiger Fläche), pro Domain konfigurierbar
* **Invertiertes Favicon** wird als pixelgenaues SVG mit dem originalen REDAXO-R generiert – kein Imagick nötig
* **Live-Farbvorschau:** Farbänderungen werden sofort im Backend sichtbar – ohne Speichern
* **Live-Favicon-Vorschau:** Tab-Favicon wechselt beim Konfigurieren direkt mit
* **Hex-Farbcodes:** `#ff6400` wird automatisch in `rgba()` umgerechnet
* Anpassung des Login-Screen-Hintergrundbildes aus dem Backend heraus
* Anzeigen eines Projektlogos im Login-Screen und in der Navigation
* Anzeigen eines Agenturlogos, Adresse o.ä. in den Credits
* Anzeigen des Agenturnamens im Footer des Backends
* Hinweisleiste (Border) am oberen Rand des Backends
* **Custom CSS:** Freies CSS-Feld für individuelle Backend-Anpassungen
* Generierung von Frontend-Favicons aus dem Medienpool heraus (SVG empfohlen, PNG/ICO als Fallback)
* **AVIF-Support** für Login-Hintergrundbilder (zusätzlich zu WebP/JPG)
* Multidomainfähigkeit mit YRewrite – pro Domain ein eigenes Branding-Profil
* **Favicon-Vorschau** direkt in der Konfigurationsseite

---

Benutzung
---------

### Wo finde ich das AddOn?

Ab Version 2 ist das AddOn unter **System &rsaquo; Backend-Branding** zu finden. Es hat keinen eigenen Menüpunkt mehr in der linken Navigation – das macht Sinn, da man es typischerweise nur einmal zum Einrichten benötigt.

### Login-Seite individualisieren

Unter `System > Backend-Branding > Backend Branding > Projektbranding` den gewünschten Hintergrund für den Login-Screen wählen:

| Option | Beschreibung |
|---|---|
| Eigenes Hintergrundbild | Bild aus dem Medienpool, automatisch als AVIF, WebP und JPG ausgeliefert |
| Primärfarbe | Vollflächige Primärfarbe als Hintergrund |
| Sekundärfarbe | Vollflächige Sekundärfarbe als Hintergrund |
| Farbverlauf | Linearer Verlauf von Primär- zu Sekundärfarbe |
| REDAXO-Standard | Das Original-Hintergrundbild von REDAXO |

Ein hinterlegtes Projektlogo erscheint automatisch oberhalb der Login-Box.

### Farben eingeben

Farbfelder akzeptieren zwei Formate:

* **RGBa:** `rgba(255, 100, 0, 1)` – mit Alpha-Kanal für Transparenz
* **Hex:** `#ff6400` – wird beim Verlassen des Feldes automatisch in rgba umgerechnet

Mit aktiviertem Colorpicker kann die Farbe auch grafisch gewählt werden.

### Dark Mode

Unter `Farbschema` können separate Farben für den Dark Mode hinterlegt werden. Diese werden automatisch verwendet, wenn der Browser oder Nutzer den Dark Mode aktiviert hat. Leer lassen bedeutet: gleiche Farben wie im hellen Modus.

### Backend-Favicon

Unter `Backend-Favicon` stehen drei Optionen zur Verfügung:

| Option | Beschreibung |
|---|---|
| REDAXO-Standard (nicht färben) | Das originale REDAXO-Favicon bleibt unverändert |
| Primärfarbe | Das REDAXO-R wird in der Primärfarbe eingefärbt (Imagick benötigt) |
| Sekundärfarbe | Das REDAXO-R wird in der Sekundärfarbe eingefärbt (Imagick benötigt) |

Zusätzlich kann **Favicon invertieren** aktiviert werden: Das Addon generiert dann ein SVG-Favicon mit weißem REDAXO-R auf farbiger Fläche – besser sichtbar im Dark Mode und auf farbigen Browser-Tabs. Kein Imagick nötig.

Die Live-Vorschau zeigt das Favicon direkt beim Konfigurieren an und wechselt auch das Tab-Favicon der aktuellen Browser-Session sofort mit.

### Custom CSS

Das Feld `Freies CSS für das Backend` erlaubt beliebige CSS-Regeln, die direkt in den Backend-Output eingebettet werden. Eingabe ohne umschließendes `<style>`-Tag. Nützlich für Anpassungen, die über Farben und Logo hinausgehen.

### Multidomainfähigkeit mit YRewrite

Unter `System > Backend-Branding > Konfiguration > Domainprofile aktivieren` wird die Multidomain-Unterstützung eingeschaltet. Anschließend stehen für jede in YRewrite angelegte Domain separate Branding-Profile zur Verfügung.

Das Backend sieht je nach Login-URL unterschiedlich aus:
* `domain-a.de/redaxo` → Profil A (eigene Farben, Logo, Hintergrundbild, Favicon)
* `domain-b.de/redaxo` → Profil B (eigene Farben, Logo, Hintergrundbild, Favicon)

Alles unter einer gemeinsamen REDAXO-Installation.

### Frontend-Favicons generieren und einbinden

Unter `System > Backend-Branding > Frontend-Favicons` stehen pro YRewrite-Domain zwei Methoden zur Verfügung:

#### SVG-Favicon (empfohlen)

Eine SVG-Datei direkt aus dem Medienpool wählen – kein Imagick nötig. SVGs skalieren pixelgenau auf jede Auflösung und unterstützen Dark Mode direkt in der Datei:

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
  <style>
    .icon { fill: #ff6400; }
    @media (prefers-color-scheme: dark) {
      .icon { fill: #ffffff; }
    }
  </style>
  <circle class="icon" cx="50" cy="50" r="50"/>
</svg>
```

Alle modernen Browser (Chrome, Firefox, Edge, Safari ab 2022) unterstützen SVG-Favicons.

#### PNG/ICO-Favicons (Fallback)

Quelldatei aus dem Medienpool wählen (empfohlen: transparentes PNG, mindestens 310×310 Pixel). Das AddOn generiert daraus alle benötigten Formate für ältere Browser und Apple-Geräte. Erfordert Imagick auf dem Server.

#### Einbindung im Template

Folgenden Code einmalig im `<head>`-Bereich des Templates einfügen:

```php
<?= be_branding::getFrontendFavicons(rex_yrewrite::getCurrentDomain()->getId()) ?>
```

Das gibt automatisch den SVG-Link zuerst aus, gefolgt von den PNG/ICO-Fallbacks. Browser mit SVG-Support ignorieren die PNG-Links automatisch:

```html
<!-- Ausgabe im Frontend -->
<link rel="icon" type="image/svg+xml" href="https://example.com/media/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href=".../favicon-32x32-ff6400--1.png">
<link rel="apple-touch-icon" sizes="180x180" href=".../apple-touch-icon-180x180-ff6400--1.png">
<link rel="shortcut icon" type="image/x-icon" href=".../favicon--1.ico">
<meta name="theme-color" content="#ff6400">
```

---

Migration von v1 auf v2
------------------------

Das Update ist **abwärtskompatibel**. Bestehende Konfigurationen werden beim ersten Backend-Aufruf nach dem Update automatisch migriert. Ein Protokoll der Migration erscheint einmalig als Hinweis im Backend.

**Wichtig:** Das AddOn hat keinen eigenen Menüpunkt mehr. Nach dem Update findet man es unter **System &rsaquo; Backend-Branding**.

Neu in v2 hinzugekommene Einstellungen (Dark Mode, Custom CSS, invertiertes Favicon) starten leer und greifen nicht in bestehende Konfigurationen ein.

---

Voraussetzungen
---------------

* REDAXO >= 5.13
* YRewrite >= 2.7
* PHP >= 8.1
* Imagick (optional, für normales Favicon-Einfärben)

---

Änderungshistorie
-----------------

### Version 2.0.0

**Breaking Change:**
* Das AddOn ist nicht mehr als eigener Menüpunkt in der linken Navigation zu finden. Die Einstellungen befinden sich jetzt unter **System &rsaquo; Backend-Branding**.

**Neu:**
* SVG-Favicon für das Frontend – kein Imagick nötig, Dark-Mode-fähig, pixelgenau
* Backend-Favicon: invertierter Modus (weißes REDAXO-R auf farbiger Fläche) als SVG, kein Imagick nötig
* Backend-Favicon: Farbauswahl (Primär-/Sekundärfarbe) und Invertierung pro Domain konfigurierbar
* Live-Favicon-Vorschau: Tab-Favicon wechselt beim Konfigurieren direkt mit
* Dark Mode Support: separate Primär- und Sekundärfarbe für helles/dunkles Backend-Theme
* Live-Farbvorschau: Farbänderungen sofort im Backend sichtbar, ohne Speichern
* Hex-Farbcodes (`#rrggbb`) werden automatisch in rgba umgerechnet
* Custom CSS-Feld: freies CSS pro Profil/Domain
* AVIF-Support für Login-Hintergrundbilder
* Favicon-Vorschau direkt in der Konfigurationsseite
* Automatische Migration bestehender v1-Konfigurationen mit Protokoll im Backend

**Verbessert:**
* `getCurrentBeDomainId()` gecacht – kein wiederholter Datenbank-Aufruf pro Request mehr
* CSS-Ausgabe aus `boot.php` in Klassenmethoden ausgelagert (`buildHeaderCss()`, `buildLoginCss()`)
* `install.php` und `update.php` teilen gemeinsame Logik (`be_branding_setup.php`)
* `uninstall.php` räumt jetzt alle Media-Manager-Typen vollständig auf
* `checkExtension()` unterstützt WebP und AVIF
* Farbwerte in der CSS-Ausgabe durch `rex_escape()` abgesichert (XSS-Schutz)
* HTTP_HOST wird vor der Datenbankabfrage validiert
* Gradient-CSS-Fehler behoben (fehlende Klammer bei `-moz-`/`-webkit-linear-gradient`)
* Redundante `rgb2hex()`-Funktion bereinigt (BC-Alias auf `rgba2hex()`)
* Veralteten REX-5.12-Versionscheck entfernt (REX ≥ 5.13 ist Voraussetzung)
* Alle Fragments: konsequentes `rex_escape()` für alle ausgegebenen Werte

### Version 1.8.1

=== 29.01.2024 ===

Letzte Version vor REDAXO 5.16 – Anpassung der Splashscreen-URL in der README.

### Version 1.8.0

=== 11.10.2022 ===

**Neu:**
* Multidomainfähigkeit: Pro YRewrite-Domain ein eigenes Backend-Branding-Profil
* Backend-Favicons werden pro Domain eingefärbt
* Frontend-Link im Header (be_style/customizer) wird je nach Domain angepasst
* Favicon-Einstellungen in Tabs dargestellt

**Behoben:**
* Logo bei Installation im Unterordner wird korrekt angezeigt ([@aeberhard](https://github.com/aeberhard))

### Version 1.7.1

=== 12.08.2022 ===

**Behoben:**
* Scripts im pageHeader funktionieren wieder (Watson, Fontawesome-Picker, Structure Tweaks etc.)

### Version 1.7

=== 07.01.2022 ===

**Breaking Changes:**
* Nur noch kompatibel mit REDAXO ab 5.13.0
* YRewrite wird vorausgesetzt
* `REX_BE_BRANDING[type=fe_favicon]` entfernt – bitte `be_branding::getFrontendFavicons()` verwenden

**Neu:**
* Frontend-Favicons für Multidomain-Installationen mit YRewrite

### Version 1.6-beta

=== 04.05.2021 ===

* Mehr Optionen für den Login-Screen (eigenes Bild, Primär-/Sekundärfarbe, Verlauf, REDAXO-Standard)
* Umstellung auf eigene REDAXO-Variable `REX_BE_BRANDING[]`

### Ältere Versionen

Versionshistorie < 1.6 siehe [GitHub Releases](https://github.com/medienfeuer/be_branding/releases).

---

Autor
-----

Daniel Springer, Medienfeuer
[www.medienfeuer.de](https://www.medienfeuer.de)

---

Credits
-------

**border**
Ursprünglich aus dem AddOn „out5" von Oliver Kreischer / FOR.

**hex2rgb, rgb2hex, makeFavIcon**
Basierend auf Funktionen von Jan Kristinus aus dem R4-AddOn „Backend Utilities / Colorizer" von RexDude.

**FaviconGenerator**
Class generation favicon for browsers and devices.
* Author: Dmitry Mamontov <d.slonyara@gmail.com>
* License: [BSD 3-Clause](http://www.opensource.org/licenses/BSD-3-Clause)
* [github.com/dmamontov/favicon](https://github.com/dmamontov/favicon)

**jQuery MiniColors**
A tiny color picker built on jQuery.
Developed by Cory LaViska for A Beautiful Site, LLC.
Licensed under the [MIT License](http://opensource.org/licenses/MIT).
[Demo & Dokumentation](http://labs.abeautifulsite.net/jquery-minicolors/)
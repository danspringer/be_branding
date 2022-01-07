Backend Branding für REDAXO 5
========================

Mit dem AddOn lässt sich das Backend von Redaxo branden, um dem Backend eine individuellere Note zu geben. Damit kann man, wenn man mehrere REDAXO-Projekte betreut, das Backend auf einen Blick schneller unterscheiden. Inkl. Favicon-Generator für das Frontend aus dem Medienpool heraus.

![Backend Branding für REDAXO 5](https://camo.githubusercontent.com/862161690ca664720e911080eb44709619245d0bd554d55b11e5a6fb0e352d4d/68747470733a2f2f692e696d6775722e636f6d2f6f6971355a716d2e706e67 "Backend Branding Redaxo 5")

Features
-------
* Einfärben des Headers und REDAXO-Logos im Backend
* Einfärben des Favicon im Backend (Imagemagick benötigt)
* Einfache Anpassung des Loginscreen-Hintergrundbildes ab REDAXO 5.12 aus dem Backend heraus
* Generierung von Favicons für das Frontend aus dem Medienpool heraus (Imagick benötigt)
* Frontend-Favicons für Multidomain-Installationen mit YRewrite
* Anzeigen eines Projektlogos im Login-Screen und in der Navigation des Backends
* Anzeigen eines Logos und Anschrift o.ä. in den Credits
* Anzeigen eines Namens im Footer des Backends 
* Anzeigen einer zusätzlichen Hinweisleiste (border) im Backend

Benutzung
-------
### Login-Seite individualisieren ###
Um die Login-Seite von REDAXO mit einem eigenen Hintergrundbild zu individualisieren, muss in den AddOn-Einstellungen unter <code>Backend-Branding > Projektbranding</code> als Hintergrund des Login-Screens die Option "Eigenes Hintergrundbild" auswählen und aus dem Medienpool ein geeignetes Bild auswählen.<br>
Die Farben der Login-Box oder des Farbverlaufs als Hintergrund werden aus dem Farbschema der Primärfarbe und Sekundärfarbe gebildet. Sollte ein Projektlogo hinterlegt sein, wird dieses oberhalb der Login-Box angezeigt.<br><br>
Folgende Optionen für den Hintergrund des Login-Screens stehen zur Verfügung:
* Eigenes Hintergrundbild
* Primärfarbe aus Farbschema
* Sekundärfarbe aus Farbschema
* Farbverlauf aus Primärfarbe und Sekundärfarbe
* Das Standard-Hintergrundbild von REDAXO


### Backend individualisieren ###
Das AddOn bietet die Möglichkeit, das Aussehen von REDAXO im Backend anzupassen, um es so stärker individualisieren zu können. Dies eignet sich, um bei Kundenprojekten näher an der CI zu sein oder um unterschiedliche REDAXo-Installationen optisch leichter unterscheiden zu können.<br>
Hierzu können eine Primärfarbe und eine Sekundärfarbe im AddOn definiert werden. Mit diesen beiden Farben wird das REDAXO-Logo eingefärbt, die Header-Leiste im Backend sowie die Login-Box eingefärbt.

#### Backend-Favicon einfärben ####
Das Backend-Favicon von REDAXO kann im AddOn unter <code>Konfiguration > Favicon im Backend färben?</code> umgefärbt werden. Hierbei wird die Primärfarbe des Farbschemas verwendet.

### Frontend-Favicons generieren und einbinden ###
Das AddOn ermöglicht die einfache Generierung und Einbindung aller möglichen Favicon-Formate für das Frontend.<br>
Hierzu muss YRewrite installiert und aktiviert sein. Außerdem muss mindestens eine Domain in YRewrite angelegt sein.
Für jede Domain steht anschließend ein Auswahlfeld für den Medienpool zur Verfügung, mit welchem man eine Quelldatei wählen kann, aus welcher dann die entsprechenden Favicons generiert werden. (Empfohlen wird eine PNG-Datei mit mindestens 310x310 Pixeln)<br><br>
Um die Favicons im Template einzubinden wird folgender PHP-Code im <code>head</code>-Bereich des Templates eingefügt:
<p><code>be_branding::getFrontendFavicons( rex_yrewrite::getCurrentDomain()->getId() )</code></p>

Last Changes
-------
### Version 1.7 ###

=== 07.01.2022 ===

<strong>ACHTUNG - Breaking-Changes:</strong>
* Die Version 1.7 ist nur noch kompatibel mit REDAXO ab Version 5.13.0
* YRewrite wird vorausgesetzt
* Die REX-Variable <code>REX_BE_BRANDING[type=fe_favicon]</code> wird in dieser Version nicht mehr berücksichtigt. Zur Ausgabe der Frontend-Favicons bitte den PHP-Code <code>be_branding::getFrontendFavicons( rex_yrewrite::getCurrentDomain()->getId() )</code> nutzen.

<strong>Fixed:</strong>
* Anpassungen an die neue REDAXO-Backend-Struktur ab Version 5.13

<strong>Neu:</strong>
* Frontend-Favicons nun auch für Multidomain-Installationen mit YRewrite 
* Hilfedatei ergänzt

### Version 1.6-beta2 ###

=== 05.05.2021 ===
* Nach Update, Installation oder Reinstallation wird der Template-Cache geleert, damit die Ersetzung von <code>REX_BE_BRANDING[type=fe_favicon]</code> im Frontend korrekt funktioniert.

### Version 1.6-beta ###

=== 04.05.2021 ===
* <strong>NEU:</strong> Mehr Optionen zur Einstellung des neuen REX-Loginscreens (Eigenes Hintergrundbild, REDAXO-Standard-HG-Bild, Primärfarbe, Sekundärfarbe oder Verlauf wählbar)
* Umstellung auf eigene Redaxo-Variable <code>REX_BE_BRANDING[]</code> anstatt <code>str_replace()</code>
* Auslagerung der Funktionen in der boot.php in die Klasse <code>be_branding</code>
* Redaxo-Cache wird nach Änderung des Frontend-Favicon gelöscht
* Fallback-Hintergrundbild im Loginscreen wird nun nur noch bei mobiler Ansicht angezeigt, nicht bei Desktop-Version, da es zu einem kurzen Zitter-Effekt gekommen ist

### Version 1.5.1 ###

=== 07.04.2021 ===
* Anpassung background-properties für #rex-page-login: Hintergrund-Bild für Login-Screen nun auch bei mobiler-Anicht
* Anpassung Screenshot-URL in README.md

### Version 1.5 ###

=== 26.03.2021 ===
* Anpassung an neuen Login-Screen ab REDAXO 5.12
* Hintergrundbild des neuen Login-Screens (REX 5.12) kann nun bequem im AddOn eingestellt werden
* Media-Manager-Types für JPG und WebP werden bei Reinstall und Update in REX 5.12 angelegt.
* Credits wurden nicht mehr angezeigt => fixed Danke @helpy

### Version 1.4.2 ###

=== 28.09.2020 ===
* Bei einem Reinstall wurde versehentlich noch ein Ordner "favicon" im Root generiert.
* vendor/favicon/src/FaviconGenerator.php entfernt.
* Mögliches Überbleibsel im Root (favicon-Ordner) wir bei Reinstall und Update gelöscht.

### Version 1.4.1 ###

=== 28.09.2020 ===
* Umstellung auf Fragments, wo möglich.
* Frontend-Favicons werden nun nur noch beim Speichern auf der Einstellungsseite für die Frontend-Favicons generiert.

### Version 1.4 ###

=== 24.09.2020 ===
* Neu: Ein <code>favicon.ico</code> wird zur Sicherheit noch ins Root-Verzeichnis der Website gelegt, da manche Suchmaschinen einfach nach dem Standardpfad schauen.
* Umstellung auf die Klasse <code>fe_favicon</code> zur Generierung der Icons und zur Ausgabe des HTML-Codes. 
* Die Frontend-Favicons werden nun nur noch generiert, wenn Sie noch nicht im Assets-Ordner des AddOns existieren, nicht mehr bei jedem Seitenaufruf.
* Die <code>.settings</code>-Datei für die Frontend-Favicons wird bei einem Update oder Reinstall gelöscht, falls sie aus früheren Versionen noch fehlerhaft war.

### Version 1.3.4 ###

=== 22.09.2020 ===
* Behebt die Warnings bzgl. <code>array_key_exists()</code> in FE_FaviconGenerator.php - Danke an Serhan Sidan @ <a href="https://www.mattomedia.de">Mattomedia.de</a>
* Anpassung an Imageick-Version zur korrekten Generierung von Favicons.

### Version 1.3.3 ###

=== 13.03.2020 ===
* Behebt einen Fehler der Frontend-Favicons der Version 1.3.2. Korrigiert die Einbindung der Color-Pickers.

### Version 1.3.2 ###

=== 12.03.2020 ===
* Backend-Favicon-Update für Rex 5.10.0. Diese Verson korrigiert die Einbindung der gefärbten Icons im Backend und berücksichtigt die verschiedenen Einbindungsarten der Versionen 5.0-5.7, von 5.7-5.8, 5.8.0 und ab 5.8.1 bis zu 5.10.0.

### Version 1.3.1 ###

=== 08.11.2019 ===
* Backend-Favicon-Update für Rex 5.8.1. In der Redaxo Version 5.8.1 wurden die Favicons geändert und anders eingebunden. Diese Verson korrigiert die Einbindung der gefärbten Icons im Backend und berücksichtigt die verschiedenen Einbindungsarten der Versionen 5.0-5.7, von 5.7-5.8, 5.8.0 und ab 5.8.1.

### Version 1.3 ###

=== 03.09.2019 ===
* Backend-Favicon-Update für Rex 5.8.0. In der Redaxo Version 5.8.0 wurden die Favicons geändert und anders eingebunden. Diese Verson korrigiert die Einbindung der gefärbten Icons im Backend und berücksichtigt die verschiedenen Einbindungsarten der Versionen 5.0-5.7, von 5.7-5.8 und ab 5.8.
* Fixed: SVG-Branding-Logo wurde nicht geladen, wenn Frontend durch maintenance-AddOn gesperrt war. Danke @helpy

### Version 1.2 ###

=== 28.06.2019 ===
* rex::isFrontend() entfernt, da die Funktion erst ab REDAXO 5.7 verfügbar ist und niedrigere Versionen beim Update des AddOns einen Whoops bekommen haben.
* Meldungen angepasst, wenn ImageMagick auf dem Server nicht verfügbar ist und man Favicons für das Frontend generieren wollte.
* SVG-Unterstützung für das Projekt-Logo im Backend

### Version 1.1 ###

=== 11.05.2019 ===
* NEU: Favicon-Generator fürs Frontend.
* Unter dem neuen Menüpunkt Frontend-Favicon kann eine Datei aus dem Medienpool ausgewählt werden, die dann automatisch in die jeweiligen Formate für Favicons generiert wird.
* Ebenfalls kann die Tile-Color für Android-Geräte und Windows-Tiles angegeben werden (Das Favicon wird dabei nicht gefärbt).
* Die Einbindung ins Frontend ist mittels dem Snippet REX_BE_BRANDING[type=fe_favicon] im Template im <head>-Bereich möglich.

### Version 1.0.9 ###

=== 19.03.2019 ===
* Wenn Redaxo in einem Unterordner installiert ist, gab es einen Fehler, wenn man die Option für das Färben der Favicons aktiviert hatte.
* Tile-Color für Android Endgeräte ergänzt.
* Pfade für Einbindung der Favicons gefixt. 

### Version 1.0.8 ###

=== 13.03.2019 ===
* Favicon-Update für Rex 5.7.0. In der Redaxo Version 5.7.0 wurden die Favicons geändert und anders eingebunden. Diese Verson korrigiert die Einbindung der gefärbten Icons je nachdem, ob die Redaxo-Version größer oder kleiner als V 5.7.0 ist.

### Version 1.0.7 ###

=== 26.02.2019 ===
* Der Ordner assets/favicon und die Datei assets/favicon/.original wurden ergänzt. Ohne den Ordner bzw. die Datei gibt es einen Fehler, wenn man das Favicon färben möchte.

### Version 1.0.6 ###

=== 19.10.2018 ===
* Der Servername für FavIcons für Android wurde noch mit R4-Methoden angeben und wurde auf R5 korrigiert.

### Version 1.0.5 ###

=== 22.08.2018 ===
* Diverse Notices gefixt

### Version 1.0.4 ###

=== 21.08.2018 ===
* Favicon im Backend kann gefärbt werden (Imagemagick benötigt)
* Übersichtlichere Aufteilung in Konfiguration und Branding-Page
* Umgestellt auf includeCurrentPageSubPath
* jQuery Colorpicker ist keine Pflicht mehr. Wenn ui_tools/jquery-minicolors installiert ist, wird es verwendet, ansonsten normales input-Feld oder selbst mitglieferter jQuery-Colorpicker
* In der Konfiguration kann aus den installierten Editoren der bevorzugte festgelegt werden (zur Zeit: ckEditor, ckEditor 5, Markitup / Markdown o. Textile, redactor 2, tinymce4.

### Version 1.0.3 ###

=== 05.06.2018 ===
* Bugfix Kompatibilität mit Quick Navigation


Autor
-------
Daniel Springer, Medienfeuer<br>
<a href="https://www.medienfeuer.de">www.medienfeuer.de</a>

Credits
-------
### border ###
"border" stammt aus dem (mittlerweile nicht mehr weitergeführten) AddOn "out5" von Oliver Kreischer / FOR. Vielen Dank!

### hex2rgb, rgb2hex und makeFavIco ###
Die Funktionen zur Umwandlung von RGB-, bzw. HEX-Werten und zum Färben des PNGs stammen von Jan Kristinus aus dem R4-AddON "Backend Utilities / Colorizer" von RexDude.

### FaviconGenerator ###
Class generation favicon for browsers and devices Android, Apple, Windows and display of html code. It supports a large number of settings such as margins, color, compression, three different methods of crop and screen orientation.
* @author    Dmitry Mamontov <d.slonyara@gmail.com>
* @copyright 2015 Dmitry Mamontov <d.slonyara@gmail.com>
* @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
* @version   Release: 1.0.0
* @link      https://github.com/dmamontov/favicon
* @since     Class available since Release 1.0.0

### jQuery MiniColors ###
A tiny color picker built on jQuery
Developed by Cory LaViska for A Beautiful Site, LLC

Licensed under the MIT license: http://opensource.org/licenses/MIT

Demo & Documentation
http://labs.abeautifulsite.net/jquery-minicolors/



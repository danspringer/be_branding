Backend Branding für REDAXO 5
========================

Mit dem AddOn lässt sich das Backend von Redaxo branden, um dem Backend eine individuellere Note zu geben. Damit kann man, wenn man mehrere REDAXO-Projekte betreut, das Backend auf einen Blick schneller unterscheiden.

![Backend Branding für REDAXO 5](https://i.imgur.com/DS3zRxo.png "Backend Branding Redaxo 5")

Features
-------
* Einfärben des Headers und REDAXO-Logos im Backend
* Einfärben des Favicon im Backend (Imagemagick benötigt)
* Generierung von Favicons für das Frontend aus dem Medienpool heraus (Imagemagick benötigt)
* Anzeigen eines Projektlogos im Login-Screen und in der Navigation des Backends
* Anzeigen eines Logos und Anschrift o.ä. in den Credits
* Anzeigen eines Namens im Footer des Backends 
* Anzeigen einer zusätzlichen Hinweisleiste (border) im Backend


Last Changes
-------
### Version 1.3 ####

=== 03.09.2019 ===
* Backend-Favicon-Update für Rex 5.8.0. In der Redaxo Version 5.8.0 wurden die Favicons geändert und anders eingebunden. Diese Verson korrigiert die Einbindung der gefärbten Icons im Backend und berücksichtigt die verschiedenen Einbindungsarten der Versionen 5.0-5.7, von 5.7-5.8 und ab 5.8.
* Fixed: SVG-Branding-Logo wurde nicht geladen, wenn Frontend durch maintenance-AddOn gesperrt war. Danke @helpy

### Version 1.2 ####

=== 28.06.2019 ===
* rex::isFrontend() entfernt, da die Funktion erst ab REDAXO 5.7 verfügbar ist und niedrigere Versionen beim Update des AddOns einen Whoops bekommen haben.
* Meldungen angepasst, wenn ImageMagick auf dem Server nicht verfügbar ist und man Favicons für das Frontend generieren wollte.
* SVG-Unterstützung für das Projekt-Logo im Backend

### Version 1.1 ####

=== 11.05.2019 ===
* NEU: Favicon-Generator fürs Frontend.
* Unter dem neuen Menüpunkt Frontend-Favicon kann eine Datei aus dem Medienpool ausgewählt werden, die dann automatisch in die jeweiligen Formate für Favicons generiert wird.
* Ebenfalls kann die Tile-Color für Android-Geräte und Windows-Tiles angegeben werden (Das Favicon wird dabei nicht gefärbt).
* Die Einbindung ins Frontend ist mittels dem Snippet REX_BE_BRANDING[type=fe_favicon] im Template im <head>-Bereich möglich.

### Version 1.0.9 ####

=== 19.03.2019 ===
* Wenn Redaxo in einem Unterordner installiert ist, gab es einen Fehler, wenn man die Option für das Färben der Favicons aktiviert hatte.
* Tile-Color für Android Endgeräte ergänzt.
* Pfade für Einbindung der Favicons gefixt. 

### Version 1.0.8 ####

=== 13.03.2019 ===
* Favicon-Update für Rex 5.7.0. In der Redaxo Version 5.7.0 wurden die Favicons geändert und anders eingebunden. Diese Verson korrigiert die Einbindung der gefärbten Icons je nachdem, ob die Redaxo-Version größer oder kleiner als V 5.7.0 ist.

### Version 1.0.7 ####

=== 26.02.2019 ===
* Der Ordner assets/favicon und die Datei assets/favicon/.original wurden ergänzt. Ohne den Ordner bzw. die Datei gibt es einen Fehler, wenn man das Favicon färben möchte.

### Version 1.0.6 ####

=== 19.10.2018 ===
* Der Servername für FavIcons für Android wurde noch mit R4-Methoden angeben und wurde auf R5 korrigiert.

### Version 1.0.5 ####

=== 22.08.2018 ===
* Diverse Notices gefixt

### Version 1.0.4 ####

=== 21.08.2018 ===
* Favicon im Backend kann gefärbt werden (Imagemagick benötigt)
* Übersichtlichere Aufteilung in Konfiguration und Branding-Page
* Umgestellt auf includeCurrentPageSubPath
* jQuery Colorpicker ist keine Pflicht mehr. Wenn ui_tools/jquery-minicolors installiert ist, wird es verwendet, ansonsten normales input-Feld oder selbst mitglieferter jQuery-Colorpicker
* In der Konfiguration kann aus den installierten Editoren der bevorzugte festgelegt werden (zur Zeit: ckEditor, ckEditor 5, Markitup / Markdown o. Textile, redactor 2, tinymce4.

### Version 1.0.3 ####

=== 05.06.2018 ===
* Bugfix Kompatibilität mit Quick Navigation


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



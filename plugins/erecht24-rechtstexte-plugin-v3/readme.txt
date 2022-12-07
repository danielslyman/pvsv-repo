=== eRecht24 Rechtstexte für WordPress ===
Requires at least: 5.0
Tested up to: 6.1.99
Requires PHP: 7.1
Stable tag: 3.3.6

eRecht24 Rechtstexte ermöglicht die einfache Integration von Impressum und Datenschutzerklärung von eRecht24.

== Changelog ==
= 3.3.6 =
- Kompatibilität mit WordPress 6.1 getestet

= 3.3.5 =
- Bugfix: Fehlernachricht bei der Migration von der Version 2.x auf Version 3.x unterdrücken, wenn Shortcodes in der Navigation enthalten sind.

= 3.3.4 =
- Bugfix: Push an das Plugin schlägt bei einigen Hostern fehl, da die URL-Parameter zu Kleinbuchstaben umgewandelt wurden. #159
- Bugfix: Veralteten Filter `block_categories` ersetzen #158
- Bugfix: Überprüfung "WordPress-Adresse (URL) ist unverändert" soll ohne API-Schlüssel keinen Fehler ausgeben. #154
- Feature: Sicherstellen, dass die Aktivierung und Deaktivierungs-Hooks auch ausgeführt werden, wenn das Plugin-Verzeichnis anders benannt wurde.
- Tests für neue Funktionen und gelöste Probleme hinzugefügt

= 3.3.3 =
- Fehlermeldungen im WordPress Backend in Vorbereitung auf eine Wartung der eRecht24 Rechtstexte API verbessert.

= 3.3.2 =
- Kompatibilität mit WordPress 6.0 getestet

= 3.3.1 =
- Feature: Wechsel der URL des WordPress detektieren und Warnhinweis anzeigen #126
- Ausgabe des Debug-Logs um weitere Details ergänzen #148
- Bugfix: JavaScript im WordPress Backend nur auf Einstellungsseiten des Plugins laden

= 3.3.0 =
- Kompatibilität mit WordPress 5.9 getestet
- Bugfix: Google-Analytics Tracking Code wird unter Umständen unabhängig von Auswahl des Schalters eingebunden nach Neuinstallation #147
- Bugfix: Unter Umständen erscheint ein PHP-Warning im Frontend bei hinterlegtem Google-Analytics Tracking-Key nach Neuinstallation #146
- Bugfix: PHP-Warning "Cannot modify header information - headers already sent" im Pluginstatus-Tab #124

= 3.2.2 =
- Feature: Plugin-Status-Tabs überprüft nun die Funktionalität der WordPress REST-API #135
- Feature: Horizontales Scrollen bei Rechtstexten wird auf mobilen Endgeräten standardmäßig verhindert #138
- Feature: Kompatibilität mit Google Analytics 4 verbessert #132
- Bugfix: Kompatibilität mit Elementor verbessert #139
- Fehlermeldungen wurden verbessert #79 #135

= 3.2.1 =
- Bugfix: "Fatal error: Uncaught Error: Class 'Puc_v4p11_Vcs_Api' not found" #140

= 3.2.0 =
- Kompatibilität mit WordPress 5.8 verbessert
- Kompatibilität mit Hostern wie IONOS verbessert #135
- REST-API Routen verwenden die HTTP-Method GET
- Bugfix: Ein PHP Fehler wurde protokolliert, wenn das Plugin nicht korrekt konfiguriert wurde #137

= 3.1.3 =
- Bugfix: Ein Anzeigefehler im Plugin-Status Tab wurde behoben #130
- Alert nach Klick auf Google Analytics Opt-out Button hinzugefügt

= 3.1.2 =
- Probleme beim Speichern der Einstellungen wurden behoben #129
- PHP "headers already sent" Warnungen werden nicht mehr auf der Wp-Option Page angezeigt #124

= 3.1.1 =
- Probleme in Zusammenhang mit dem Plugin Filebird Pro behoben #128
- Updater auf Version 4.11 aktualisiert !98

= 3.1.0 =
- Kompatibilität mit WordPress 5.7 verbessert
- Kompatibilität mit PHP 8 getestet
- Feature: Darstellung des Datenquellen-Buttons optimiert #115

= 3.0.3 =
- Debuginformationen ließen sich zum Teil wegen einer zu langen URL nicht anzeigen #118
- Veraltetes jQuery entfernt #116
- Fehler in den Übersetzungen korrigiert #117

= 3.0.2 =
- Fehler im Zusammenhang mit dem Gutenberg Editor wurde behoben
- Das Update Script wurde angepasst
- Kompatibilität mit PHP 7.1 verbessert

= 3.0.1 =
- Performance des Status-Tabs verbessert
- Kompatibilität mit DIVI verbessert
- Cron-Fehler wurde behoben
- In bestimmten PHP-Versionen trat ein kritischer PHP Fehler auf
- Übersetzungen hinzugefügt
- Zeichencodierung UTF-8 für Rechtstexte
- Optimierung der Fehlerlog Funktion

= 3.0.0 =
- PHP 7.1 mindestens notwendig
- Neue Version des Plugins, die viele Fehler bei der Synchronisation der Rechtstexte behebt
- Composer Abhängigkeiten wurden minimiert
- API Schnittstelle zu eRecht24 Servern optimiert
- Schnittstellensicherheit verbessert
- Rest API ersetzt WP_Ajax
- WP Action und Filter Hooks eingeführt
- Verbessertes Error Handling
- Neues Logo

= 2.2.1 =
- Gelöst: Bei Verwendung des WYSIWYG-Editor Elementor werden die Rechtstexte nicht mehr ausgegeben #78
- Notice "register_rest_route wurde fehlerhaft aufgerufen" behoben

= 2.2.0 =
- Multisite-Tauglichkeit verbessert #63
- Notice: "Trying to access array offset on value of type bool" behoben #77
- Webseite wird vom eRecht24 Server abgemeldet, wenn der API-Schlüssel gelöscht wird
- Fehlermeldungen überarbeitet

= 2.1.1 =
- Im Tab "Google Analytics" werden Änderungen nicht mehr automatisch gespeichert, der Nutzer muss immer manuell auf "Änderungen speichern" klicken

= 2.1.0 =
- Google Analytics kann nun mit Usercentrics verwendet werden #40
- Warning behoben: jQuery Migrate Helper — Warnings encountered: wl-erecht24-admin.js: jQuery.fn.load() is deprecated #68
- Deprecation Warning behoben: WP_User->id wurde mit einem Parameter oder Argument aufgerufen, der seit Version 2.1.0 veraltet ist! #72
- Google Opt-Out Cookies werden nun in allen Browsern gespeichert #69
- Hinweis zu Problemen mit IONOS ausblenden, die Netzwerksprobleme sind behoben
- Fehlerbehebung: Call to undefined function GuzzleHttp\_idn_uri_convert() in /inc/libraries/guzzlehttp/guzzle/src/Client.php:220 #70
- Plugin Update Checker auf Version 4.10 aktualisiert #73

= 2.0.9 =
- Hinweis im Backend zu den aktuellen Probleme bei IONOS - https://www.e-recht24.de/mitglieder/tools/erecht24-rechtstexte-plugin/#probleme-wp
- Servererreichbarkeit wird bei deaktivierter php-Funktion fsockopen() falsch angezeigt #57
- Fehlermeldungen können mit den Debuginformationen dem Support übermittelt werden
- Timeout hinzugefügt: Auf Anfragen an eRecht24 max. 2 Sekunden warten

= 2.0.8 =
- Falsche URL zur REST-API von WordPress korrigiert #44
- Gekürzter API-Schlüssel, Adresse des API-Servers, dessen Erreichbarkeit und WordPress URL zur REST-API in Debuginformationen aufgenommen

= 2.0.7 =
- Falsche API-URL bei direkten Updates via WordPress entfernt. Behebt falsches Zurückweisen gültiger API-Schlüssel und Nichtverfügbarkeit von Texten, die im Projektmanager erstellt wurden.
- Behebung eines Fehlers, der bei deaktivierter Funktion cURL das Updaten von Texten verhindert hat.

= 2.0.6 =
- Behebung eines Fehlers, der das Updaten von Rechtstexten verhindert hat.

= 2.0.5 =
- Prüfung von Serveranforderungen des Plugins hinzugefügt.
- Statusübersichtsseite hinzugefügt.
- Debuginformationen für Supportanfragen werden erzeugt.
- Hintergrundfunktionalitäten für Push-Api aktualisiert.

= 2.0.4 =
- Plugin-Icon für die WordPress-Updateansicht hinzugefügt.
- Meldung zu nicht mehr unterstützten Shortcodes dauerhaft ausblendbar gemacht.
- Möglichkeit alle Rechtstexte auf einmal zu synchronisieren hinzugefügt.
- Möglichkeit synchronisierte Rechtstexte in die lokale Version zu übernehmen integriert.

= 2.0.3 =
- Script für die Erstellung der Übersetzungsdateien hinzugefügt.
- Anzeige des Datums von Aktualisierungen korrigiert.

= 2.0.2 =
- Übersetzungen für alle deutschen Dialekte hinzugefügt.
- Ungenutzte JavaScript und CSS-Dateien aus dem Frontend entfernt.
- Plugingröße stark reduziert.
- Aufruf von Editor-JavaScript aus dem Frontend entfernt.

= 2.0.1 =
- API-Schlüssel kann nun auch gespeichert werden, wenn es noch kein Impressum gibt.

= 2.0.0 =
- Neue Version des Plugins, die eine Synchronisation der Rechtstexte erlaubt.

== Upgrade Notice ==
= Hinweise zum Update von der Version 1.x.x auf 2.x.x =
Es handelt sich bei diesem Plugin um eine Neuentwicklung des eRecht24 Rechtstexte Plugins. Sofern zum Zeitpunkt der Installation dieses Plugins (Version 2.x.x) eine frühere Version des Plugins in der Version 1.x.x installiert war, wurden die Einstellungen in dieses Plugin übernommen und das bisherige Plugin deaktiviert.

*   Sie können das Plugin in der Version 1.x.x nun deinstallieren, ohne dass Daten verloren gehen.
*   Ersetzen Sie bitte die bisherigen Shortcodes `[impressum]` in Ihrer Impressumseite und `[datenschutz]` in Ihrer Datenschutzerklärungsseite durch die Shortcodes dieses Plugins. Mehr zu den Shortcodes finden Sie in der nachfolgenden Anleitung.

== Installation ==

1. Entpacken Sie das zip-Archiv
2. Laden Sie alle Dateien in das Verzeichnis `/wp-content/plugins/`
3. Aktivieren Sie das Plugin in WordPress im Menü `Plugins`

== Description ==
*Stets aktuelle Rechtstexte*  
Mit nur einem Klick können Impressum und Datenschutzerklärung von eRecht24 in Ihre Website übernommen werden.
Die Einbindung der Texte in die jeweilige Unterseite erfolgt bequem als Inhaltselement oder als Shortcode.

Eine detaillierte Anleitung finden Sie nach der Installation im WordPress unter:  
`Einstellungen > eRecht24 Rechtstexte > Dokumentation`

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

export default {
  menu: {
    start:        'Startseite',
    l10n:         'Sprache ändern',
    l10n_loading: 'Lade Sprachen...'
  },
  pages: {
    not_found: {
      title: 'Fehler 404',
      text:  'Diese Seite existiert nicht.'
    },
    hello: {
      head: 'Hallo Welt!'
    },
    portal: {
      head:           'Konto anlegen',
      create_account: {
        info_box: 'Bitte alle Felder ausfüllen. Danach wirst du eine Bestätigungsmail bekommen, um deinen Account aktivieren zu können.',
        form:     {
          username: 'Wähle einen Benutzernamen',
          password: 'Wähle ein Passwort',
          email:    'Wähle eine Email Adresse',
          button:   'Konto anlegen',
          language: 'Wähle eine Sprache'
        },
        suggestions: 'Die folgenden Optionen für deinen Benutzernamen wurden generiert:',
        success:     'Du hast erfolgreich einen Account angelegt. Du bekommst nun eine Email zugeschickt mit deren Hilfe du deinen Account aktivieren kannst.'
      },
      activate: {
        progress: 'Aktiviere den Account für',
        success:  'Aktivierung erfolgreich.',
        error:    'Aktivierung fehlgeschlagen. Ist die Aktivierung abgelaufen?',
        headline: 'Nutzeraktivierung'
      },
      login: {
        headline:  'Startseite',
        info_text: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
        form:      {
          username: 'Benutzername',
          password: 'Passwort',
          button:   'Einloggen'
        },
        panels: {
          login: 'Login',
          info:  'Über Sententiaregum'
        }
      }
    },
    network: {
      logout:    'Logout',
      dashboard: {
        index: {
          title: 'Neueste Nachrichten'
        }
      }
    }
  }
};

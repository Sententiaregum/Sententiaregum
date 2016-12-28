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

import {GET_LOCALES, CHANGE_LOCALE  } from '../constants/Locale';

/**
 * Action which is responsible for loading a language.
 *
 * All available languages are stored on server-side. This helps when validating locales that can be handled
 * by the entire system. If a locale is configured, but no translations (this system depends on that, too) are available,
 * `English` will be the default.
 *
 * @returns {void}
 */
export const loadLanguages = () => {

  // const loadLocales = () => axios.get('/api/locale.json').then(response => response.data);

  return({
    type: GET_LOCALES,
    locales: { "de": "Deutsch", "en": "English" }
  });
};

/**
 * Action which is responsible for changing a language.
 *
 * Languages are stored in a cookie, but a requirement of the application's core is that
 * a language is always available. So whenever a user's active with an account, an ajax request to change the user's locale
 * will be dispatched. To keep the entire application easy-to-use, a simple switcher might be used rather than
 * a huge formula.
 *
 * @param {String}   locale  The new locale.
 *
 * @returns {void}
 */
export const changeLocale = (locale) => ({
  type: CHANGE_LOCALE,
  locale: locale
});

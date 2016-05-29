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

import counterpart from 'counterpart';
import invariant from 'invariant';
import Cookies from 'cookies-js';

/**
 * Simple helper class which utilizes locale management and
 * connects the counterpart library with a http cookie library.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class LocaleService {
  /**
   * Constructor.
   *
   * @param {string} defaultLanguage Language whether to use if the language cookie is empty.
   *
   * @returns {void}
   */
  constructor(defaultLanguage) {
    this.defaultLanguage = defaultLanguage || 'en';
  }

  /**
   * Gets the current locale.
   *
   * @returns {string} Language value.
   */
  getLocale() {
    return Cookies.get('language') || this.defaultLanguage;
  }

  /**
   * Changes the locale.
   * If no locale is given, the default locale will be chosen.
   *
   * @param {string|null} locale The new locale.
   *
   * @returns {void}
   */
  setLocale(locale) {
    let newLocale;
    if (null === locale) {
      newLocale = this.getLocale();
    } else {
      const allowedLocales = ['de', 'en'];
      invariant(
        0 <= allowedLocales.indexOf(locale),
        '[LocaleService.setLocale(%s)] Invalid locale! Allowed locales are %s!',
        locale,
        allowedLocales.join(',')
      );

      newLocale = locale;
    }

    Cookies.set('language', newLocale);
    counterpart.setLocale(newLocale);
  }
}

export default new LocaleService('en');

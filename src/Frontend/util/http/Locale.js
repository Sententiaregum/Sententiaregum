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
import invariant   from 'invariant';
import Cookies     from 'cookies-js';

/**
 * Simple helper class which utilizes locale management and
 * connects the counterpart library with a http cookie library.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default new class Locale {
  /**
   * Constructor.
   *
   * @returns {void}
   */
  constructor() {
    this.defaultLanguage = 'en';
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
    const newLocale = this._getLocale(locale);

    Cookies.set('language', newLocale);
    counterpart.setLocale(newLocale);
  }

  /**
   * Simple helper to validate the locale.
   *
   * @param {String} locale The new locale.
   *
   * @returns {String} The validated value of the new locale.
   * @private
   */
  _getLocale(locale) {
    if (!locale) {
      return this.getLocale();
    }

    const allowedLocales = ['de', 'en'];
    invariant(
      0 <= allowedLocales.indexOf(locale),
      '[LocaleService.setLocale(%s)] Invalid locale! Allowed locales are %s!',
      locale,
      allowedLocales.join(',')
    );

    return locale;
  }
}();

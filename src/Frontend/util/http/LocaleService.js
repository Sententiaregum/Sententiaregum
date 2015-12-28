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

/**
 * Simple helper class which utilizes locale management and
 * connects the counterpart library with a http cookie library.
 *
 * NOTE: when testing this class, a jsdom instance will be created in order to
 * gather cookies through a faked window object.
 *
 * INTERNAL NOTE: this is just meant to be used inside the i18n system.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class LocaleService {
  /**
   * Constructor.
   *
   * @param {string} defaultLanguage Language whether to use if the language cookie is empty.
   * @param {Object} cookieFactory   Instance of a factory for the cookie handling.
   *
   * @returns {void}
   */
  constructor(defaultLanguage, cookieFactory) {
    this.defaultLanguage = defaultLanguage || 'en';
    this.cookieFactory   = cookieFactory;
  }

  /**
   * Gets the current locale.
   *
   * @returns {string} Language value.
   */
  getLocale() {
    return this.cookieFactory.getCookies().get('language') || this.defaultLanguage;
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
        '[LocaleService.setLocale(%s) Invalid locale! Allowed locales are %s!',
        locale,
        allowedLocales.join(',')
      );

      newLocale = locale;
    }

    this.cookieFactory.getCookies().set('language', newLocale);
    counterpart.setLocale(newLocale);
  }
}

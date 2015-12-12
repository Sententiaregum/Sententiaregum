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

import Cookies from 'cookies-js';
import CookieFactory from './CookieFactory';
import counterpart from 'counterpart';
import invariant from 'react/lib/invariant';

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
   * @param {string} defaultLanguage
   * @param {Object} cookieFactory
   */
  constructor(defaultLanguage, cookieFactory) {
    this.defaultLanguage = defaultLanguage || 'en';
    this.cookieFactory   = cookieFactory;
  }

  /**
   * Gets the current locale.
   *
   * @returns {string}
   */
  getLocale() {
    return this.cookieFactory.getCookies().get('language') || this.defaultLanguage;
  }

  /**
   * Changes the locale.
   * If no locale is given, the default locale will be chosen.
   *
   * @param {string|null} locale
   */
  setLocale(locale) {
    if (null === locale) {
      locale = this.getLocale();
    } else {
      const allowedLocales = ['de', 'en'];
      invariant(
        allowedLocales.indexOf(locale) >= 0,
        '[LocaleService.setLocale(%s) Invalid locale! Allowed locales are %s!',
        locale,
        allowedLocales.join(',')
      );
    }

    this.cookieFactory.getCookies().set('language', locale);
    counterpart.setLocale(locale);
  }
}

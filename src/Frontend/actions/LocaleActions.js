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

import AppDispatcher from '../dispatcher/AppDispatcher';
import LocaleConstants from '../constants/Locale';
import { Locale, ApiKey } from '../util/http/facade/HttpServices';
import $ from 'jquery';

/**
 * Action creator which dispatches actions for the locale switcher.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class LocaleActions {
  /**
   * Loads supported items and dispatch them to the store.
   *
   * @returns {void}
   */
  loadLanguages() {
    $.ajax({
      url:     '/api/locale.json',
      method:  'GET',
      success: (response) => {
        AppDispatcher.dispatch({
          event:  LocaleConstants.GET_LOCALES,
          result: response
        });
      }
    });
  }

  /**
   * Changes the locale.
   *
   * @param {string} locale The new locale.
   *
   * @returns {void}
   */
  changeLocale(locale) {
    Locale.setLocale(locale);

    if (ApiKey.isLoggedIn()) {
      const params = { locale };

      $.ajax({
        url:     '/api/protected/locale.json',
        method:  'PATCH',
        data:    params,
        headers: {
          'X-API-KEY': ApiKey.getApiKey()
        }
      });
    }
  }
}

export default new LocaleActions();

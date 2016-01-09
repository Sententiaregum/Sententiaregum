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

import axios from 'axios';
import { ApiKey } from '../http/facade/HttpServices';

/**
 * API utils for the locale api.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class LocaleWebAPIUtils {
  /**
   * Fetches all available locales.
   *
   * @param {Function} languageHandler Handler to be executed when the locales have been loaded.
   *
   * @returns {void}
   */
  getLocales(languageHandler) {
    axios.get('/api/locale.json')
      .then(response => {
        languageHandler.apply(this, [response.data]);
      });
  }

  /**
   * Changes the locale of a logged in user.
   *
   * @param {String} locale New user locale.
   *
   * @returns {void}
   */
  changeUserLocale(locale) {
    axios.patch(
      '/api/protected/locale.json',
      { locale },
      {
        headers: {
          'X-API-KEY': ApiKey.getApiKey()
        }
      }
    );
  }
}

export default new LocaleWebAPIUtils();

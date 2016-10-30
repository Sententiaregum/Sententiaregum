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

import { GET_LOCALES, CHANGE_LOCALE } from '../constants/Locale';
import axios from 'axios';
import userStore from '../store/userStore';
import Locale from '../util/http/Locale';
import ApiKey from '../util/http/ApiKey';

/**
 * Action which is responsible for all action related to the i18n management.
 *
 * @returns {Object} The action configuration.
 */
export default () => {
  /**
   * Action which is responsible for loading a language.
   *
   * All available languages are stored on server-side. This helps when validating locales that can be handled
   * by the entire system. If a locale is configured, but no translations (this system depends on that, too) are available,
   * `English` will be the default.
   *
   * @param {Function} publish The function to publish the fetched locales.
   *
   * @returns {void}
   */
  function loadLanguages(publish) {
    axios.get('/api/locale.json').then(response => publish(response.data));
  }

  /**
   * Action which is responsible for changing a language.
   *
   * Languages are stored in a cookie, but a requirement of the application's core is that
   * a language is always available. So whenever a user's active with an account, an ajax request to change the user's locale
   * will be dispatched. To keep the entire application easy-to-use, a simple switcher might be used rather than
   * a huge formula.
   *
   * @param {Function} publish The function to publish the fetched menu items.
   * @param {String}   locale  The new locale.
   *
   * @returns {void}
   */
  function changeLocale(publish, locale) {
    Locale.setLocale(locale);
    if (userStore.getStateValue('auth.authenticated')) {
      axios.patch('/api/protected/locale.json', { locale }, {
        headers: { 'X-API-KEY': ApiKey.getApiKey() }
      });
    }

    publish({ locale });
  }

  return {
    [CHANGE_LOCALE]: changeLocale,
    [GET_LOCALES]:   loadLanguages
  };
};

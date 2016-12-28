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

import { CHANGE_LOCALE  } from '../constants/Locale';
import axios              from 'axios';
import ApiKey             from '../util/http/ApiKey';

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
 * @returns {object}
 */
export const changeLocale = (locale) => {
  //TODO: Fix auth
  if (false) {
    axios.patch('/api/protected/locale.json', { locale }, {
      headers: { 'X-API-KEY': ApiKey.getApiKey() }
    });
  }
  return ({
    type: CHANGE_LOCALE,
    locale
  });
};

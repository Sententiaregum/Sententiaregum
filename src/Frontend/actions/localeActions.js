/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import { CHANGE_LOCALE  } from '../constants/Locale';
import axios              from 'axios';
import Locale             from '../util/http/Locale';

/**
 * Action which is responsible for changing a language.
 *
 * Languages are stored in a cookie, but a requirement of the application's core is that
 * a language is always available. So whenever a user's active with an account, an ajax request to change the user's locale
 * will be dispatched. To keep the entire application easy-to-use, a simple switcher might be used rather than
 * a huge formula.
 *
 * @param {String} locale  The new locale.
 *
 * @returns {Object} The payload for the reducers.
 */
export const changeLocale = (locale) => (dispatch, state) => {
  const { authenticated, appProfile } = state().user.security;
  if (authenticated) {
    axios.patch('/api/protected/locale.json', { locale }, {
      headers: { 'X-API-KEY': appProfile.apiKey }
    });
  }

  Locale.setLocale(locale);

  dispatch({
    type: CHANGE_LOCALE,
    locale
  });
};

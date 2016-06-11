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
import Locale from '../util/http/LocaleService';
import LocaleWebAPIUtils from '../util/api/LocaleWebAPIUtils';
import UserStore from '../store/UserStore';
import getStateValue from '../store/provider/getStateValue';

/**
 * Action creator for the language loader.
 *
 * @returns {Function} The language initialization action.
 */
export function loadLanguages() {
  return dispatch => {
    LocaleWebAPIUtils.getLocales(response => {
      dispatch(GET_LOCALES, { locales: response });
    });
  };
}

/**
 * Action creator for the locale change.
 *
 * @param {String} locale The new locale.
 *
 * @returns {Function} The action.
 */
export function changeLocale(locale) {
  return dispatch => {
    Locale.setLocale(locale);
    if (getStateValue(UserStore, 'is_logged_in', false)) {
      LocaleWebAPIUtils.changeUserLocale(locale);
    }

    dispatch(CHANGE_LOCALE, { locale });
  };
}
